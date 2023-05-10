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

class facilities extends Controller
{


    public function index(Request $request)
    {


             if((\App\Http\Controllers\users::roleCheck('Facilities','View',0)) == 'No')   
                    { return redirect('/home');}  


        
         if ($request->ajax()) {
   
            $data = DB::table('facilities') 
                         ->select('facilities.*',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'facilities.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'facilities.updated_by');


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
                    ->editColumn('status', function($data){ 

                        if($data->status == 1) {
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

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); 
                            return $updated_at;
                            
                        }
                     })


                    ->setRowId('id')
                    ->rawColumns(['action','status'])
                    ->make(true);

                    
                  
        }

        $Lists = DB::table('Lists')->select('id','text','Default')->where('ListType','SCT')->where('InUse',1)->orderBy('ListOrder')->get();

          $data = [
            'Lists' => $Lists
          ];  
            
          return view ('facilities')->with('data',$data);
        
    }


     public function Facility(Request $request)
    {



        if($request->id != '') {

          $data = DB::table('facilities')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id'); 

     $data2 = DB::table('OCMRequestTestsDetails')->where('hospital', $id)->get();   
           
           if(count($data2) > 0) {

                return response()->json(['error'=>'Data Exist.']);                  
           } 

     $log = DB::table('facilities')->select('name')->where('id',$id)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Facilities', 'Facility "'.$log[0]->name.'" Deleted. ']); 
           
     DB::table('facilities')->where('id', $id)->delete(); 
     return response()->json(['success'=>'Data Deleted.']);

    }



     public function add(Request $request)
    {
        $id = DB::table('facilities')->max('id')+1;
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:facilities,name',      
            'type' => 'required',
            'status' => 'required'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into facilities (id, name, type,  status, created_at, created_by) values (?, ?, ?, ?, ?, ?)', 
            [$id, $name, $type, $status, date('Y-m-d H:i:s'), $user['id'] ] );  


            
             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Facilities', 'New Facility "'.$name.'" Added. ']); 
             return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $name = $request->input('name');
        $type = $request->input('type');
        $status = $request->input('status');

         $user = auth()->user();
        


         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:facilities,name,'.$id,     
            'type' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            
            DB::update("
            update facilities 
            set 
            name = '$name', 
            type = '$type', 
            status = '$status', 
            updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."' 
             where id =  '$id' 
            ");

            
            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Facilities', 'Facility "'.$name.'" Updated. ']); 
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}