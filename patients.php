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

class patients extends Controller
{   

    


 public function getChartResults(Request $request)
    {  

        $mrn = $request->mrn;
        $code = $request->code;
        $date1 = $request->date1;
        $date2 = $request->date2;



             $results = DB::table('results')
                ->select('sampleid','result','RunTime')
                ->whereBetween('RunTime',[$date1, $date2])
                ->where('patient',$mrn)
                ->where('code',$code)
                ->groupby('sampleid')
                ->orderBy('sampleid','desc')
                ->get();


                $range = DB::table('results')
                ->select('NormalLow','NormalHigh')
                ->where('patient',$mrn)
                ->where('code',$code)
                ->where('NormalLow','!=',null)
                ->where('NormalHigh','!=',null)
                ->orderBy('sampleid','desc')
                ->limit(1)
                ->get();

                if($range[0]->NormalLow == '' || $range[0]->NormalLow == null) {

                   $NormalLow = 0; 
                } 
                else {

                    $NormalLow = $range[0]->NormalLow; 
                }


                if($range[0]->NormalHigh == '' || $range[0]->NormalHigh == null) {

                   $NormalHigh = 0; 
                } 
                else {

                    $NormalHigh = $range[0]->NormalHigh; 
                }

                foreach($results as $result) {


                        $labels[] = $result->sampleid;

                        if($result->result == null || $result->result == '') {

                            $result = 0;
                        } else {

                            $result = $result->result;
                        }
                        $values[] = $result;
                }


          $data = [

                    'labels' => $labels,
                    'values' => $values,
                    'NormalLow' => $NormalLow,
                    'NormalHigh' => $NormalHigh

          ]; 

        return view ('layouts.patientchart')->with('data',$data);


    }     

    public function getPatientHistory(Request $request)
    {  

        $mrn = $request->mrn;
        $date1 = $request->date1;
        $date2 = $request->date2;
        $department = $request->department;



        $samplesinfo = DB::table('results')->where('results.patient',$mrn)
                ->select('results.sampleid','ocmphlebotomy.PhlebotomySampleDateTime')
                ->leftjoin('ocmphlebotomy', 'ocmphlebotomy.PhlebotomySampleID', '=', 'results.sampleid')
                ->whereBetween('results.RunTime',[$date1, $date2])
                ->groupby('results.sampleid')
                ->orderBy('ocmphlebotomy.PhlebotomySampleID','desc')
                ->when(!empty($department) , function ($query) use($request){
                return $query->where('results.department',$department);
                })
                ->get();  


         $codes = DB::table('results')->where('patient',$mrn)
                ->select('Code')
                ->whereBetween('RunTime',[$date1, $date2])
                ->groupby('Code')
                ->when(!empty($department) , function ($query) use($request){
                return $query->where('results.department',$department);
                })
                ->get();


          $flags = DB::table('results')->where('patient',$mrn)
                ->select('Flags')
                ->whereBetween('RunTime',[$date1, $date2])
                ->whereIn('Flags',['H','L'])
                ->when(!empty($department) , function ($query) use($request){
                return $query->where('results.department',$department);
                })
                ->get();       


           $data = [

                    'codes' => $codes,
                    'samplesinfo' => $samplesinfo,
                    'flags' => count($flags)

          ]; 
                
        
          return view ('layouts.patienthistorylist')->with('data',$data);
    }



    public static function GetResult($code='',$sampleid='',$mrn='')
        
        {       

               $results = DB::table('results')
                ->select('results.result','results.Flags')
                ->where('results.patient',$mrn)
                ->where('results.sampleid',$sampleid)
                ->where('results.Code',$code)
                ->get();  

                if(count($results) > 0) {

                        return $results[0]->result;
                 }


        }


        public static function GetResult2($code='',$sampleid='',$mrn='')
        
        {       

               $results = DB::table('results')
                ->select('results.result','results.Flags')
                ->where('results.patient',$mrn)
                ->where('results.sampleid',$sampleid)
                ->where('results.Code',$code)
                ->get();  

                if(count($results) > 0) {

                       return $results[0]->Flags;
                }
        }




    public function PatientHistory(Request $request)
    {   


          if((\App\Http\Controllers\users::roleCheck('Patient History Chart','View',0)) == 'No')  {

                 return redirect('/home');
             }   

        $mrn = $request->id;
        
        $departments = DB::table('Lists')->where('ListType','DPT')->where('Text','!=','Trans')->get();
        $patient = DB::table('patientifs')->where('id',$mrn)->get();
        $now = Carbon::now();

        $date1 = Carbon::now()->subDay(35);
        $date2 = Carbon::now();

        $date1 =  $date1->format('Y-m-d'); 
        $date2 =  $date2->format('Y-m-d'); 

       

     $data = [

                    'date1' => $date1,
                    'date2' => $date2,
                    'patient' => $patient,
                    'departments' => $departments


          ];  

        
        return view ('patienthistory')->with('data',$data);
    } 


    public function index(Request $request)
    {   

                 
          $segment =  $request->List;  


            if($segment == 'All') {

                    if((\App\Http\Controllers\users::roleCheck('Patients','View',0)) == 'No')   
                    { return redirect('/home');}  
            } 
            elseif($segment == 'My') { 

                    if((\App\Http\Controllers\users::roleCheck('My Patients','View',0)) == 'No')   
                    { return redirect('/home');}  
            }


              


         if ($request->ajax()) {
            
         

            $segment =  $request->List;  


           
            $user = auth()->user();


        
          $myPatients = DB::table('ocmrequest')->where('RequestCreatedBy',$user->id)
                    ->orderBy('ReqestID','desc')->limit(10)->pluck('RequestPatientID');
                          


            $ocmrequest = DB::table('ocmrequest')->where('RequestCreatedBy',$user->id)
            ->orderBy('ReqestID','desc')->limit(10)->pluck('RequestPatientID');

          
             
            if(count($ocmrequest) == 0) {

               $id = 0;

            }    else {

                 $result = array(); 
                   foreach ($ocmrequest as $value){
                      if(!in_array($value, $result))
                        $result[]=$value;
                    }
                  
                  sort($result, SORT_NUMERIC); 
                 $id = implode(",",$result);

            }

            $data = DB::table('PatientIFs') 
                            ->select(
                                    'PatientIFs.*',
                                    'Wards.Text as Wards',
                                    'Clinicians.Text as Clinicians',
                                
                                )
                            ->leftjoin('Wards', 'Wards.id', '=', 'PatientIFs.Ward')
                            ->leftjoin('Clinicians', 'Clinicians.id', '=', 'PatientIFs.Clinician')
                           
                              


                              ->when(!empty($request->mrn) , function ($query) use($request){
                            
                           
                                 return $query->where('patientifs.Chart',$request->mrn);
                            
                             }) 


                              ->when(!empty($request->clinician) , function ($query) use($request){
                            
                           
                                 return $query->where('patientifs.Clinician',$request->clinician);
                            
                             }) 


                              ->when(!empty($request->ward) , function ($query) use($request){
                            
                           
                                 return $query->where('patientifs.Ward',$request->ward);
                            
                             })  


                             ->when(!empty($request) , function ($query) use($myPatients,$segment){
                                         
                                  
                                         if($segment == 'My') {
                                          return $query->whereIn('PatientIFs.id',$myPatients);
                                            }

                                     })

                            ->orderByRaw("FIELD(PatientIFs.id , $id) Desc");

                                  


            return Datatables::of($data)

                    ->addIndexColumn()

                    ->editColumn('PatName', function($row){ 

                     

                            return '<a href="'.route('Requests').'/All/'.$row->id.'" class="text-primary">'.$row->PatName.'</a>';

                       

                     })

                    ->editColumn('Clinicians', function($row){ 

                     

                            return '<button id="'.$row->Clinician.'" class="text-left text-primary clinicianID" style="border:0px;background:none;">'.$row->Clinicians.'</button>';

                       

                     })

                    ->editColumn('Wards', function($row){ 

                     

                            return '<button id="'.$row->Ward.'" class="text-left text-primary wardID" style="border:0px;background:none;">'.$row->Wards.'</button>';

                       

                     })


                    ->addColumn('action', function($row){
     
                           $btn = '
                                
                                <div class="btn-group" role="group">
                                 <button type="button" id="'.$row->id.'" title="Add New Request" class="addRequest btn btn-warning"><i class="fas fa-plus"></i>
                                </button>



                                 <a href="../PatientHistory/'.$row->id.'" title="Patient History" class="PatientHistory btn btn-primary">
                                 <i class="fas fa-chart-area"></i>
                                </a>


                                 <a href="../PatientHistoryBT/'.$row->Chart.'" title="Product History" class="PatientHistoryBT btn btn-danger">
                                 <i class="fas fa-chart-line"></i>
                                </a>

                                 <a href="../Requests/All/'.$row->id.'" title="Requests List" class="btn btn-info"><i class="fas fa-th-list"></i>
                                </a>


                                 </div>
                                  ';
    
                            return $btn;
                    }) 

                    ->setRowId('id')
                    ->rawColumns(['action','PatName','Wards','Clinicians'])
                    ->make(true);

                    
                  
        }


        $clinicians = DB::table('clinicians')->orderby('Text')->get();
        $wards = DB::table('wards')->orderby('Text')->get();


        $data = [

                    'clinicians' => $clinicians,
                    'wards' => $wards


          ];  

        
        return view ('patients')->with('data',$data);

        
    }



    


}