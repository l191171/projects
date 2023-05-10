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

class profiles extends Controller
{


    public function index(Request $request)
    {
                  
                    if((\App\Http\Controllers\users::roleCheck('Profiles','View',0)) == 'No')   
                    { return redirect('/home');}  


            $user = auth()->user();
            $userID = $user['id'];

    

             $user = auth()->user();
            $userID = $user['id'];
            $ids = DB::table('quicktestprofiles')->where('userID',$userID)->pluck('profileID'); 


            if(count($ids) > 0) {

               $ids = implode(",",json_decode($ids));

            }    else {

                 $ids = '';

            } 



         if ($request->ajax()) {




            $data = DB::table('testprofiles') 
                         ->select(

                            'testprofiles.id',
                            'testprofiles.name',
                            'C.Text as department',
                            'D.Text as specialhandling',
                            'testprofiles.diagnostics',
                            'testprofiles.InUse',
                            'testprofiles.created_at',
                            'testprofiles.updated_at',
                            'A.name as created_by',
                            'B.name as updated_by',
                            'quicktestprofiles.profileID as fvrt'

                            )
                          ->orderByRaw("FIELD(testprofiles.id , $ids) desc")

                         ->leftjoin('users AS A', 'A.id', '=', 'testprofiles.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'testprofiles.updated_by')
                         ->leftjoin('Lists AS C', 'C.id', '=', 'testprofiles.department')
                         ->leftjoin('Lists AS D', 'D.id', '=', 'testprofiles.specialhandling')
                         ->leftJoin('quicktestprofiles', function($join) use ($userID)
                         {
                             $join->on('quicktestprofiles.profileID', '=', 'testprofiles.id');
                             $join->on('quicktestprofiles.userID','=',DB::raw("'".$userID."'"));
                         });
                         


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
                    ->editColumn('fvrt', function($data){ 

                        if($data->fvrt != '') {

                            return '<button data="'.$data->id.'" data2="'.$data->fvrt.'" type="button" class="btn btn-success fvrt" title="Remove from Quick List">
                                <i class="fas fa-star"></i>
                                </button>';
                        } else {

                            return '<button data="'.$data->id.'" data2="'.$data->fvrt.'" type="button" class="btn btn-secondary fvrt" title="Add to Quick List">
                                <i class="fas fa-star"></i>
                                </button>';
                        }
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
                    ->rawColumns(['action','InUse','fvrt'])
                    ->make(true);

                    
                  
        }

        $Lists = DB::table('Lists')->select('id','text','Default')->where('ListType','DPT')->where('InUse',1)->orderBy('ListOrder')->get();
        $SHL = DB::table('Lists')->select('id','text','Default')->where('ListType','SHL')->where('InUse',1)->orderBy('ListOrder')->get();
        $DGN = DB::table('Lists')->select('id','text','Default')->where('ListType','DGN')->where('InUse',1)->orderBy('ListOrder')->get();

          $data = [
            'Lists' => $Lists,
            'SHL' => $SHL,
            'DGN' => $DGN
          ];  
            
          return view ('profiles')->with('data',$data);
        
    }


     public function Profile(Request $request)
    {



        if($request->id != '') {

          $data = DB::table('testprofiles')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    } 


    public function addProfileTOQuickList(Request $request)
    {


        if($request->id != '' && $request->fvrt == '') {

           $user = auth()->user();

            $id = DB::table('quicktestprofiles')->max('id')+1; 

            $userIDs = DB::table('quicktestprofiles')->select('userID')->where('userID',$user['id'])->get();  

            if(count($userIDs) < 10) { 

                  DB::insert('insert into quicktestprofiles 
                (
                    id,
                    profileID,
                    userID,
                    created_at,
                    created_by
                    
                ) 
                values (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                    )', 
                [
                    $id,
                    $request->id,
                    $user['id'],
                    date('Y-m-d H:i:s'),
                    $user['id']
                ]);

              $log = DB::table('testprofiles')->select('name')->where('id',$request->id)->get();
             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile', 'Profile "'.$log[0]->name.'" added to quick list. ']); 

              return response()->json(['success'=> 'Profile added.']);
              
            }  else {

                   return response()->json(['error'=> '10 Profiles already added.']);
            } 
            
           
        } 
             

          
    } 


     public function RemoveProfileFromQuickList(Request $request)
    {


        if($request->id != '' && $request->fvrt != '') {

           $user = auth()->user();

            DB::table('quicktestprofiles')->where('profileID', $request->id)->where('userID', $user['id'])->delete(); 
             $log = DB::table('testprofiles')->select('name')->where('id',$request->id)->get();
             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile', 'Profile "'.$log[0]->name.'" removed from quick list. ']); 

        } 
             

          
    } 

 


    public function delete(Request $request)
    {
     $id = $request->input('id'); 


     $log = DB::table('testprofiles')->select('name')->where('id',$id)->get();
     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile', 'Profile "'.$log[0]->name.'" Deleted.']); 


     DB::table('testprofiles')->where('id', $id)->delete(); 
     DB::table('ProfileQuestionMapping')->where('ProfileID', $id)->delete(); 
     DB::table('ProfileTestMapping')->where('ProfileID', $id)->delete(); 


    }



     public function add(Request $request)
    {
        $id = DB::table('testprofiles')->max('id')+1;
        $level = $request->input('level');
        $name = $request->input('name');
        $department = $request->input('department');
        $specialhandling = $request->input('specialhandling');
        $diagnostics = $request->input('diagnostics');
        $dppType = $request->input('dppType');
        $dpp = $request->input('dpp');
        $rcf = $request->input('rcf');


        if(!empty($diagnostics)) {

           $diagnostics = implode(',',$diagnostics);
        }
        
        $InUse = $request->input('InUse');
        $btcheck = $request->input('btcheck');
        $mandatory = $request->input('mandatory');
        

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'level' => 'required',
            'name' => 'required|unique:testprofiles,name',      
            'department' => 'required',
            'InUse' => 'required',
            'dppType' => 'required',
            'rcf' => 'required',
            'btcheck' => 'required',
            'mandatory' => 'required'

        ]);
     

             if ($validator->passes()) {


                if($dppType == 'Hours') {

                    $dppHours = $dpp;
                }

                if($dppType == 'Days') {

                    $dppHours = $dpp*24;
                }

                if($dppType == 'Months') {

                    $dppHours = $dpp*24*30;
                }
 

        DB::insert('insert into testprofiles (id, level, btcheck, mandatory, name, specialhandling, diagnostics, department, dppType, dpp, dppHours, InUse, rcf, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $level, $btcheck, $mandatory, $name, $specialhandling, $diagnostics, $department, $dppType, $dpp, $dppHours, $InUse, $rcf, date('Y-m-d H:i:s'), $user['id'] ] );  

             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile', 'New Profile "'.$name.'" Added.']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $level = $request->input('level');  
        $name = $request->input('name');         
        $department = $request->input('department');
        $specialhandling = $request->input('specialhandling');
        $InUse = $request->input('InUse');
        $diagnostics = $request->input('diagnostics');
        $dppType = $request->input('dppType');
        $dpp = $request->input('dpp');
        $rcf = $request->input('rcf');
        $btcheck = $request->input('btcheck');
        $mandatory = $request->input('mandatory');

        if(!empty($diagnostics)) {

           $diagnostics = implode(',',$diagnostics);
        }

         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'level' => 'required',
            'name' => 'required|unique:testprofiles,name,'.$id,     
            'department' => 'required',
            'InUse' => 'required',
            'dppType' => 'required',
            'rcf' => 'required',
            'btcheck' => 'required',
            'mandatory' => 'required'
        ]);

        if ($validator->passes()) {

              if($dppType == 'Hours') {

                    $dppHours = $dpp;
                }

                if($dppType == 'Days') {

                    $dppHours = $dpp*24;
                }

                if($dppType == 'Months') {

                    $dppHours = $dpp*24*30;
                }


            DB::update("
            
            update testprofiles 
            set 
            level = '$level', 
            name = '$name', 
            department = '$department', 
            specialhandling = '$specialhandling', 
            diagnostics = '".$diagnostics."', 
            dppType = '$dppType',
            dpp = '$dpp',
            dppHours = '$dppHours',
            rcf = '$rcf',
            btcheck = '$btcheck', 
            mandatory = '$mandatory', 
            InUse = '$InUse', updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where id =  '$id' 
            
            ");


             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile', 'Profile "'.$name.'" Updated.']); 
            
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}