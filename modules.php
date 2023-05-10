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

class modules extends Controller
{


    public function index(Request $request)
    {

        
         if ($request->ajax()) {
   
            $data = DB::table('modules') 
                         ->select('modules.ID',
                            'modules.created_at',
                            'modules.updated_at',
                            'modules.name',
                            'modules.permissions',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'modules.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'modules.updated_by');


            return Datatables::of($data)    

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <button id="'.$row->ID.'" title="Edit" class="btn btn-primary update">
                                 <i class="bx bx-edit"></i>
                                </button>
                                <button type="button" title="Delete" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
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

            
          return view ('modules');
        
    }


     public function Module(Request $request)
    {

        if($request->id != '') {

          $data = DB::table('modules')->where('ID', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id');   
     DB::table('modules')->where('id', $id)->delete(); 
     DB::table('rolesPermissions')->where('module', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('modules')->max('ID')+1;
        $name = $request->input('name');
        $permissions = $request->input('permissions');

        if(!empty($permissions)) {

           $permissions = implode(',',$permissions);
        }

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'name' => 'required|unique:modules,name'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into modules (ID, name, permissions, created_at, created_by) values (?, ?, ?, ?, ?)', 
            [$id, $name, $permissions, date('Y-m-d H:i:s'), $user['id'] ] );  


            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $name = $request->input('name');
        $permissions = $request->input('permissions');

        if(!empty($permissions)) {

           $permissions = implode(',',$permissions);
        }

         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:modules,name,'.$id
        ]);

        if ($validator->passes()) {

            
            DB::update("
            update modules 
            set 
            name = '$name', permissions = '".$permissions."', updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where ID =  '$id' 
            ");

            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}