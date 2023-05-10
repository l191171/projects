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

class mappingpanels extends Controller
{


    public function index0(Request $request)
    {


            
         if ($request->ajax()) {

               
                    if(!empty($request->panel))
                 
                  {

                 $profiles = DB::table('PanelsMapping')->select('ProfileID')->where('PanelD',$request->panel)->get(); 
                 $profilesList = array();
                 foreach($profiles as $profile) {

                    $profilesList[] = $profile->ProfileID;
                 }  

                $data = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                                    'testprofiles.name',
                                    'Lists.Text as department')
                                ->leftjoin('Lists', 'Lists.id', '=', 'testprofiles.department')
                                ->where('testprofiles.InUse', 1)
                                ->whereNotIn('testprofiles.id', $profilesList);

                  }  else {

                     $data = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                                    'testprofiles.name',
                                    'Lists.Text as department')
                                ->leftjoin('Lists', 'Lists.id', '=', 'testprofiles.department')
                                ->where('testprofiles.InUse', 1)
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


                    ->setRowId('id')
                    ->rawColumns(['action','InUse'])
                    ->make(true);
                    
                  
        }

            
          return view ('mappingpanels');
        
    }



    public function index(Request $request)
    {

              
        if((\App\Http\Controllers\users::roleCheck('Panels Mapping','View',0)) == 'No')   
                    { return redirect('/home');}   
        
         if ($request->ajax()) {

                 if(!empty($request->panel))
                 
                  {


            $data = DB::table('PanelsMapping') 
                         ->select('PanelsMapping.ID',
                            'PanelsMapping.created_at',
                            'PanelsMapping.updated_at',
                            'Panels.name as PanelD',
                            'testprofiles.name as ProfileID',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('Panels', 'Panels.id', '=', 'PanelsMapping.PanelD')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'PanelsMapping.ProfileID')
                         ->leftjoin('users AS A', 'A.id', '=', 'PanelsMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'PanelsMapping.updated_by')
                         ->where('PanelsMapping.PanelD',$request->panel);

                   } else {


                      $data = DB::table('PanelsMapping') 
                         ->select('PanelsMapping.ID',
                            'PanelsMapping.created_at',
                            'PanelsMapping.updated_at',
                            'Panels.name as PanelD',
                            'testprofiles.name as ProfileID',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('Panels', 'Panels.id', '=', 'PanelsMapping.PanelD')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'PanelsMapping.ProfileID')
                         ->leftjoin('users AS A', 'A.id', '=', 'PanelsMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'PanelsMapping.updated_by')
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

        $Panels = DB::table('Panels')->select('id','name')->where('InUse',1)->orderBy('name')->get();

          $data = [
            'Panels' => $Panels
          ];  
            
          return view ('mappingpanels')->with('data',$data);
        
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

     $log = DB::table('PanelsMapping')->select('PanelD','ProfileID')->where('ID',$id)->get();
     $Panel = DB::table('Panels')->select('name')->where('id',$log[0]->PanelD)->get();
     $Profile = DB::table('testprofiles')->select('name')->where('id',$log[0]->ProfileID)->get();
     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Panel Mapping', 'Profile "'.$Profile[0]->name.'" removed from the Panel "'.$Panel[0]->name.'". ']); 

     return DB::table('PanelsMapping')->where('ID', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('PanelsMapping')->max('ID')+1;
        $profile = $request->input('profile');
        $panel = $request->input('panel');


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'panel' => 'required|unique:PanelsMapping,PanelD,NULL,id,ProfileID,'.$profile,
            'profile' => 'required'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into PanelsMapping (ID, PanelD, ProfileID, created_at, created_by) values (?, ?, ?, ?, ?)', 
            [$id, $panel, $profile, date('Y-m-d H:i:s'), $user['id'] ] );  


     $Panel = DB::table('Panels')->select('name')->where('id',$panel)->get();
     $Profile = DB::table('testprofiles')->select('name')->where('id',$profile)->get();
     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Panel Mapping', 'Profile "'.$Profile[0]->name.'" added to the Panel "'.$Panel[0]->name.'". ']); 



            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }



}