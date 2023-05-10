<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use App\Models\ticketattachment;
use App\Models\ticketmessages;
use DB;
use App\Mail\SignUp;
use Mail;
// use Illuminate\Support\Facades\Mail;
use App;
use Carbon\Carbon;
use DataTables;
use DateTime;
use Artisan;
class tickets extends Controller
{


   public function uploadFiles(Request $request)
   {                    
// return $request ;
// $destinationPath = '/home/ocmsoftware/public_html/support/public/images';
         if ($request->hasfile('files')) {
            
// return $request;
            foreach ($request->file('files') as $file) {
                
                $name = $file->getClientOriginalName();
            $destinationPath = public_path('images/');
                // $destinationPath = '/Applications/XAMPP/xamppfiles/htdocs/ocm/storage/app/images/';
              $extension = $file->getClientOriginalExtension();
         $filename = uniqid().'.'.$extension;
              $file->move($destinationPath,$filename);

            if($request->mid != '') {


  DB::insert('insert into ticketattachments (ticketid, filename,  datetime, mid) values (?, ?, ?, ?)', 
            [$request->tid, $filename, date('Y-m-d H:i:s'),  $request->mid] ); 

            } else {


          DB::insert('insert into ticketattachments (ticketid, filename,  datetime) values (?, ?, ?)', 
            [$request->tid, $filename, date('Y-m-d H:i:s') ] ); 

            }    

                
            }

             return response()->json(['success'=>'File uploaded.']);
        }


   } 

   public function Ticket(Request $request){

         $patients = array();


        if($request->id != '') {

             $id = $request->id;
             $ticketinfo=ticket::find($id); 
                $ticketattachments=ticketattachment::find($id); 

                $data = [
                            'ticketinfo' => $ticketinfo,
                            'ticketattachments' => $ticketattachments,
                            'patients' => $patients

                  ];  

        } 
        else {


                $data = [
                            'ticketinfo' => '',
                            'ticketattachments' => '',
                            'patients' => $patients

                  ];    

   
        }  
        
        return view('ticket')->with('data', $data);  

    
    }



       public function TicketView(Request $request){

        // $ticketinfo=DB::table('tickets')->where('id','<',12410)->delete(); 
        
                $patients = array(); 
                
                $id = $request->id;
                // return $request;
                $ticketinfo=DB::table('tickets')->where('id',$id)->get(); 
               
                $ticketattachments=DB::table('ticketattachments')->where('mid', null)->where('ticketid',$ticketinfo[0]->ticketid)->get(); 

// return $user=auth()->user();

                  $ticketmessages = DB::table('ticketmessages') 
                         ->select('*')
                         ->where('ticketmessages.ticketid',$ticketinfo[0]->ticketid)
                         ->orderBy('created_at','asc')
                         ->get();
                        //  $ticketinfo=DB::table('tickets')->where('id',$id)->get(); 
                         
// $maxid=DB::table('timeline')->where('ticketid','=',$ticketinfo[0]->ticketid)->max('id');

                        //  $status=DB::table('timeline')->select('status')->where('id','=',$maxid)->get(); 
  
$internal=DB::table('tickets')->where('id',$id)->pluck('internal');
$contact=DB::table('tickets')->where('id',$id)->pluck('contact');

                $data22= [
                            

                            'ticketinfo' => $ticketinfo,
                            'ticketattachments' => $ticketattachments,
                            'ticketmessages' => $ticketmessages,
                            'patients' => $patients,
                            'internal'=>$internal,
                           'contact'=>$contact

                  ];  
                  
// return $data['ticketinfo'][0]->status2;

        
        return view('ticketview')->with('data22', $data22);  

    
    }

    public static function getTicketReplyAttachments($mid){


        
         $mid =DB::table('ticketattachments')->where('mid',$mid)->get();

         return $mid;


    }


public function save(Request $request){
   
      $patientname = $request->patientname;
    $contact = $request->contact ;
        $sampleid = $request->sampleid;
        $subject = $request->subject;
        $department = $request->department;
        $priority = $request->priority;
        $message = $request->message;
        $tid = $request->tid;
           
            $id=auth()->user()->role;
           $email=auth()->user()->email;
           $date=Carbon::now();


           $validator=$request->validate([
             'subject' => 'required',
            'department' => 'required',
            'priority'=> 'required',
            'message' => 'required',
             'contact'=>'required'
                                 
        ]);
        // if ($validator->fails())
        // {
        //     return response()->json(['status'=>0,'errors'=>$validator->errors()[0]]);
        // }
         DB::insert('insert into tickets (patientname, username, contact,sampleid,subject,department,priority,message,created_at,created_by,ticketid,status,mailed) values (?,?,?,?,?,?,?,?,?,?,?,?,?)', [$patientname,$email,$contact,$sampleid,$subject,$department,$priority,$message,$date,$id,$tid,'Opened',0]);
              return response()->json(['status','true',$tid]);

}
public function commands()
{   
    /* php artisan config:clear */
    // Artisan::call(cache:clear);
    shell_exec("composer dump-autoload");

    dd('Configuration cache cleared!');
}

public function updateTicketInfo(Request $request){


        // return $request;
        $patientname = $request->patientname;
        $contact = $request->contact ;
        $sampleid = $request->sampleid;
        $subject = $request->subject;
         $department = $request->department;
          $priority = $request->priority;
           $message = $request->message;
             $tid = $request->tid;
             $mid = $request->mid;
           
           $user = auth()->user()->id;
           $email=auth()->user()->email;
           $date=Carbon::now();


           $validator=$request->validate([
            'tid' => 'required',
            'mid' => 'required',
             'subject' => 'required',
            'department' => 'required',
            'priority'=> 'required',
            'message' => 'required'
            
                                 
        ]);
        // if ($validator->passes())
        // {
        //     return response()->json(['status'=>0,'errors'=>$validator->errors()->first()]);
        // }
        

     DB::insert('insert into ticketmessages (ticketid, mid, username, message, user, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
            [$request->tid, $request->mid, $email, $message, 'client', date('Y-m-d H:i:s'), $user ]); 
            DB::update("update tickets  set priority = '$priority'  where ticketid = '".$tid."'");
            // DB::table('tickets')
            // ->where('ticketid', $request->tid)
            // ->update(['priority' => $priority]);
            
            $status= DB::table('tickets')->select('status')->where('ticketid',$request->tid)->first();
            if($status->status=="Completed"){
                DB::update("update tickets  set status = 'Processing' where ticketid = '".$request->tid."'");

            }

      $tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
      return $tid[0]->id;

         
          

}
public function sendTicketToNET(Request $request){

// return $request;
        
    $patientname = $request->patientname;
    $requestid = $request->requestid ;
    $sampleid = $request->sampleid;
    $subject = $request->subject;
     $department = $request->department;
      $priority = $request->priority;
       $message = $request->message;
         $tid = $request->tid;
         $mid = $request->mid;
       
       $user = auth()->user()->id;
       $email=auth()->user()->email;
       $date=Carbon::now();


    $validated = $request->validate([
        'tid' => 'required',
        'mid' => 'required',
         'subject' => 'required',
        'department' => 'required',
        'priority'=> 'required',
        'message' => 'required'
        
                             
    ]);
 $loggedin=auth()->user()->role;

 DB::insert('insert into ticketmessages (ticketid, mid, username, message, user, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
        [$request->tid, $request->mid, $email, $message, 'client', date('Y-m-d H:i:s'), $user ]); 
         DB::update("update tickets  set priority = '$priority'  where ticketid = '".$tid."'");

        if($loggedin>=4){

            DB::update("update tickets  set status = 'Opened', internal = 2  where ticketid = '".$request->tid."'");

        }else{

            DB::update("update tickets  set status = 'Processing', internal = 2  where ticketid = '".$request->tid."'");
            
        }


  $tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
  return $tid[0]->id;
}
public function searches(){
    $search_text = $_POST['tid'];
   if($search_text!=""){
    $products = ticket::whereticketid($search_text)->get();
    return view('searche',compact('products'));
}
else{
    return ("Not found");
}
//     public function data(){
//         $search_text = $_GET['search'];
//        if($search_text!=""){
//         $products = Apps::where('name','LIKE','%'.$search_text.'%')->get();
//         return view('data',compact('products'));
//     }

// }
}

public function sendTicketToOCM(Request $request){


        
        $patientname = $request->patientname;
        $requestid = $request->requestid ;
        $sampleid = $request->sampleid;
        $subject = $request->subject;
         $department = $request->department;
          $priority = $request->priority;
           $message = $request->message;
             $tid = $request->tid;
             $mid = $request->mid;
           
           $user = auth()->user()->id;
           $email=auth()->user()->email;
           $date=Carbon::now();


        $validated = $request->validate([
            'tid' => 'required',
            'mid' => 'required',
             'subject' => 'required',
            'department' => 'required',
            'priority'=> 'required',
            'message' => 'required'
            
                                 
        ]);

     DB::insert('insert into ticketmessages (ticketid, mid, username, message, user, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
            [$request->tid, $request->mid, $email, $message, 'client', date('Y-m-d H:i:s'), $user ]); 
             DB::update("update tickets  set priority = '$priority'  where ticketid = '".$tid."'");
 if(auth()->user()->role>=4){

    DB::update("update tickets  set status = 'Opened', internal = 1  where ticketid = '".$request->tid."'");
    
 }
 else{

    DB::update("update tickets  set status = 'Processing', internal = 1  where ticketid = '".$request->tid."'");
    
 }
    

      $tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
      return $tid[0]->id;

         
          

}

public function StartTicket(Request $request){
    // return $request->tid;

    DB::insert('insert into timeline (ticketid, time1) values (?, ?)', 
    [$request->tid,date('Y-m-d H:i:s')]);

    DB::update("Update tickets set status2='Start' where ticketid= '".$request->tid."' ");

$tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
return $tid[0]->id;


}
public function PauseTicket(Request $request){



$time=date('Y-m-d H:i:s');
 $id=DB::table('timeline')->where('ticketid','=',$request->tid)->max('id'); 
    DB::update("update  timeline set time2 ='$time' where ticketid = '".$request->tid."' AND  id= '$id'");
    DB::update("Update tickets set status2='Pause' where ticketid= '".$request->tid."' ");
    
    $s=DB::table('timeline')->select('time1')->where('id','=',$id)->get();

$t=$s[0]->time1;
$timeFirst  = strtotime($t);
$timeSecond = strtotime($time);
$diff = ($timeSecond - $timeFirst);

// return $diff;
DB::update("update  timeline set totaltime ='$diff' where ticketid = '".$request->tid."' AND  id= '$id'");
    
$tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
    return $tid[0]->id;



}

public function CompleteTicket(Request $request){


        
    $patientname = $request->patientname;
    $requestid = $request->requestid ;
    $sampleid = $request->sampleid;
    $subject = $request->subject;
     $department = $request->department;
      $priority = $request->priority;
       $message = $request->message;
         $tid = $request->tid;
         $mid = $request->mid;
       
       $user = auth()->user()->id;
        $email=auth()->user()->email;
       $date=Carbon::now()->format('Y-m-d H:i:s');


    $validated = $request->validate([
        'tid' => 'required',
        'mid' => 'required',
        'department' => 'required',
        'priority'=> 'required',
        'message' => 'required'
        
                             
    ]);
    $status =DB::table('tickets')->where('ticketid',$request->tid)->first();
    if( $status->status2=='Start'){

    $tim=date('Y-m-d H:i:s');
    $id=DB::table('timeline')->where('ticketid','=',$request->tid)->max('id'); 
       DB::update("update  timeline set time2 ='$tim' where ticketid = '".$request->tid."' AND  id= '$id'");
       DB::update("Update tickets set status2='Pause' where ticketid= '".$request->tid."' ");
       
       $s=DB::table('timeline')->select('time1')->where('id','=',$id)->get();
     
   $t=$s[0]->time1;
   $timeFirst  = strtotime($t);
   $timeSecond = strtotime($tim);
   $diff = ($timeSecond - $timeFirst);
   
   // return $diff;
   DB::update("update  timeline set totaltime ='$diff' where ticketid = '".$request->tid."' AND  id= '$id'");
    }
    $time=DB::table('timeline')->select('totaltime')->where('ticketid',$request->tid)->sum('totaltime');
  
  
    function secondsToTime($seconds) {
      $dtF = new \DateTime('@0');
      $dtT = new \DateTime("@$seconds");
      return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
  }
  $ab=secondsToTime($time);
 if ($ab[0]=='0'){

    $ab=str_replace('0 days, ','',$ab);
   
 }

 if ($ab[0]=='0'){

    $ab=str_replace('0 hours, ','',$ab);
    
 }
 if ($ab[0]=='0'){

    $ab=str_replace('0 minutes and ','',$ab);
    
 }

 DB::insert('insert into ticketmessages (ticketid, username, mid, message, user, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
        [$request->tid, $email, $request->mid, $message, 'client', date('Y-m-d H:i:s'), $user ]); 


   DB::update("update tickets  set status = 'Completed',  timetaken='".$ab."' where ticketid = '".$request->tid."'");
   DB::update("update tickets  set priority = '$priority'  where ticketid = '".$tid."'");
   DB::update("update tickets  set completedat ='$date' where ticketid = '".$request->tid."'");

   $tid =DB::table('tickets')->where('ticketid',$request->tid)->get();

  return $tid[0]->id;

     
      

}

public function reporte(Request $request){
  
if($request->ajax()){
    
    $user=auth()->user()->email;
    
    $role=auth()->user()->role;
    if($role>5){
    // return $user;
//    return $request;
    if($request->tilldate=="")
    {
    $request->tilldate=date('Y-m-d');
    
    }


$project=DB::table('tickets')->select('*')
->when($request->todate, function($query) use ($request){
    $todate=$request->todate." 00:00:00";
    
    $tilldate=$request->tilldate." 23:59:59";

   
    $query->whereBetween('created_at', 
    [$todate, $tilldate]
);
   
})


->when($request->status, function($query) use ($request){
    $query->where('status', '=', $request->status);
})
->when($request->assignedby, function($query) use ($request){
    $query->where('assignedby', '=', $request->assignedby);
})


->when($request->assignedto, function($query) use ($request){
    $query->where('assignedto', '=', $request->assignedto);
})
->where('username','=',$user)


->get();
return Datatables::of($project)
->addIndexColumn()
->rawColumns(['action'])
  ->make(true);
;

}
else{
    $project=DB::table('tickets')->select('*')
    ->when($request->todate, function($query) use ($request){
        $todate=$request->todate." 00:00:00";
        
        $tilldate=$request->tilldate." 23:59:59";
    
       
        $query->whereBetween('created_at', 
        [$todate, $tilldate]
    );
       
    })
    
    
    ->when($request->status, function($query) use ($request){
        $query->where('status', '=', $request->status);
    })
    ->when($request->assignedby, function($query) use ($request){
        $query->where('assignedby', '=', $request->assignedby);
    })
    
    
    ->when($request->assignedto, function($query) use ($request){
        $query->where('assignedto', '=', $request->assignedto);
    })
    
    
    
    ->get();
    return Datatables::of($project)
    ->addIndexColumn()
    ->rawColumns(['action'])
      ->make(true);
    ;  
}}

$data=DB::table('users')->where('role','>=','1')->get();

// array_unique($data2);
   return view('reports',[

    'data'=>$data,
  
]);
}


public function sendMail(Request $request){

 if($request->ajax()){
$tid=$request->tid;
        // $raisedby=auth()->user()->name;
        // $email=auth()->user()->email;
        //  $message=$request->message;
              $completedby=auth()->user()->name;
        $completedmail=auth()->user()->email;
       
        $status=DB::table('tickets')->where('ticketid',$tid)->pluck('status');
         $email=DB::table('tickets')->where('ticketid',$tid)->pluck('username');
         $raisedby=DB::table('users')->where('email',$email[0])->pluck('name');
       
   
        
        $q=DB::table('users')->where('role','4')
     ->pluck('email')->toArray();
        
 $id=DB::table('tickets')->where('ticketid',$tid)->pluck('id');
  if($status[0]=='Opened'){

         
         $esubject='New Ticket #'.$tid.' Opened by '.$raisedby[0].' ( '.$email[0].')'; 
         }
      
        else if($status[0]=='Completed'||$status[0]=='Processing'){
            $esubject='Ticket #'.$tid.' Completed by '.$completedby.'( '.$completedmail.')';


         }
          $messages=DB::table('tickets')->where('ticketid',$tid)->pluck('message');
         $subject=DB::table('tickets')->where('ticketid',$tid)->pluck('subject');
  
 $data=['data'=>$subject[0],'name'=>$raisedby[0],'email'=>$email[0],'tid'=>$tid,'esubject'=>$esubject,'messages'=>$messages[0],'status'=>$status[0],'id'=>$id[0]];
foreach($q as $admin){
$user['to']=$admin;

 Mail::send('mail',$data,function($messages) use ($user,$esubject){
$messages->to($user['to']);
$messages->subject($esubject);
});
}
DB::update("update tickets  set mailed = 1 where ticketid = '".$request->tid."'");
return 1;   

}


}

public  static function userRate(Request $request){

     $user= $request->email;
    // DB::
    
    $allrating=DB::table('tickets')->where('assignedto',$user)->pluck('rating')->toArray();
    
    $name=DB::table('users')->where('email',$user)->pluck('name');
    $allrating=DB::table('tickets')->where('assignedto',$user)->pluck('rating')->toArray();
   
    $time=DB::table('tickets')->where('assignedto',$user)->pluck('timetaken')->toArray();
    $role=DB::table('users')->where('email',$user)->pluck('role');
    $text=DB::table('lists')->where('id',$role)->pluck('Text');
    // $name=DB::table('users')->where('email',$user)->pluck('name');
//    return $name;
   
    $count=count($allrating);
    if($count==0){
        $sum="N/A";
    }
    else{
     $sum=array_sum($allrating)/$count;
     $sum=number_format($sum,2);
    }
    $data=[
        'sum'=>$sum,
        'name'=>$name,
        'text'=>$text,
        'user'=>$user
    ];
return view('rating')->with('data',$data);

}

public static function rating(){
$user=auth()->user()->email;
$allrating=DB::table('tickets')->where('assignedto',$user)->pluck('rating')->toArray();
$count=count($allrating);
if($count==0){
    $sum=" N/A";
}
else{
 $sum=array_sum($allrating)/$count;
 $sum=number_format($sum,2);
}
 return $sum;
}
public function rep(Request $request){

    return view('reports');
}
public function CloseTicket(Request $request){
    // return $request;
                        $patientname = $request->patientname;
        $requestid = $request->requestid ;
        $sampleid = $request->sampleid;
        $subject = $request->subject;
         $department = $request->department;
          $priority = $request->priority;
           $message = $request->message;
             $tid = $request->tid;
             $mid = $request->mid;
           $rating=$request->check;
           $time=$request->time;
           $satisfy=$request->satisfy;
           $comment=$request->comment;
           $user = auth()->user()->id;
            $email=auth()->user()->email;
           $date=Carbon::now()->format('Y-m-d H:i:s');
        //    return $date;

        $validated = $request->validate([
            'tid' => 'required',
            'mid' => 'required',
            'department' => 'required',
            'priority'=> 'required',
            'message' => 'required'
            
                                 
        ]);

     DB::insert('insert into ticketmessages (ticketid, username, mid, message, user, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?)', 
            [$request->tid, $email, $request->mid, $message, 'client', date('Y-m-d H:i:s'), $user ]); 
            DB::update("update tickets  set status = 'Closed' where ticketid = '".$request->tid."'");
   
         DB::update("update tickets  set closedat ='$date' where ticketid = '".$request->tid."'");

      $tid =DB::table('tickets')->where('ticketid',$request->tid)->get();
      return $tid[0]->id;

         
          

}


public function update(Request $req){
  
   $data=ticket::find($req->id);
   $file=ticket::find($req->id);
   $data->patientname=$req->patientname;
   $data->requestid=$req->requestid;
   $data->sampleid=$req->sampleid;
   $data->subject=$req->subject;
   $data->department=$req->department;
   $data->priority=$req->priority;
   $data->file=$req->file;
   $data->message=$req->message;
   $data->save();
   
}
public function deleteTicket(Request $request){
   

   DB::table('tickets')->where('ticketid',$request->id)->delete(); 
   DB::table('ticketattachments')->where('ticketid',$request->id)->delete(); 


}

public function assignTicketNow(Request $request){
   
 $user=auth()->user()->email;
 $role=auth()->user()->role;

 if($role>=4)
   return DB::update("
   update tickets  set status = 'Processing',
   assignedby='".$user."',

   assignedto = '".$request->user."',
    assignedat = '".date('Y-m-d H:i:s')."'
     where id = '".$request->tid."'"
    );
    
else if($role<=3){
    return  DB::update("
   update tickets  set status = 'Processing',
   assignedbyocm='".$user."',

   assignedto = '".$request->user."',
    assignedat = '".date('Y-m-d H:i:s')."'
     where id = '".$request->tid."'"
    );
}

}
public function Tickettype(Request $request){

}

public function tickets(Request $request){
   
    // $type=$request->type;
// return $type;
         if ($request->ajax()) {
                 $cr=0;
           
            $user = auth()->user();
$r=$user->role;
            if($user->role == 1) {

                $role = '';
            }
            else if($user->role== 0){
                $role="asfsg";
            }
            else if ($user->role==2){
                $role="Agent";
            }
            if($user->role>=4){
                $cr=6;
            }

if($request->type=="ALL"){
    if($user->role<=3){
    $data = DB::table('tickets')
    ->select(
          'tickets.*',
          DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
          )
          ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')

          ->Where('internal','2')
          ->orWhere('internal','1')
          ->get();
    // return $data;    
    }

    else{

        if($user->role>=6)
    {
        $loggedin=auth()->user()->email;
        $data = DB::table('tickets')
        ->select(
              'tickets.*',
              DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
              )
              ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')
              ->where('username',$loggedin)
              
              ->get();
    }





else{
        $data = DB::table('tickets')
        ->select(
              'tickets.*',
              DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
              )
              ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')
              
              
              ->get();

        }
            
    }



}

else{
    if($user->role<=3)
    {
                  $data = DB::table('tickets')
                  ->select(
                        'tickets.*',
                        DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
                        )
                        // ->when(!empty($type) , function ($query) use($request, $type){
                                        
                        //     return $query->where('status',$type);

                        // })
                        ->having('status',$request->type)
                        
                        ->orWhere('internal','2')
                        ->orWhere('internal','1')

                        ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')
                  ->get();
          
                        }
                        else{
                            if($user->role>=6)
                            {
                                $loggedin=auth()->user()->email;
                                $data = DB::table('tickets')
                                ->select(
                                      'tickets.*',
                                      DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
                                      )
                                      ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')
                                      ->having('status',$request->type)
                                      ->where('username',$loggedin)
                                      
                                      ->get();
                            }
                        
                        
                        
                        
              
                                
                                       
                            else{
                  $data = DB::table('tickets')
                  ->select(
                        'tickets.*',
                        DB::raw('(SELECT max(created_at) FROM ticketmessages WHERE tickets.ticketid = ticketmessages.ticketid order by created_at desc limit 1) as closed')
                        )
                        // ->when(!empty($type) , function ($query) use($request, $type){
                                        
                        //     return $query->where('status',$type);

                        // })
                        ->where('status',$request->type)
                       
                  ->leftjoin('ticketattachments', 'tickets.id' ,"=",'ticketattachments.id')
                  ->get();
                        }
                        }
                    }
            return Datatables::of($data)
                    
            
            ->addIndexColumn()


                     ->editColumn('priority', function($row){ 

                     if($row->priority == 'Low') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-info">'.$row->priority.'</span>';


                     } elseif($row->priority == 'Medium') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-primary">'.$row->priority.'</span>';

                     } 
                     elseif($row->priority == 'High') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-warning">'.$row->priority.'</span>';

                     } 

                      elseif($row->priority == 'Critical') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-danger">'.$row->priority.'</span>';

                     } 

                                

                        })


                  ->editColumn('internal', function($row){ 

                     if($row->internal == null) {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-primary">Internal</span>';


                     } else if($row->internal == 1) {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-success">OCM Support</span>';

                     } 
                     else if($row->internal == 2) {

                        return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-info">NA Support</span>';

                    } 
                    }) 


                     ->editColumn('status', function($row){ 
                                               
                     if($row->status == 'Opened') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-primary">'.$row->status.'</span>';


                     } elseif($row->status == 'Processing') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-warning">'.$row->status.'</span>';

                     } 
                     elseif($row->status == 'Closed') {

                         return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-success">'.$row->status.'</span>';

                     } 
                     elseif($row->status == 'Completed') {

                        return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-info">'.$row->status.'</span>';

                    } 

                                

                        })

                    ->addColumn('action', function($row){
                    
                        $k=DB::table('ticketattachments')->select('filename','ticketid')->having('ticketid',"=",$row->ticketid)
                        ->where('filename',"LIKE",'%.mov')
                        ->orWhere('filename',"LIKE",'%.mp4')
                        ->orWhere('filename',"LIKE",'%.mpg')
                        ->orWhere('filename',"LIKE",'%.webj')
                        ->orWhere('filename',"LIKE",'%.flv')
                        ->first();
                   
                        $ls=DB::table('ticketattachments')->select('filename','ticketid')->having('ticketid',"=",$row->ticketid)
                        ->where('filename',"LIKE",'%.jpg')
                        ->orWhere('filename',"LIKE",'%.png')
                        ->orWhere('filename',"LIKE",'%.docx')
                        ->orWhere('filename',"LIKE",'%.xls')
                        ->orWhere('filename',"LIKE",'%.pdf')
                        ->orWhere('filename',"LIKE",'%.gif')
                        ->orWhere('filename',"LIKE",'%.jpeg')

                        ->orWhere('filename',"LIKE",'%.zip')
                        ->first();

                       $btn = '
                                <div class="btn-group" role="group" aria-label="Basic example">
                                ';

                                if($row->status != 'Closed' && $row->status != 'Completed') {

                                           $user = auth()->user();
                                           
                                        if($user->role ==4||$user->role ==1||$user->role==5) {
                                            

                                            $btn .= ' <button type="button" id="'.$row->id.'" title="Assign to User" class="btn btn-primary assign">
                                                 <i class="fas fa-user-plus"></i>
                                                </button>';
                                        }
                                        
                                 }
                                 if($row->status == 'Closed'&&$row->rating!='') {

                                    $user = auth()->user();
                                    
                                 if($user->email ==$row->username) {
                                     if($row->rating<=2&&$row->rating>=1)
{
                                     $btn .= ' <button type="button" id="'.$row->ticketid.'" class="btn btn-danger ">
                                          <i class="fas fa-star"></i>
                                         </button>';
                                 }



                                 if($row->rating>2&&$row->rating<=4)
                                 {
                                                                      $btn .= ' <button type="button" id="'.$row->ticketid.'" class="btn btn-warning ">
                                                                           <i class="fas fa-star"></i>
                                                                          </button>';
                                                                  }

                                                                  if($row->rating>4)
                                                                  {
                                                                                                       $btn .= ' <button type="button" id="'.$row->ticketid.'" class="btn btn-success ">
                                                                                                            <i class="fas fa-star"></i>
                                                                                                           </button>';
                                                                                                   }
                                                                 
                                }
                          }
                          if($row->status == 'Closed'&&$row->rating==''){
                            $user = auth()->user();
                                    
                            if($user->email ==$row->username) 
                               
{
                                $btn .= ' <button type="button" id="'.$row->ticketid.'" class="btn btn-primary rates">
                                     <i class="far fa-star"></i>
                                    </button>';
}
                          }
                              
                          
                             if($k){
                                $btn .=  '<a class=" btn btn-warning">
                                <i class="fas fa-video"></i>
                                </a>
                                '; 
                             }
                             if($ls){
                                $btn .=  '<a class=" btn btn-dark">
                                <i class="fas fa-paperclip"></i>
                                </a>
                                '; 
                             }
                        
                          
                               $btn .=  '<a  href="../TicketView/'.$row->id.'" title="View Ticket" class="btn btn-info update">
                                 <i class="fas fa-eye"></i>
                                </a>
         </div>

                                <a href="../Ticket/'.$row->id.'" title="Edit User" class="btn btn-primary update d-none">
                                 <i class="bx bx-edit"></i>
                                </a>
                                <button id="'.$row->ticketid.'" type="button" class="d-none btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                  ';
    

                            return $btn;
                             
                                                         }                   )

                    ->addColumn('resolved', function($row){
     
                                        if($row->status == 'Closed') {

                                        $start  = new Carbon($row->requestedat);
                                        $end    = new Carbon($row->closed);

                                        if($start->diff($end)->format('%D') > 1) {

                                            return $start->diff($end)->format('%D days %H hours');   
                                        }
                                        elseif($start->diff($end)->format('%H') > 1) {

                                            return $start->diff($end)->format('%H hours %I mins');   
                                        } 
                                        else {

                                            return $start->diff($end)->format('%I mins');  
                                        }
                                        }
                                        
                                                                     
                    })


                    

                    ->setRowId('id')
                    ->rawColumns(['action','priority','internal','status'])
                    ->make(true);

                   
                 
        }

                // $users=DB::table('users')->get(); 
                // $use=DB::table('users')->whererole(0)->get(); 
                
                
                // $data = [

                //          'users' => $users,
                //          'use' => $use

                // ];           

        // return view('tickets')->with('data', $data);  
    
                 
$data=DB::table('users')->where('role','<>','1')->get(); 
    
$data2=DB::table('users')->whereIn('role',[1,2,3])->get();
$data3=DB::table('users')->where('role','=','5')
->orWhere('role','=','4')
->get();

// return view('tickets')->with('data', $data);  

return view('tickets', [

    'data'=>$data,
    'data2'=>$data2,
    'data3'=>$data3
    
]);  




}
public function rateNow(Request $request){
    if($request->ajax()){
    $tid = $request->tid;
    // $mid = $request->mid;
  $rating=$request->check;
  $time=$request->time;
  $satisfy=$request->satisfy;
  $comment=$request->comment;
 return DB::update("update tickets  set  rating='".$rating."',time='".$time."',satisfy='".$satisfy."',comments='".$comment."' where ticketid = '".$request->tid."'");
   
    // return $request;
    }
  
    }
    public function Error(){
    return view('error');
}

}



