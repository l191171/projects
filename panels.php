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


class panels extends Controller
{


    public function index(Request $request)
    {   

       

        if((\App\Http\Controllers\users::roleCheck('Panels','View',0)) == 'No')   
                    { return redirect('/home');}   


         if ($request->ajax()) {

                            if($request->InUse != '') {

                                $InUse = 1;
                            } 
                            else {

                                 $InUse = '';   
                            }
                                   
            
                                        if($InUse != '') {

                                            $data = DB::table('Panels')
                                        ->select(
                                            'Panels.*',
                                             DB::raw('(select count(listorder) from Panels) as MaxListOrder'),
                                             'A.name as created_by',
                                             'B.name as updated_by',
                                             'Lists.Text as department'
                                            ) 
                                        ->leftjoin('Lists', 'Lists.id', '=', 'Panels.department')
                                        ->leftjoin('users AS A', 'A.id', '=', 'Panels.created_by')
                                        ->leftjoin('users AS B', 'B.id', '=', 'Panels.updated_by')
                                        ->where('Panels.InUse', $InUse);

                                  
                                        } else {
                                            
                                            $data = DB::table('Panels')
                                        ->select(
                                            'Panels.*',
                                             DB::raw('(select count(listorder) from Panels) as MaxListOrder'),
                                             'A.name as created_by',
                                             'B.name as updated_by',
                                             'Lists.Text as department'
                                            ) 
                                        ->leftjoin('Lists', 'Lists.id', '=', 'Panels.department')
                                        ->leftjoin('users AS A', 'A.id', '=', 'Panels.created_by')
                                        ->leftjoin('users AS B', 'B.id', '=', 'Panels.updated_by');

                                        }    

            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($data){
     
                           $btn = '
                                <div class="btn-group" role="group">';

                               if($data->listorder == 1) {
                            
                           
                            $btn .= '<button  title="Edit" class="btn btn-info movedown" index="'.$data->listorder.'">
                                 <i class="fas fa-arrow-down"></i>
                                </button>';
                            }
                            elseif($data->listorder == $data->MaxListOrder) {
                            
                           
                            $btn .= '<button  title="Edit" class="btn btn-info moveup" index="'.$data->listorder.'">
                                 <i class="fas fa-arrow-up"></i>
                                </button>';
                            }
                             else {

                            $btn .= '
                                <button  title="Edit" class="btn btn-info moveup" index="'.$data->listorder.'">
                                 <i class="fas fa-arrow-up"></i>
                                </button>

                                <button  title="Edit" class="btn btn-info movedown" index="'.$data->listorder.'">
                                 <i class="fas fa-arrow-down"></i>
                                </button>
                                ';
                            }   
                             
                                 $btn .= '<button id="'.$data->id.'" title="Edit" class="btn btn-primary update">
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


         
        $Lists = DB::table('Lists')->select('id','text','Default')->where('ListType','DPT')->where('InUse',1)->orderBy('ListOrder')->get();

          $data = [
            'Lists' => $Lists
          ];  
            
          return view ('panels')->with('data',$data);
        
    }


     public function Panel(Request $request)
    {


        if($request->id != '') {

          $data = DB::table('Panels')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    } 


 


    public function delete(Request $request)
    {


     $id = $request->input('id');   
     
     $log = DB::table('Panels')->select('name')->where('id',$id)->get();
     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Panel', 'Panel "'.$log[0]->name.'" Deleted.']); 

     DB::table('Panels')->where('id', $id)->delete(); 
     DB::table('PanelsMapping')->where('PanelD', $id)->delete(); 

    }



     public function add(Request $request)
    {   



        $id = DB::table('Panels')->max('id')+1;
        $name = $request->input('name');
        $barcode = $request->input('barcode');
        $InUse = $request->input('InUse');

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:Panels,name',
            'InUse' => 'required'
        ]);
     

             if ($validator->passes()) {

 
        $listorder = DB::table('Panels')->max('listorder')+1;           
        DB::insert('insert into Panels (id, name, barcode, listorder,  InUse, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
            [$id, $name, $barcode, $listorder, $InUse, date('Y-m-d H:i:s'), $user['id'] ] );  


             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Panel', 'New panel "'.$name.'" Added.']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {   
        

        $id = $request->input('id');   
        $name = $request->input('name');  
        $barcode = $request->input('barcode');
        $InUse = $request->input('InUse');


         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:Panels,name,'.$id,
            'InUse' => 'required'
        ]);

        if ($validator->passes()) {



            DB::update("
            update Panels 
            set 
            name = '$name',
              barcode = '$barcode',

            InUse = '$InUse', updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where id =  '$id' 
            ");

             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Panel', 'Panel "'.$name.'" Updated.']); 

            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 


      public function PanelRowShift(Request $request)
    {

         $currentRow = $request->input('currentRow');
         $moveToRow = $request->input('moveToRow');
         $currentRowId = $request->input('currentRowId');
         $nextRowId = $request->input('nextRowId');

         DB::update("update Panels set listorder = $moveToRow where id = $currentRowId");
         DB::update("update Panels set listorder = $currentRow where id = $nextRowId");


    }



}