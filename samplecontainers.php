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

class samplecontainers extends Controller
{


    public function index(Request $request)
    {

        
             if((\App\Http\Controllers\users::roleCheck('Sample Containers','View',0)) == 'No')   
                    { return redirect('/home');}   

         if ($request->ajax()) {
   
            $data = DB::table('containers') 
                         ->select('containers.*',
                            'A.name as created_by',
                            'B.name as updated_by',
                            'C.Text as type'
                            )
                         ->leftjoin('Lists AS C', 'C.id', '=', 'containers.type')
                         ->leftjoin('users AS A', 'A.id', '=', 'containers.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'containers.updated_by');


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

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at;
                            
                        }
                     })

                    ->setRowId('id')
                    ->rawColumns(['action','status'])
                    ->make(true);

                    
                  
        }

        $SCT = DB::table('Lists')->select('id','text','Default')->where('ListType','SCT')->where('InUse',1)->orderBy('Text')->get();

          $data = [
            'SCTs' => $SCT
          ];  
            
          return view ('samplecontainers')->with('data',$data);
        
    }


     public function SampleContainer(Request $request)
    {



        if($request->id != '') {

          $data = DB::table('containers')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id');

    
     $log = DB::table('containers')->select('name')->where('id',$id)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Sample Containers', 'Sample Container "'.$log[0]->name.'" Deleted. ']); 

     DB::table('containers')->where('id', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('containers')->max('id')+1;
        $name = $request->input('name');
        $type = $request->input('type');
        $min_vol = $request->input('min_vol');
        $max_vol = $request->input('max_vol');
        $status = $request->input('status');

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:containers,name',      
            'type' => 'required',
            'min_vol' => 'required',
            'max_vol' => 'required',
            'status' => 'required'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into containers (id, name, type, min_vol, max_vol, status, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $name, $type, $min_vol, $max_vol, $status, date('Y-m-d H:i:s'), $user['id'] ] );  


            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Sample Containers', 'New Sample Container "'.$name.'" Added. ']); 
            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $name = $request->input('name');
        $type = $request->input('type');
        $sample_type = $request->input('sample_type');
        $min_vol = $request->input('min_vol');
        $max_vol = $request->input('max_vol');
        $category = $request->input('category');
        $status = $request->input('status');

         $user = auth()->user();
        


         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:testprofiles,name,'.$id,     
            'type' => 'required',
            'min_vol' => 'required',
            'max_vol' => 'required',
            'status' => 'required'
        ]);

        if ($validator->passes()) {

            
            DB::update("
            update containers 
            set 
            name = '$name', 
            type = '$type', 
            min_vol = '$min_vol', 
            max_vol = '$max_vol',  
            status = '$status', 
            updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."' 
             where id =  '$id' 
            ");

            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Sample Containers', 'Sample Container "'.$name.'" Updated. ']); 
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}