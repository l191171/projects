<?php
     
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use App\Models\ticketattachment;
use Validator;
use DB;
Use Carbon\Carbon;
use DateTime;
use Datatables;

class home extends Controller
{
    

    public function __construct()
    {
        $this->middleware('auth');
    }

     public function updateThemeInfo(Request $request)
    {

          if($request->limit == 'all') {

            $sql = DB::update("update users  set 

                            colorscheme = '".substr($request->colorscheme, 1)."',
                            font = '".$request->font."',
                            font_link = '".$request->font_link."',
                            font_weight = '".$request->font_weight."',
                            resolution = '".$request->resolution."' ");   

             return response()->json(['success'=>'Changes applied to all users.']);


          } else {

            $user = auth()->user();
            $sql = DB::update("update users  set 

                            colorscheme = '".substr($request->colorscheme, 1)."',
                            font = '".$request->font."',
                            font_link = '".$request->font_link."',
                            font_weight = '".$request->font_weight."',
                            resolution = '".$request->resolution."'

                            where id = '".$user->id."' "); 

              return response()->json(['success'=>'Changes applied successfully.']);                                  

          }
          

           

    }


      

    public function index()
    {
        $role=Auth()->user()->role;
        if($role<=3){
          $ticketsThisWeek =  DB::table('tickets')
                                    ->where('status','Opened')
                                    ->whereIn('internal',[1,2])
                                    
                                      ->count();

          $ticketsProcessing =  DB::table('tickets')
                                       
                                        ->where('status','Processing')
                                        ->whereIn('internal',[1,2])
                                        ->count();

         $ticketsClosedThisWeek =  DB::table('tickets')
                                       ->whereBetween('closedat', 
                                            [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                                        ) 
                                        ->where('status','Closed') 
                                        ->whereIn('internal',[1,2])
                                        ->count();

        $ticketsCompletedThisWeek =  DB::table('tickets')
                                        ->whereBetween('completedat', 
                                             [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                                         ) 
                                         ->where('status','Completed') 
                                         ->whereIn('internal',[1,2])
                                         ->count();
 

        }elseif($role==4 ||$role==5){
            $ticketsThisWeek =  DB::table('tickets')
            ->where('status','Opened')
            
            
              ->count();

$ticketsProcessing =  DB::table('tickets')
               
                ->where('status','Processing')
               
                ->count();

$ticketsClosedThisWeek =  DB::table('tickets')
               ->whereBetween('closedat', 
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                ) 
                ->where('status','Closed') 
                
                ->count();

$ticketsCompletedThisWeek =  DB::table('tickets')
                ->whereBetween('completedat', 
                     [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                 ) 
                 ->where('status','Completed') 
                
                 ->count();

        }else{
             $email=Auth()->user()->email;
            $ticketsThisWeek =  DB::table('tickets')
            ->where('status','Opened')
            ->where('username',$email)
            
              ->count();

$ticketsProcessing =  DB::table('tickets')
               
                ->where('status','Processing')
                ->where('username',$email)
                ->count();

$ticketsClosedThisWeek =  DB::table('tickets')
               ->whereBetween('closedat', 
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                ) 
                ->where('status','Closed') 
                ->where('username',$email)
                ->count();

$ticketsCompletedThisWeek =  DB::table('tickets')
                ->whereBetween('completedat', 
                     [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                 ) 
                 ->where('status','Completed') 
                 ->where('username',$email)
                 ->count();
        }
    

         
         $data = [

                'ticketsThisWeek' => $ticketsThisWeek,
                'ticketsProcessing' => $ticketsProcessing,
                'ticketsClosedThisWeek' => $ticketsClosedThisWeek,
                'ticketsCompletedThisWeek'=>$ticketsCompletedThisWeek
         ];



         return view ('home')->with('data',$data);
    }

    public function getTicketsReport(Request $request)
    {

             $duration =  $request->duration;  

                $now = Carbon::now();
                


                if($duration == 'This Week') {


                    $start = $now->startOfWeek()->format('d-m-Y');
                    $end = $now->endOfWeek()->format('d-m-Y'); 
                    $labels = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');    
                

                } elseif($duration == 'Last Week') {

                    $start = $now->startOfWeek()->subWeek()->format('d-m-Y');
                    $end = $now->endOfWeek()->format('d-m-Y');
                    $labels = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
                    
                }


                elseif($duration == 'This Month') {

                    $start = $now->format('Y-m-1');
                    $end = $now->format('Y-m-t');
                    $labels = array();
                    
                }

                elseif($duration == 'Last Month') {

                    $start = $now->subMonth()->format('Y-m-1');
                    $end = $now->format('Y-m-t');
                    $labels = array();
                    
                }

                


                $start = new DateTime( $start );
                $end   = new DateTime( $end );    


                $sr = 0;
                $sr2 = 0;



                for($i = $start; $i <= $end; $i->modify('+1 day')) {


                       $date = $i->format("Y-m-d"); 

                        if($duration == 'This Month') {

                            $labels[] = $date;

                        }
                        elseif($duration == 'Last Month') {

                            $labels[] = $date;

                        }
                         $role=auth()->user()->role;
                         if($role==4||$role==5){
                   
                        $values[]= DB::table('tickets')->select('ticketid')
                        ->where([
                            ['created_at','like',$date.'%'],
                          ['status','Opened'] 
                          
                          ]) 
                                              ->count();

                        $values2[] =  DB::table('tickets')
                                              ->whereDate('created_at','like',$date.'%')
                                              ->where('status','Processing') 
                                              ->count();

                        $values3[] =  DB::table('tickets')
                        ->where([
                            ['closedat','like',$date.'%'],
                          ['status','Closed'] 
                          
                          ])
                                              ->count();                                                                            
                       
                        $values4[] =  DB::table('tickets')
                                              ->where([
                                                ['completedat','like',$date.'%'],
                                              ['status','Completed'] 
                                              
                                              ])
                                              ->count();                                                                            
                       
                            
                                              }else if($role<=3){
                                                $values[]= DB::table('tickets')->select('ticketid')
                                                ->where([
                                                    ['created_at','like',$date.'%'],
                                                  ['status','Opened'] 
                                                  
                                                  ]) 
                                                  ->whereIn('internal',[1,2])
                                                                      ->count();
                        
                                                $values2[] =  DB::table('tickets')
                                                                      ->whereDate('created_at','like',$date.'%')
                                                                      ->where('status','Processing') 
                                                                      ->count();
                        
                                                $values3[] =  DB::table('tickets')
                                                ->where([
                                                    ['closedat','like',$date.'%'],
                                                  ['status','Closed'] 
                                                  
                                                  ])
                                                  ->whereIn('internal',[1,2])
                                                                      ->count();                                                                            
                                               
                                                $values4[] =  DB::table('tickets')
                                                                      ->where([
                                                                        ['completedat','like',$date.'%'],
                                                                      ['status','Completed'] 
                                                                      
                                                                      ])
                                                                      ->whereIn('internal',[1,2])
                                                                      ->count();                                                                            
                                               
                                              }
                                              else{
                                                $email=Auth()->user()->email;
                                                $values[]= DB::table('tickets')->select('ticketid')
                                                ->where([
                                                    ['created_at','like',$date.'%'],
                                                  ['status','Opened'] 
                                                  
                                                  ]) 
                                                  ->where('username',$email)
                                                                      ->count();
                        
                                                $values2[] =  DB::table('tickets')
                                                                      ->whereDate('created_at','like',$date.'%')
                                                                      ->where('status','Processing') 
                                                                      ->where('username',$email)
                                                                      ->count();
                        
                                                $values3[] =  DB::table('tickets')
                                                ->where([
                                                    ['closedat','like',$date.'%'],
                                                  ['status','Closed'] 
                                                  
                                                  ])
                                                  ->where('username',$email)
                                                                      ->count();                                                                            
                                               
                                                $values4[] =  DB::table('tickets')
                                                                      ->where([
                                                                        ['completedat','like',$date.'%'],
                                                                      ['status','Completed'] 
                                                                      
                                                                      ])
                                                                      ->where('username',$email)
                                                                      ->count();                                                                            
                                               

                                              }
                        
                } 
                
             $data = [

                'labels' => $labels,
                'values' => $values,
                'values2' => $values2,
                'values3' => $values3,
                'values4' => $values4
         ];



             return \Response::json($data);  
             


    }    

   


}