<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use App\Models\ticketattachment;
use App\Models\ticketmessages;
use DB;
class ticketcontroller extends Controller
{
    //
    public function show()
    {
            return ticket::all();
    }
    public function store(Request $request)
    {
        $request->validate([


            'requestid'=>'required',
            'sampleid'=>'required',
            'subject'=>'required',
            'department'=>'required',
            'priority'=>'required',
            'message'=>'required',
            'ticketid'=>'required',
            
        ]);
        return ticket::create($request->all());
       
    
    
    }




    public function getattachments(){
            return ticketattachment::all();  
    }

    public function insertattachments( Request $request){

        
       
        
             
            // your code here.
            // You can access each item's data like: $data->id, $data->user etc.


            // $newCart = new ticketattachment();
            // // $newCart->user_id = Auth::id();
            // $newCart->ticketid = $request->ticketid;
            // $newCart->filename = $request->filename;
            // $newCart->datetime = $request->datetime;
            // $newCart->mid = $request->mid;
            // $newCart->save();
        DB::insert('insert into ticketattachments (ticketid, filename, datetime ,mid) values (?, ?, ?, ?)',
        [$request->ticketid,$request->filename,$request->datetime, $request->mid ]);
        
    
        return response()->json('tickets Successfully Updated',200);
    }


public function getmessages(){
    return ticketmessages::all();
}
public function insertmessages(Request $request){


    DB::insert('insert into ticketmessages (ticketid, username,mid,message,user) values (?, ?, ?, ?,?)',
    [$request->ticketid,$request->username, $request->mid,$request->message,$request->user ]);
    return response()->json('ticketmessage Successfully Updated',200);
}
public function getall($id){
    
$ticketattachments =DB::table('ticketattachments')->where('ticketid', $id)->get();
$ticketmessages =DB::table('ticketmessages')->where('ticketid', $id)->get();
$tickets =DB::table('tickets')->where('ticketid', $id)->get();

$data=[

    'ticketattachments'=>$ticketattachments,
    'ticketmessages'=>$ticketmessages,
    'tickets'=>$tickets
];
return $data;

}


public function saveimages(Request $request){

    $url_to_image=$request->url;
    // $url_to_image='http://www.google.co.in/intl/en_com/images/srpr/logo1w.png';
    $my_save_dir='images/';
    $filename=basename($url_to_image);
    $comp=$my_save_dir.$filename;
    file_put_contents($comp,file_get_contents($url_to_image));

    return response()->json('tickets Successfully Updated',200);
}


public function uploadimages(Request $request){
    // return $result->getClientOriginalName();
$image=$request->file('file');
    // $re=$result->getClientOriginalName()->store('public');
            $new_name=$image->getClientOriginalName();
            $image->move(public_path(),$new_name);
            return response()->json("file Uploaded".$new_name );   
    

}

}
