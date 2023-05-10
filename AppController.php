<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;

class AppController extends Controller
{
    
    public function search(Request $request){
      
        $search_text =$request->tid;
        $date =$request->date;
        $ulist =$request->ulist;
        $priority =$request->upriority;
        $status =$request->ustatus;
        $udepartment =$request->udepartment;
        $usubject =$request->usubject;
        
       if($search_text!=""||$date!=""||$ulist!=""||$priority!=""||$status!=""||$udepartment!=""||$usubject!=""){
       
            $products = ticket::where('ticketid', '=', $search_text)
            ->get();    
 
        return view('search',compact('products'));
    }
    else{
        return view('report');
    }
//     public function data(){
//         $search_text = $_GET['search'];
//        if($search_text!=""){
//         $products = Apps::where('name','LIKE','%'.$search_text.'%')->get();
//         return view('data',compact('products'));
//     }

// }
}}