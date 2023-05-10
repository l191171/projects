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

class userrolesmapping extends Controller
{



    public function index(Request $request)
    {

            if((\App\Http\Controllers\users::roleCheck('User Roles Mapping','View',0)) == 'No')  {

                 return redirect('/home');
             }   



              
         if ($request->ajax()) {


              
                if(!empty($request->role))
                 
                  { 
                    
                    $data = DB::table('modules') 
                         ->select(
                             DB::raw($request->role.' as role'),
                            'modules.ID',
                            'modules.name',
                            'modules.permissions',
                            'modules.report',
                            'modules.created_at',
                            'modules.updated_at',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'modules.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'modules.updated_by')
                         ->whereNotIn('modules.ID',[19,45])
                         ->limit(500);


                  }

                  
                    else {


                      $data = DB::table('modules') 
                         ->select(
                            'modules.ID',
                            'modules.name',
                            'modules.permissions',
                            'modules.report',
                            'modules.created_at',
                            'modules.updated_at',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'modules.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'modules.updated_by')
                         ->whereNotIn('modules.ID',[19,45])
                         ->limit(0);


                     }

                         
            return Datatables::of($data)

                    ->addIndexColumn()


                    ->editColumn('name', function($data){

                            if($data->report == 1) {

                                return 'Reports ( '.$data->name.' )';
                            } 
                            else {

                                return $data->name;
                            }

                     })

                    ->editColumn('permissions', function($data){ 

                            $permissions = explode(',',$data->permissions);
                            $checked = '';
                     

                        $btns = '<div class="custom-control custom-checkbox"><input class="custom-control-input allCheck" type="checkbox" id="'.$data->ID.'-All'.'" value="All" ><label for="'.$data->ID.'-All'.'" class="custom-control-label">All</label></div>&nbsp;&nbsp;&nbsp;';

                        foreach($permissions as $permission) {

                            $btns .= '
                            <input type="hidden" name="id[]" value="'.$data->ID.'"  />
                            <input type="hidden" name="permissions[]" value="'.$permission.'"  />';

                            $value = DB::table('rolesPermissions')
                                                                ->select('value')
                                                                ->where('role',$data->role)
                                                                ->where('module',$data->ID)
                                                                ->where('permissions',$permission)
                                                                ->get();
                           
                           if(count($value) > 0) {

                           if($value[0]->value == 'Yes') {

    
                                $checked = 'checked';
                                $btns .= '<input type="hidden" name="permission[]" value="Yes" id="permission-'.$data->ID.$permission.'"  />';

                                } else {
                                
                                $checked = '';
                                $btns .= '<input type="hidden" name="permission[]" value="No" id="permission-'.$data->ID.$permission.'"  />';
                                
                                }

                                $btns .= '<input type="hidden" name="status[]" value="OLD"  />';

                                } else {

                                $checked = '';

                                 $btns .= '<input type="hidden" name="status[]" value="NEW"  />';
                                 $btns .= '<input type="hidden" name="permission[]" value="No" id="permission-'.$data->ID.$permission.'"  />';

                                }

                            $btns .= '<div class="custom-control custom-checkbox">
                            <input '.$checked.' class="custom-control-input singleCheck"  type="checkbox" id="'.$data->ID.$permission.'" ><label for="'.$data->ID.$permission.'" class="custom-control-label">'.$permission.'</label></div>';
                        }

                        return $btns;

                             })  

                    ->editColumn('created_at', function($data){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d M Y H:i a'); return $created_at; })
                   ->editColumn('updated_at', function($data){ 
                        if($data->updated_at != '') {

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at;
                            
                        }
                     })

                    ->setRowId('ID')
                    ->rawColumns(['permissions'])
                    ->make(true);

                    
                  
        }

        $roles = DB::table('Lists')->select('id','Text as name')->where('InUse',1)->where('ListType','ROL')->orderBy('Text')->get();

          $data = [
            'roles' => $roles
          ];  
            
          return view ('usersrolesmapping')->with('data',$data);
        
    }




    



          public function savePermissions(Request $request)
            {
                 $role = $request->input('role');
                 $sync = $request->input('sync');
                 
                 $user = auth()->user();

               if(count($request->input('id')) > 0 ) {  


                foreach($request->input('id') as $key =>  $id) {

                    $permissions = $request->input('permissions')[$key];
                    $value = $request->input('permission')[$key];
                    $status = $request->input('status')[$key];

                     $modules = DB::table('modules')->select('name','report')->where('ID', $id)->get();
                     $moduleName = $modules[0]->name;
                     $moduleReport = $modules[0]->report; 

                     if($status == 'NEW') {

                     

                     $ids = DB::table('rolesPermissions')->max('ID')+1;
                     
                     if($moduleReport == 1) {

                             DB::insert('insert into rolesPermissions (ID, role, module, moduleName, permissions, value, created_at, created_by, report) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                             [$ids, $role, $id, $moduleName, $permissions, $value, date('Y-m-d H:i:s'), $user['id'], 1 ] ); 

                     }  
                     else {

                             DB::insert('insert into rolesPermissions (ID, role, module, moduleName, permissions, value, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?)', 
                             [$ids, $role, $id, $moduleName, $permissions, $value, date('Y-m-d H:i:s'), $user['id'] ] ); 

                     }

                     
                    

                      
                     } else {

                      
                        DB::update("
                            update rolesPermissions 
                            
                            set 
                            
                            value = '$value', 
                            updated_at = '".date('Y-m-d H:i:s')."',  
                            updated_by = '".$user['id']."', 
                            report = '".$moduleReport."'

                            where 

                            role = '$role' and 
                            module =  '$id' and 
                            permissions =  '$permissions' 
                            ");
                     }
                      


                    }

                       return response()->json(['success'=>'Data saved.']);

                } 
                         
            }   




}