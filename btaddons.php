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

class btaddons extends Controller
{


    public function index(Request $request)
    {


             if((\App\Http\Controllers\users::roleCheck('Facilities','View',0)) == 'No')   
                    { return redirect('/home');}  


        
         if ($request->ajax()) {
   
            $data = DB::table('btaddons') 
                         ->select('btaddons.*',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'btaddons.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'btaddons.updated_by');


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
                    ->editColumn('inuse', function($data){ 

                        if($data->inuse == 1) {
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
                    ->rawColumns(['action','inuse'])
                    ->make(true);

                    
                  
        }

        $Lists = DB::table('Lists')->select('id','text','Default')->where('ListType','SCT')->where('InUse',1)->orderBy('ListOrder')->get();

          $data = [
            'Lists' => $Lists
          ];  
            
          return view ('btaddons')->with('data',$data);
        
    }


     public function BTAddon(Request $request)
    {



        if($request->id != '') {

          $data = DB::table('btaddons')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id'); 

     $data2 = DB::table('btproducts')->where('pid', $id)->get();   
           
           if(count($data2) > 0) {

                return response()->json(['error'=>'Data Exist.']);                  
           } 

     $log = DB::table('btaddons')->select('name')->where('id',$id)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Blood Transfusion', 'Product "'.$log[0]->name.'" Deleted. ']); 
           
     DB::table('btaddons')->where('id', $id)->delete(); 
     return response()->json(['success'=>'Data Deleted.']);

    }



     public function add(Request $request)
    {
        $id = DB::table('btaddons')->max('id')+1;
        $name = $request->input('name');
        $barcode = $request->input('barcode');
        $generic = $request->input('generic');
        $batch = $request->input('batch');
        $inuse = $request->input('inuse');

        $dppType = $request->input('dppType');
        $dpp = $request->input('dpp');



         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:btaddons,name',      
            'inuse' => 'required',
            'dppType' => 'required'
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



        DB::insert('insert into btaddons (id, name, barcode,  generic, batch, inuse, created_at, created_by, dpp, dppHours, dppType) values (?, ?, ?, ?, ?, ?,?,?, ?, ?, ?)', 
            [$id, $name, $barcode, $generic, $batch, $inuse, date('Y-m-d H:i:s'), $user['id'], $dpp, $dppHours, $dppType ] );  


            
             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Blood Transfusion ', 'New Prduct "'.$name.'" Added. ']); 
             return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $name = $request->input('name');
        $barcode = $request->input('barcode');
        $generic = $request->input('generic');
        $batch = $request->input('batch');
        $inuse = $request->input('inuse');

          $dppType = $request->input('dppType');
        $dpp = $request->input('dpp');

         $user = auth()->user();
        


         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:btaddons,name,'.$id,     
            'inuse' => 'required',
            'dppType' => 'required'
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
            update btaddons 
            set 
            name = '$name', 
            barcode = '$barcode', 
            dppType = '$dppType',
            dpp = '$dpp',
            dppHours = '$dppHours',
            inuse = '$inuse', 
            generic = '$generic', 
            batch = '$batch',  
            updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."' 
             where id =  '$id' 
            ");

            
            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Blood Transfusion', 'Product "'.$name.'" Updated. ']); 
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}