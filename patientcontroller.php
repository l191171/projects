<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use DB;

class patientcontroller extends Controller
{
    //
    // public function display(){
    //     $data=array();
    //     $data=patientdetails::all();
    //     return view("home",compact('data'));
    // }

    public function getone($id){
        // $rec=patientdetails::find($id);
        //$data = patientdetails::where('SampleID',$id)->first(); 
        
        //$data = patientdetails::wheresampleid($id)->first();
        
        // $products = btproducts::wheresampleid($id)->get();
//         $data=patientdetails::select('patientdetails.*')
// ->join('btproducts', 'btproducts.SampleID', '=', 'patientdetails.sampleid')
// ->join('btaddons', 'btaddons.', '=', 'posts.id');
     
        // $data = DB::table('btproducts')
        // ->join('patientdetails', 'patientdetails.SampleID', '=','btproducts.sampleid')
    
     
        // ->select('patientdetails.*','btproducts.*' )
        // ->get();



        
        // $d= DB::table('btproducts')
        // ->join('patientdetails', 'patientdetails.SampleID', '=','btproducts.sampleid','left outer')
    
     
        // ->select('patientdetails.fgroup','btproducts.*' )

        // // ->where('btproducts.sampleid','=', DB::raw("'".$id."'"))
        // ->get();
 

        //most correct
        // $d = DB::table('btproducts')
       
     $patientdetails = DB::table('patientdetails')
        ->where('patnum',$id)
        ->orderBy('DateTime','desc')
        ->limit(1)
         ->get();


         $sampleids = DB::table('patientdetails')
        ->where('patnum',$id)
         ->pluck('sampleid');


        //  $kleihauer = DB::table('kleihauer')
        // ->whereIn('sampleid',$sampleids)
        // ->orderBy('DateTime','desc')
        //  ->get();


     $btproducts = DB::table('btproducts')
        ->join('btaddons', 'btaddons.id', '=','btproducts.pid')
    
     
        ->select('btaddons.name','btproducts.*' )
        ->whereIn('sampleid',$sampleids)
        ->get();

        $data = [

                'patientdetails' => $patientdetails,
                'sampleids' => $sampleids,
                'btproducts' => $btproducts
                
            ];

        return view('patienthistorybt')->with('data',$data);
     
        // ->select('btproducts.unitnumber','btproducts.status','patientdetails.fgroup' )
        // ->where('btproducts.sampleid','=', DB::raw("'".$id."'"))
       
        // ->get();




        

    //     $data = patientdetails::whereSampleID( $id)
    // ->leftJoin('btproducts', 'patientdetails.SampleID', '=', 'btproducts.sampleid')
    // ->select('patientdetails.*','btproducts.*')->get();
        

        // $data = patientdetails::join('btproducts', 'patientdetails.SampleID', '=', 'btproducts.Sampleid')
        //        ->get(['patientdetails.*', 'btproducts.*']);





        // return view("single",compact('data','d'));
        // //return view('single',compact('data'));
    }
}
