<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
use Auth;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App;  
class files extends Controller
{


     public function index(Request $request)
    {


           if((\App\Http\Controllers\users::roleCheck('Documents Uploading','View',0)) == 'No')   
                    { return redirect('/home');}   


         if ($request->ajax()) {
            
            $data = DB::table('CaseFiles') 
                         ->select('CaseFiles.FileID',
                            'CaseFiles.FileName',
                            'CaseFiles.FilePath',
                            'CaseFiles.FileExtention',
                            'CaseFiles.CreatedDateTime',
                            'CaseFiles.ModifiedDateTime',
                            'CaseFiles.InUse',
                            'A.name as CreatedBy',
                            'B.name as ModifiedBy',
                            'C.Text as FileType',
                            'D.Text as FileDepartment')
                         ->leftjoin('users AS A', 'A.id', '=', 'CaseFiles.CreatedBy')
                         ->leftjoin('users AS B', 'B.id', '=', 'CaseFiles.ModifiedBy')
                         ->leftjoin('Lists AS C', 'C.id', '=', 'CaseFiles.FileType')
                         ->leftjoin('Lists AS D', 'D.id', '=', 'CaseFiles.FileDepartment')
                          ->where('CaseFiles.FileName', '!=', null);


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                            
                             $btn = '<div class="btn-group" role="group">
                             <a href="File/'.$data->FileID.'" title="Edit User" class="update btn btn-primary">
                                 <i class="bx bx-edit"></i>
                                </a>
                             <button type="button" title="Delete" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>';

                            return $btn;
                    }) 
                    
                    ->editColumn('FilePath', function($data){ 

                          $url = asset('storage').'/'.$data->FilePath;
                
                            return "<a target='_blank' href='{$url}' class='btn btn-secondary'>View/Download
                                </a>";
                        
                    })

                    ->editColumn('FileExtention', function($data){ 

                        if($data->FileExtention == 'jpg' || $data->FileExtention == 'png' || $data->FileExtention == 'jpeg' || $data->FileExtention == 'gif') {
                            return '<button type="button" class="btn btn-warning"><i class="fas fa-image"></i> Image 
                                </button>';
                        }
                        if($data->FileExtention == 'pdf') {
                            return '<button type="button" class="btn btn-success"><i class="fas fa-file-pdf"></i> PDF
                                </button>';
                        }

                        if($data->FileExtention == 'doc' || $data->FileExtention == 'docx') {
                            return '<button type="button" class="btn btn-info"><i class="fas fa-file-word"></i> Word
                                </button>';
                        }
                        if($data->FileExtention == 'xlsx') {
                            return '<button type="button" class="btn btn-primary"><i class="fas fa-file-excel"></i> Excel
                                </button>';
                        }
                         else {
                            return '<button type="button" class="btn btn-danger">Other/'.$data->FileExtention.'
                                </button>';
                        }
                    })

                     ->editColumn('InUse', function($data){ 

                        if($data->InUse == 1) {
                            return '<div class="input-group-prepend">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                      Yes
                                    </button>
                                    <ul class="dropdown-menu">
                                      <li id="0" class="dropdown-item status"><span type="button" class="status">No</span></li>
                                    </ul>
                                  </div>';
                        } else {
                            return '<div class="input-group-prepend">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                      No
                                    </button>
                                    <ul class="dropdown-menu">
                                      <li id="1" class="dropdown-item status"><span type="button" class="status">Yes</span></li>
                                    </ul>
                                  </div>';
                        }
                    })
                    ->editColumn('CreatedDateTime', function($data){ 
                     if($data->CreatedDateTime != '') {
                        $CreatedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $data->CreatedDateTime)->format('d M Y H:i a'); return $CreatedDateTime;
                            } 
                        })
                    ->editColumn('ModifiedDateTime', function($data){ 
                        
                         if($data->ModifiedDateTime != '') {

                        $ModifiedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $data->ModifiedDateTime)->format('d M Y H:i a'); return $ModifiedDateTime;
                            }   
                            
                         })

                    ->setRowId('FileID')
                    ->rawColumns(['action','FilePath','FileExtention','InUse'])
                    ->make(true);

                    
     
            }           
                    

        return view ('files');
        
    }


       public function File(Request $request)
    {

  


        $file = '';
        $editmode = 'off';



        $docTypes = DB::table('Lists')->select('Text','id','Default')->where('ListType','FL')->get();
        $DPTs = DB::table('Lists')->select('Text','id','Default')->where('ListType','DPT')->get();


        if($request->id != '') {

         $file = DB::table('CaseFiles')->where('FileID', $request->id)->get();
         $editmode = 'on';

                  if((\App\Http\Controllers\users::roleCheck('Documents Uploading','Update',0)) == 'No')   
                    { return redirect('/home');}     


        } else {

                  if((\App\Http\Controllers\users::roleCheck('Documents Uploading','Add',0)) == 'No')   
                    { return redirect('/home');}   
        }
            
          $data = [
                    'editmode' => $editmode,
                    'file' => $file,
                    'types' => $docTypes,
                    'DPTs' => $DPTs
          ];  

          return view ('file')->with('data',$data);
    }  
 


     public function add(Request $request)
    {   

         $user = auth()->user();
        
        if ($request->hasFile('file')) {
                
                $file = $request->file('file');
                $extension = $request->file->getClientOriginalExtension();
         
                if($request->file('file')->getSize() > 4000000) {

                    return response()->json(['error'=> 'File size should be less than 40mb']);
             
                }

                $destinationPath = public_path('storage');
                $filename = uniqid().'.'.$extension;
                $file->move($destinationPath,$filename);

                $id = DB::table('CaseFiles')->max('FileID')+1;

                DB::insert('insert into CaseFiles 
                (FileID, FileExtention, FilePath, InUse, CreatedDateTime, CreatedBy) values (?, ?, ?, ?, ?, ?)', 
                [$id, $extension, $filename, 1,  date('Y-m-d H:i:s'), $user->id]);

                return response()->json(['success'=>'File uploaded.', 'filename' => $filename]);
        }


    }





     public function addFileInfo(Request $request)
    {   


        $name = $request->input('name');
        $status = $request->input('status');
        $filename = $request->input('filename');
        $type = $request->input('type');
        $department = $request->input('department');
        $user = auth()->user();
        

        $validator = Validator::make($request->all(), [
            'name' => 'required',   
            'status' => 'required',   
            'filename' => 'required',
            'type' => 'required',
            'department' => 'required'
        ]);
     

     if ($validator->passes()) {

      
        DB::update("
            update CaseFiles 
            set 
            FileName = '$name', FileType = '$type', FileDepartment = '$department', ModifiedDateTime = '".date('Y-m-d H:i:s')."' , ModifiedBy = '".$user['id']."'
            where FilePath =  '$filename' 
            ");  


     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Documents', 'New Document "'.$name.'" Added.']); 


            return response()->json(['success'=>'File Added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);


    }




     public function update(Request $request)
    {   

         $user = auth()->user();
         $id = $request->input('id');
        
        if ($request->hasFile('file')) {
                
                
                $file = $request->file('file');
                $extension = $request->file->getClientOriginalExtension();
         
                if($request->file('file')->getSize() > 4000000) {

                    return response()->json(['error'=> 'File size should be less than 40mb']);
             
                }

                $destinationPath = public_path('storage');
                $filename = uniqid().'.'.$extension;
                $file->move($destinationPath,$filename);


                return response()->json(['success'=>'File uploaded.', 'filename' => $filename]);
        }


    }


     public function updateFileInfo(Request $request)
    {   


        $id = $request->input('id');
        $name = $request->input('name');
        $status = $request->input('status');
        $filename = $request->input('filename');
        $department = $request->input('department');
        $type = $request->input('type');
        $extension = strrchr( $filename, '.');
        $extension = substr( $extension,1);
        $user = auth()->user();
        

        $validator = Validator::make($request->all(), [
            'name' => 'required',   
            'status' => 'required',   
            'filename' => 'required',
            'type' => 'required'
        ]);
     

     if ($validator->passes()) {

      
        DB::update("
            update CaseFiles 
            set 
            FileName = '$name', FileType = '$type', FilePath = '$filename', FileDepartment = '$department', FileExtention = '$extension', InUse = '$status', ModifiedDateTime = '".date('Y-m-d H:i:s')."' , ModifiedBy = '".$user['id']."'
            where FileID =  '$id' 
            ");  


     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Documents', 'Document "'.$name.'" info Updated.']); 



            return response()->json(['success'=>'Info Updated.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);


    }



     public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $user = auth()->user();
        

        $validator = Validator::make($request->all(), [
            'id' => 'required',      
            'status' => 'required'
        ]);
     

     if ($validator->passes()) {

        if($status == 1) {

            $status_ = 'Active';
        } 
        else {

            $status_ = 'In-Active';    
        }

        DB::update("
            update CaseFiles 
            set 
            InUse = '$status', ModifiedDateTime = '".date('Y-m-d H:i:s')."' , ModifiedBy = '".$user['id']."'
            where FileID =  $id 
            ");  

             $log = DB::table('CaseFiles')->select('FileName')->where('FileID',$id)->get();   
             $controller = App::make('\App\Http\Controllers\activitylogs');
             $data = $controller->callAction('addLogs', [0,0,0,0,0,'Documents', 'Document "'.$log[0]->FileName.'" status Updated to '.$status_.'.']); 

            return response()->json(['success'=>'Status updated.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


    public function delete(Request $request)
    {
     $id = $request->input('id');   


    $log = DB::table('CaseFiles')->select('FileName')->where('FileID',$id)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Documents', 'Document "'.$log[0]->FileName.'" Deleted. ']); 


     DB::table('CaseFiles')->where('FileID', $id)->delete();   
    }



}