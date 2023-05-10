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

class reflextestmapping extends Controller
{


    public function index0(Request $request)
    {




            
         if ($request->ajax()) {

               
                    if(!empty($request->test))
                 
                  {

                 $tests = DB::table('ReflexTestMapping')->select('TestDefinitionID2')->where('TestDefinitionID1',$request->test)->get(); 
                 $testsList = array();
                 foreach($tests as $test) {

                    $testsList[] = $test->TestDefinitionID2;
                 }  


                $data = DB::table('TestDefinitions') 
                         ->select('TestDefinitions.id',
                            'TestDefinitions.longname',
                            'TestDefinitions.shortname',
                            'TestDefinitions.InUse',
                            'TestDefinitions.created_at', 
                            'TestDefinitions.updated_at',
                            'Lists.Text as SampleType',
                            'facilities.name as Hospital',
                            'A.name as created_by',
                            'B.name as updated_by')
                                ->leftjoin('Lists', 'Lists.id', '=', 'TestDefinitions.SampleType')
                                ->leftjoin('facilities', 'facilities.id', '=', 'TestDefinitions.Hospital')
                                ->leftjoin('users AS A', 'A.id', '=', 'TestDefinitions.created_by')
                                ->leftjoin('users AS B', 'B.id', '=', 'TestDefinitions.updated_by')
                                ->where('TestDefinitions.InUse', 1)
                                ->where('TestDefinitions.id', '!=', $request->test)
                                ->whereNotIn('TestDefinitions.id', $testsList);

                  }  else {

                     $data = [];


                  }            
            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <button type="button" id="'.$row->id.'"  title="Add" class="add btn btn-warning"><i class="bx bx-plus"></i>
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

            
          return view ('reflextestmapping');
        
    }



    public function index(Request $request)
    {

        
        if((\App\Http\Controllers\users::roleCheck('Reflex Testing','View',0)) == 'No')   
                    { return redirect('/home');}   
                
         if ($request->ajax()) {

                 if(!empty($request->test))
                 
                  {


            $data = DB::table('ReflexTestMapping') 
                         ->select('ReflexTestMapping.ID',
                            'ReflexTestMapping.created_at',
                            'ReflexTestMapping.updated_at',
                            'TestDefinitions.longname as TestDefinitionID',
                            'TestDefinitions.id',
                            'TestDefinitions.longname',
                            'TestDefinitions.shortname',
                            'TestDefinitions.InUse',
                            'Lists.Text as SampleType',
                            'facilities.name as Hospital',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('Lists', 'Lists.id', '=', 'ReflexTestMapping.SampleType')
                         ->leftjoin('facilities', 'facilities.id', '=', 'ReflexTestMapping.Hospital')
                         ->leftjoin('TestDefinitions', 'TestDefinitions.id', '=', 'ReflexTestMapping.TestDefinitionID2')
                         ->leftjoin('users AS A', 'A.id', '=', 'ReflexTestMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ReflexTestMapping.updated_by')
                         ->where('ReflexTestMapping.TestDefinitionID1',$request->test);

                   } else {


                      $data = [];


                   }
                         
            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <button type="button" id="'.$row->ID.'" title="Delete" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 

                    ->editColumn('created_at', function($data){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d M Y H:i a'); return $created_at; })
                   ->editColumn('updated_at', function($data){ 
                        if($data->updated_at != '') {

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at;
                            
                        }
                     })

                    ->setRowId('ID')
                    ->rawColumns(['action'])
                    ->make(true);

                    
                  
        }

        $tests = DB::table('TestDefinitions')->select('id','longname as name')->where('InUse',1)->orderBy('name')->get();

          $data = [
            'tests' => $tests
          ];  
            
          return view ('reflextestmapping')->with('data',$data);
        
    }


     public function Map(Request $request)
    {

        if($request->id != '') {

          $data = DB::table('ProfileTestMapping')->where('ID', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id');   

     $log = DB::table('ReflexTestMapping')->select('TestDefinitionID1','TestDefinitionID2')->where('ID',$id)->get();
     $TestDefinitionID1 = DB::table('TestDefinitions')->select('longname')->where('id',$log[0]->TestDefinitionID1)->get();
     $TestDefinitionID2 = DB::table('TestDefinitions')->select('longname')->where('id',$log[0]->TestDefinitionID2)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Reflex Testing', 'Test "'.$TestDefinitionID2[0]->longname.'" removed as reflex test from Test "'.$TestDefinitionID1[0]->longname.'". ']); 


     return DB::table('ReflexTestMapping')->where('ID', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('ReflexTestMapping')->max('ID')+1;

        $test1 = $request->input('test1');
        $test2 = $request->input('test2');


        $data = DB::table('TestDefinitions')->select('SampleType','Hospital')->where('ID', $test2)->get();
        $SampleType = $data[0]->SampleType;
        $Hospital = $data[0]->Hospital;


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'test1' => 'required|unique:ReflexTestMapping,TestDefinitionID1,NULL,id,TestDefinitionID2,'.$test1,
            'test2' => 'required'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into ReflexTestMapping (ID, TestDefinitionID1, TestDefinitionID2, Hospital, SampleType, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
            [$id, $test1, $test2, $Hospital, $SampleType, date('Y-m-d H:i:s'), $user['id'] ] );  


         $TestDefinitionID1 = DB::table('TestDefinitions')->select('longname')->where('id',$test1)->get();
         $TestDefinitionID2 = DB::table('TestDefinitions')->select('longname')->where('id',$test2)->get();

         $controller = App::make('\App\Http\Controllers\activitylogs');
         $data = $controller->callAction('addLogs', [0,0,0,0,0,'Reflex Testing', 'Test "'.$TestDefinitionID2[0]->longname.'" added as reflex test to Test "'.$TestDefinitionID1[0]->longname.'". ']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }






}