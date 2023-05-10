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

class questions extends Controller
{


    public function index(Request $request)
    {
                   
                    if((\App\Http\Controllers\users::roleCheck('Profile Questions','View',0)) == 'No')   
                    { return redirect('/home');} 
        
         if ($request->ajax()) {
   
            $data = DB::table('ProfileQuestions') 
                         ->select('ProfileQuestions.ID',
                            'ProfileQuestions.created_at',
                            'ProfileQuestions.updated_at',
                            'ProfileQuestions.question',
                            'ProfileQuestions.answers',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'ProfileQuestions.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ProfileQuestions.updated_by');


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

        $answers = DB::table('Lists')->select('id','Text','Default')->where('ListType','ANS')->where('InUse',1)->orderBy('Text')->get();



          $data = [
            'answers' => $answers
          ];  
            
          return view ('questions')->with('data',$data);
        
    }


     public function Question(Request $request)
    {

        if($request->id != '') {

          $data = DB::table('ProfileQuestions')->where('ID', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     $id = $request->input('id');

      $log = DB::table('ProfileQuestions')->select('question')->where('ID',$id)->get();

         $controller = App::make('\App\Http\Controllers\activitylogs');
         $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Questions', 'Question "'.$log[0]->question.'" Deleted. ']); 

     DB::table('ProfileQuestions')->where('id', $id)->delete(); 

    }



     public function add(Request $request)
    {
        $id = DB::table('ProfileQuestions')->max('ID')+1;
        $question = $request->input('question');
        $answers = $request->input('answers');

        if(!empty($answers)) {

           $answers = implode(',',$answers);
        }

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'question' => 'required|unique:ProfileQuestions,question'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into ProfileQuestions (ID, question, answers, created_at, created_by) values (?, ?, ?, ?, ?)', 
            [$id, $question, $answers, date('Y-m-d H:i:s'), $user['id'] ] );  


            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Questions', 'New Question "'.$question.'" Added. ']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


      public function update(Request $request)
    {
        $id = $request->input('id');   
        $question = $request->input('question');
        $answers = $request->input('answers');

        if(!empty($answers)) {

           $answers = implode(',',$answers);
        }

         $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'question' => 'required|unique:ProfileQuestions,question,'.$id
        ]);

        if ($validator->passes()) {

            
            DB::update("
            update ProfileQuestions 
            set 
            question = '$question', answers = '".$answers."', updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where ID =  '$id' 
            ");

            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Questions', 'Question "'.$question.'" Updated. ']); 
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 




}