<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;  
use Mail;
use PDF;
use App;

class medicaltest extends Controller
{


   public function index(Request $request)
    {

        
        if((\App\Http\Controllers\users::roleCheck('Tests','View',0)) == 'No')   
                    { return redirect('/home');}   

         if ($request->ajax()) {
   
            $data = DB::table('TestDefinitions') 
                         ->select('TestDefinitions.id',
                            'TestDefinitions.longname',
                             DB::raw('CONCAT(TestDefinitions.units, "ml") AS units'),
                            'C.name as adultsContainer',
                            'D.name as childrenContainer',
                            'TestDefinitions.shortname',
                            'TestDefinitions.InUse',
                            'TestDefinitions.created_at', 
                            'TestDefinitions.updated_at',
                            'Lists.Text as SampleType',
                            'facilities.name as Hospital',
                            'A.name as created_by',
                            'B.name as updated_by')
                                ->leftjoin('containers as C', 'C.id', '=', 'TestDefinitions.adultsContainer')
                                ->leftjoin('containers as D', 'D.id', '=', 'TestDefinitions.childrenContainer')
                                ->leftjoin('Lists', 'Lists.id', '=', 'TestDefinitions.SampleType')
                                ->leftjoin('facilities', 'facilities.id', '=', 'TestDefinitions.Hospital')
                                ->leftjoin('users AS A', 'A.id', '=', 'TestDefinitions.created_by')
                                ->leftjoin('users AS B', 'B.id', '=', 'TestDefinitions.updated_by');


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <button id="'.$row->id.'" title="Edit" class="btn btn-primary update">
                                 <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" title="Delete" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 
                    ->editColumn('InUse', function($data){ 

                        if($data->InUse == 1) {
                            return '<button type="button" class="btn btn-success">Yes
                                </button>';
                        } else {
                            return '<button type="button" class="btn btn-danger">No
                                </button>';
                        }
                    })
                    ->editColumn('created_at', function($data){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d M Y H:i a'); return $created_at; })
                    
                    ->editColumn('updated_at', function($data){ 
                        if($data->updated_at != '') {

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at;
                            
                        }
                     })

                    ->setRowId('id')
                    ->rawColumns(['action','InUse'])
                    ->make(true);

                    
                  
        }

  $sampleTypes = DB::table('Lists')->select('id','Text','Default')->where('ListType','STP')->where('InUse',1)->orderBy('ListOrder')->get();
  $facilities = DB::table('facilities')->select('id','name')->where('status',1)->orderBy('name')->get();
  $containers = DB::table('containers')->select('id','name')->where('status',1)->orderBy('name')->get();

          $data = [
            'sampleTypes' => $sampleTypes,
            'containers' => $containers,
            'facilities' => $facilities
          ];  
            
          return view ('medicaltests')->with('data',$data);
        
    }


     public function Test(Request $request)
    {



        if($request->id != '') {

          $data = DB::table('TestDefinitions')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    } 



    public function syncCodes(Request $request)
    {



          $data = DB::table('TestDefinitions')->select('nacode','shortname')->where('nacode', '!=', null)->where('nacode', '!=', '')->get();


            $connectionInfo_hq = array("Database"=>"CavanTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
            $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);

                    if( $conn_hq ) {

                              foreach ($data as $value) {
                                
  

                                 $tsql = "SELECT * FROM ocmMapping where SourceValue = '$value->shortname' ";
                                 $getlist = sqlsrv_query($conn_hq, $tsql);
                                  $row = sqlsrv_fetch_array($getlist, SQLSRV_FETCH_ASSOC);

                                       if(empty($row) == 1) {



                                            $sql1119 = "insert into ocmMapping (MappingType, TargetHospital,SourceValue, TargetValue) values ('TestCode', 'Cavan', '$value->shortname', '$value->nacode')";
                                        sqlsrv_query($conn_hq, $sql1119);
                               
                                            


                                       } else {

                                         $sql1119 = "update ocmMapping set TargetValue = '$value->nacode' where SourceValue = '".$row['SourceValue']."'";
                                        sqlsrv_query($conn_hq, $sql1119);
                           


                                       }




                              }

                              return 1;
             
                    }                              
          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id');   

     $log = DB::table('TestDefinitions')->select('longname')->where('id',$id)->get();
     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Test', 'Test "'.$log[0]->longname.'" Deleted. ']); 

     DB::table('TestDefinitions')->where('id', $id)->delete(); 
     DB::table('profiletestmapping')->where('TestDefinitionID', $id)->delete(); 
    }



     public function add(Request $request)
    {

        $id = DB::table('TestDefinitions')->max('id')+1;
        $name = $request->input('name');
        $code = $request->input('code');
        $nacode = $request->input('nacode');
        $type = $request->input('type');
        $units = $request->input('units');
        $facility = $request->input('facility');
        $adults = $request->input('adults');
        $children = $request->input('children');
        $sampleage = $request->input('sampleage');
        $status = $request->input('status');


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'nacode' => 'required',
            'name' => 'required|unique:TestDefinitions,longname',      
            'code' => 'required',
            'type' => 'required',
            'units' => 'required',
            'facility' => 'required',
            'adults' => 'required',
            'children' => 'required',
            'status' => 'required'
        ]);
     

             if ($validator->passes()) {


                if($sampleage == '') {

                    $sampleage = 0;
                }

        DB::insert('insert into TestDefinitions (id, longname, shortname, nacode, SampleType, units,  adultsContainer, childrenContainer, sampleage,  Hospital, InUse, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $name, $code, $nacode, $type, $units, $adults, $children, $sampleage, $facility, $status, date('Y-m-d H:i:s'), $user['id'] ] );  


         $controller = App::make('\App\Http\Controllers\activitylogs');
         $data = $controller->callAction('addLogs', [0,0,0,0,0,'Test', 'New Test "'.$name.'" Added. ']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {   

        $id = $request->input('id');   
        $name = $request->input('name');
        $code = $request->input('code');
        $nacode = $request->input('nacode');
        $type = $request->input('type');
        $units = $request->input('units');
        $facility = $request->input('facility');
        $adults = $request->input('adults');
        $children = $request->input('children');
        $sampleage = $request->input('sampleage');
        $status = $request->input('status');


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'nacode' => 'required',
            'name' => 'required|unique:TestDefinitions,longname,'.$id,      
            'code' => 'required',
            'type' => 'required',
            'units' => 'required',
            'facility' => 'required',
            'adults' => 'required',
            'children' => 'required',
            'status' => 'required'
        ]);


        if ($validator->passes()) {

             if($sampleage == '') {

                    $sampleage = 0;
                }
            
            DB::update("
            update TestDefinitions 
            set 
            longname = '$name', shortname = '$code', nacode = '$nacode', SampleType = '$type', units= '$units', adultsContainer = '$adults', childrenContainer = '$children', sampleage = '$sampleage', Hospital = '$facility', InUse = $status, updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where id =  $id 
            ");

            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Test', 'Test "'.$name.'" Updated. ']); 
            
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}