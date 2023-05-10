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

class mapping extends Controller
{


    public function index0(Request $request)
    {
           




            
         if ($request->ajax()) {

               
                    if(!empty($request->profile))
                 
                  {

                 $tests = DB::table('ProfileTestMapping')->select('TestDefinitionID')->where('ProfileID',$request->profile)->get(); 
                 $testsList = array();
                 foreach($tests as $test) {

                    $testsList[] = $test->TestDefinitionID;
                 }  

                $data = DB::table('TestDefinitions') 
                         ->select('TestDefinitions.id',
                            'TestDefinitions.longname',
                            DB::raw('CONCAT(TestDefinitions.units, "ml") AS units'),
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
                                ->whereNotIn('TestDefinitions.id', $testsList);

                  }  else {

                     $data = DB::table('TestDefinitions') 
                         ->select('TestDefinitions.id',
                            'TestDefinitions.longname',
                            DB::raw('CONCAT(TestDefinitions.units, "ml") AS units'),
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
                                ->limit(0);


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

            
          return view ('mapping');
        
    }



    public function index(Request $request)
    {


             if((\App\Http\Controllers\users::roleCheck('Profile / Test Mapping','View',0)) == 'No')   
                    { return redirect('/home');}   

        
         if ($request->ajax()) {

                 if(!empty($request->profile))
                 
                  {


            $data = DB::table('ProfileTestMapping') 
                         ->select('ProfileTestMapping.ID',
                            'ProfileTestMapping.created_at',
                            'ProfileTestMapping.updated_at',
                            'testprofiles.name as ProfileID',
                            'TestDefinitions.longname as TestDefinitionID',
                            'TestDefinitions.shortname as shortname',
                            DB::raw('CONCAT(TestDefinitions.units, "ml") AS units'),
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ProfileTestMapping.ProfileID')
                         ->leftjoin('TestDefinitions', 'TestDefinitions.id', '=', 'ProfileTestMapping.TestDefinitionID')
                         ->leftjoin('users AS A', 'A.id', '=', 'ProfileTestMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ProfileTestMapping.updated_by')
                         ->where('ProfileTestMapping.ProfileID',$request->profile);

                   } else {


                      $data = DB::table('ProfileTestMapping') 
                         ->select('ProfileTestMapping.ID',
                            'ProfileTestMapping.created_at',
                            'ProfileTestMapping.updated_at',
                            'testprofiles.name as ProfileID',
                            'TestDefinitions.longname as TestDefinitionID',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ProfileTestMapping.ProfileID')
                         ->leftjoin('TestDefinitions', 'TestDefinitions.id', '=', 'ProfileTestMapping.TestDefinitionID')
                         ->leftjoin('users AS A', 'A.id', '=', 'ProfileTestMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ProfileTestMapping.updated_by')
                         ->limit(0);


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

        $profiles = DB::table('testprofiles')->select('id','name')->where('InUse',1)->orderBy('name')->get();

          $data = [
            'profiles' => $profiles
          ];  
            
          return view ('mapping')->with('data',$data);
        
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

     $log = DB::table('ProfileTestMapping')->select('ProfileID','TestDefinitionID')->where('ID',$id)->get();
     $Profile = DB::table('testprofiles')->select('name')->where('id',$log[0]->ProfileID)->get();
     $Test = DB::table('TestDefinitions')->select('longname')->where('id',$log[0]->TestDefinitionID)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Mapping', 'Test "'.$Test[0]->longname.'" removed from the Profile "'.$Profile[0]->name.'". ']); 

     return DB::table('ProfileTestMapping')->where('ID', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('ProfileTestMapping')->max('ID')+1;
        $profile = $request->input('profile');
        $test = $request->input('test');


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'profile' => 'required|unique:ProfileTestMapping,ProfileID,NULL,id,TestDefinitionID,'.$test,
            'test' => 'required'
        ]);
     

         if ($validator->passes()) {


        DB::insert('insert into ProfileTestMapping (ID, ProfileID, TestDefinitionID, created_at, created_by) values (?, ?, ?, ?, ?)', 
            [$id, $profile, $test, date('Y-m-d H:i:s'), $user['id'] ] );  


         $Profile = DB::table('testprofiles')->select('name')->where('id',$profile)->get();
         $Test = DB::table('TestDefinitions')->select('longname')->where('id',$test)->get();

         $controller = App::make('\App\Http\Controllers\activitylogs');
         $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Mapping', 'Test "'.$Test[0]->longname.'" added to the Profile "'.$Profile[0]->name.'". ']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }





}