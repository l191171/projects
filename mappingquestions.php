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

class mappingquestions extends Controller
{


    public function index0(Request $request)
    {

        
         if ($request->ajax()) {

               
                    if(!empty($request->profile))
                 
                  {

                 $questions = DB::table('ProfileQuestionMapping')->select('QuestionID')->where('ProfileID',$request->profile)->get(); 
                 $questionsList = array();
                 foreach($questions as $question) {

                    $questionsList[] = $question->QuestionID;
                 }  

                $data = DB::table('ProfileQuestions') 
                         ->select('ProfileQuestions.ID',
                            'ProfileQuestions.question')
                            ->whereNotIn('ProfileQuestions.ID', $questionsList);

                  }  else {

                    $data = DB::table('ProfileQuestions') 
                         ->select('ProfileQuestions.ID',
                            'ProfileQuestions.question')
                                ->limit(0);


                  }            
            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <button type="button" id="'.$row->ID.'"  title="Add" class="add btn btn-warning"><i class="bx bx-plus"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 
                

                    ->setRowId('ID')
                    ->rawColumns(['action'])
                    ->make(true);
                    
                  
        }

            
          return view ('mappingquestions');
        
    }



    public function index(Request $request)
    {


            if((\App\Http\Controllers\users::roleCheck('Profile Questions Mapping','View',0)) == 'No')   
                    { return redirect('/home');}   
                      
        
         if ($request->ajax()) {

                 if(!empty($request->profile))
                 
                  {


            $data = DB::table('ProfileQuestionMapping') 
                         ->select('ProfileQuestionMapping.ID',
                            'ProfileQuestionMapping.created_at',
                            'ProfileQuestionMapping.updated_at',
                            'testprofiles.name as ProfileID',
                            'ProfileQuestions.question as QuestionID',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ProfileQuestionMapping.ProfileID')
                         ->leftjoin('ProfileQuestions', 'ProfileQuestions.ID', '=', 'ProfileQuestionMapping.QuestionID')
                         ->leftjoin('users AS A', 'A.id', '=', 'ProfileQuestionMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ProfileQuestionMapping.updated_by')
                         ->where('ProfileQuestionMapping.ProfileID',$request->profile);

                   } else {


                        $data = DB::table('ProfileQuestionMapping') 
                         ->select('ProfileQuestionMapping.ID',
                            'ProfileQuestionMapping.created_at',
                            'ProfileQuestionMapping.updated_at',
                            'testprofiles.name as ProfileID',
                            'ProfileQuestions.question as QuestionID',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ProfileQuestionMapping.ProfileID')
                         ->leftjoin('ProfileQuestions', 'ProfileQuestions.ID', '=', 'ProfileQuestionMapping.QuestionID')
                         ->leftjoin('users AS A', 'A.id', '=', 'ProfileQuestionMapping.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'ProfileQuestionMapping.updated_by')
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

        $profiles = DB::table('testprofiles')->select('id','name')->where('InUse',1)->orderBy('name')->get();

          $data = [
            'profiles' => $profiles
          ];  
            
          return view ('mappingquestions')->with('data',$data);
        
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

     $log = DB::table('ProfileQuestionMapping')->select('ProfileID','QuestionID')->where('ID',$id)->get();

     $Profile = DB::table('testprofiles')->select('name')->where('id',$log[0]->ProfileID)->get();
     $Question = DB::table('ProfileQuestions')->select('question')->where('ID',$log[0]->QuestionID)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Questions Mapping', 'Question "'.$Question[0]->question.'" removed from Profile "'.$Profile[0]->name.'". ']); 


     return DB::table('ProfileQuestionMapping')->where('ID', $id)->delete(); 

    }



     public function add(Request $request)
    {
   
        $id = DB::table('ProfileQuestionMapping')->max('ID')+1;
        $profile = $request->input('profile');
        $question = $request->input('question');


         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [      
            'profile' => 'required|unique:ProfileQuestionMapping,ProfileID,NULL,ID,QuestionID,'.$question,
            'question' => 'required'
        ]);
     

             if ($validator->passes()) {


        DB::insert('insert into ProfileQuestionMapping (ID, ProfileID, QuestionID, created_at, created_by) values (?, ?, ?, ?, ?)', 
            [$id, $profile, $question, date('Y-m-d H:i:s'), $user['id'] ] );  

     $Profile = DB::table('testprofiles')->select('name')->where('id',$profile)->get();
     $Question = DB::table('ProfileQuestions')->select('question')->where('ID',$question)->get();

     $controller = App::make('\App\Http\Controllers\activitylogs');
     $data = $controller->callAction('addLogs', [0,0,0,0,0,'Profile Questions Mapping', 'Question "'.$Question[0]->question.'" added to Profile "'.$Profile[0]->name.'". ']); 

            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }




}