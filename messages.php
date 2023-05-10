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

class messages extends Controller
{


    public function index(Request $request)
    {

        
         if ($request->ajax()) {


             if(!empty($request->from_date))
                
                  { 


                      $userinfo = auth()->user(); 
                         $data = DB::table('signoffsmessages') 
                        ->select('signoffsmessages.*',
                                    'A.name as assignedfrom',
                                    'B.name as assignedto')
                        
                                    ->leftjoin('users AS A', 'A.id', '=', 'signoffsmessages.assignedby')
                                    ->leftjoin('users AS B', 'B.id', '=', 'signoffsmessages.userID')
                    

                          ->when(!empty($request->to_date) , function ($query) use($request){
                            
                             return $query->whereBetween('signoffsmessages.datetime', [$request->from_date, $request->to_date]);

                         })

                           ->when(!empty($request->sender) , function ($query) use($request){
                            
                             return  $query->where('signoffsmessages.assignedby','=',$request->sender);   

                         })

                             ->when(!empty($request->receiver) , function ($query) use($request){
                            
                             return  $query->where('signoffsmessages.userID','=',$request->receiver);   

                         })


                             ->when(!empty($request->subject) , function ($query) use($request){
                            
                             return  $query->where('signoffsmessages.subject', 'LIKE', '%'.$request->subject.'%'); 

                         })

                              ->when(!empty($request->sampleid) , function ($query) use($request){
                            
                             return   $query->where('signoffsmessages.sampleid','=',$request->sampleid);  

                         })
                                      

                        ->when(!empty($request->status) , function ($query) use($request){
                                        
                                    if($request->status == 'Unread') {

                                        return $query->where('signoffsmessages.datetimeread','=',null);
                                    }
                                    elseif($request->status == 'Read') {

                                        return $query->where('signoffsmessages.datetimeread','!=',null);
                                    }
                             });

                          // ->when(request('status', 'Read') , function ($query) use($request){
                                        
                          //           $query->where('signoffsmessages.datetimeread','!=',null);
                          //    });


                  } else {


                      $userinfo = auth()->user(); 
                        $data = DB::table('signoffsmessages') 
                        ->select('signoffsmessages.*',
                                    'A.name as assignedfrom',
                                    'B.name as assignedto')
                        
                                    ->leftjoin('users AS A', 'A.id', '=', 'signoffsmessages.assignedby')
                                    ->leftjoin('users AS B', 'B.id', '=', 'signoffsmessages.userID')
                        
                        ->when(empty($request->status) , function ($query) use($request){
                                         

                                         $userinfo = auth()->user();

                                         $query->where('userID', $userinfo->id )->orwhere('assignedby', $userinfo->id ); 
                                })
                         
                         ->when(!empty($request->status) , function ($query) use($request){
                                        
                                         
                                           $userinfo = auth()->user();
                                           $query->where('signoffsmessages.userID', $userinfo->id)
                                           ->where('signoffsmessages.datetimeread', '=', null);     


                                     });

                  }
            
          
                      


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                 <a type="button" href="Requests/viewRequest/'.$row->request.'/'.$row->episode.'" title="View Request" class="btn btn-info"><i class="fas fa-eye"></i>
                                </a>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 

                    ->addColumn('status', function($row){
     
                         
                             $userinfo = auth()->user(); 
                             $btn = '';

                            if($row->userID == $userinfo->id) {

                              if($row->datetimeread == '') {

                                $btn = '<a type="button" href="Requests/viewRequest/'.$row->request.'/'.$row->episode.'" title="View Request" class="btn btn-success btn-block"><i class="fas fa-envelope"></i> Received
                                </a>';  

                              }  else {

                                 $btn = '<a type="button" href="Requests/viewRequest/'.$row->request.'/'.$row->episode.'" title="View Request" class="btn btn-primary btn-block"><i class="fas fa-envelope-open"></i> Received
                                </a>'; 

                              }
                              

                            } else {

                                if($row->datetimeread == '') {


                                $btn = '<a type="button" href="Requests/viewRequest/'.$row->request.'/'.$row->episode.'" title="View Request" class="btn btn-info btn-block"><i class="fas fa-check"></i> Sent
                                </a>'; 
                                  

                                }  else {
                                $btn = '<a type="button" href="Requests/viewRequest/'.$row->request.'/'.$row->episode.'" title="View Request" class="btn btn-success btn-block"><i class="fas fa-check-double"></i> Sent
                                </a>'; 

                                }

                        }

                            return $btn;
                    }) 


                    


                    ->setRowId('id')
                    ->rawColumns(['action','status'])
                    ->make(true);

                    
                  
        }


            $users = DB::table('users')->get();
            $now = Carbon::now();
            $date1 =  $now->format('Y-m-01'); 
            $date2 =  $now->format('Y-m-t'); 


              $data = [
                    'Senders' => $users,
                    'Receivers' => $users,
                    'date1' => $date1,
                    'date2' => $date2


          ]; 


            
          return view ('messages')->with('data',$data);
        
    }




}