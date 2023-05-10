<?php
  
namespace App\Http\Controllers;

use App;  
use Illuminate\Http\Request;
use App\Models\OCMRequest;
use App\Models\Results;
use App\Models\OCMRequestDetails;
use App\Models\OCMRequestQuestionsDetails;
use App\Models\OCMRequestTestDetails;
use DataTables;
use Validator;
use DB;
use Auth;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;  
use PDF;
use DateTime;
use DateInterval;


class requests extends Controller
{   

            public $CellNumber;
            public $AccountKey;
            public $MessageBody;

      public function viewRequest(Request $request)
    {
            
          
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    'Clinics.name as clinic', 
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $DoB = Carbon::parse($OCMRequest[0]->DoB)->diff(Carbon::now())->y;     
          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d H:i A');

                  
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                       ->select('testprofiles.name','OCMRequestDetails.TestDescription') 
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

          $OCMPhlebotomies = DB::table('OCMPhlebotomy')
                       ->select('OCMPhlebotomy.PhlebotomySampleID')  
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)->get(); 

          $sampleIDs = array();

          foreach($OCMPhlebotomies as $OCMPhlebotomy) {

                $sampleIDs[] = $num = intval($OCMPhlebotomy->PhlebotomySampleID);

          }                
          

          $requestInformation = DB::table('OCMPhlebotomy')
                          ->select('OCMPhlebotomy.*',
                            DB::raw('(SELECT RunTime FROM results WHERE results.sampleid = OCMPhlebotomy.PhlebotomySampleID order by RunTime desc limit 1) as RunTime')
                                )
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)->get();  

           $ocmrequestquestionsdetails = DB::table('ocmrequestquestionsdetails') 
                          ->where('request', $request->rid)
                          ->where('episode', $request->eid)->get(); 


           $btrequestquestions = DB::table('btrequestquestions') 
                          ->where('rid', $request->rid)
                          ->where('eid', $request->eid)->get(); 


         
                      
        

          $results = DB::table('results')
                       ->select(
                                'results.Code',
                                'results.sampleid as PhlebotomySampleID',
                                'TestDefinitions.longname as test',
                                'Lists.Text as department',
                                'results.Code as code',
                                'results.result',
                                'results.SignOff',
                                'results.RunTime',
                                'results.Flags',
                                'results.Units',
                                'results.external',
                                'results.Analyser',
                                'results.NormalLow',
                                'results.NormalHigh',
                                'results.Comment as netComments',
                                'users.name as SignOffBy',
                                'results.SignOffDateTime'
                            )
                          ->leftjoin('TestDefinitions', 'TestDefinitions.shortname', '=', 'results.Code')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->leftjoin('users', 'users.id', '=', 'results.SignOffBy')
                          ->where('results.request', $request->rid)
                          ->where('results.episode', $request->eid)
                        
                          ->orderBy('results.id')
                          ->orderBy('results.department')
                          ->get();

             $requested = '';
            
            if(count($results) == 0) {

              $requested = DB::table('ocmrequesttestsdetails')
                       ->select(
                                'TestDefinitions.shortname as test',
                                'ocmrequesttestsdetails.sampleid',
                                'Lists.Text as department',
                                'testprofiles.name as profile'
                            )  
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ocmrequesttestsdetails.profileID')
                          ->leftjoin('Lists', 'Lists.id', '=', 'ocmrequesttestsdetails.department')
                          ->leftjoin('TestDefinitions', 'TestDefinitions.id', '=', 'ocmrequesttestsdetails.test')
                          ->where('ocmrequesttestsdetails.request', $request->rid)
                          ->where('ocmrequesttestsdetails.episode', $request->eid)
                          ->get(); 
            }               


           $departmentsInfo = DB::table('results') 
                          ->leftjoin('TestDefinitions', 'TestDefinitions.shortname', '=', 'results.Code')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->whereIn('results.sampleid',$sampleIDs)
                          ->groupBy('results.Code')
                          ->orderBy('results.id')
                          ->pluck('Lists.Text'); 

            $requestedDepartments = [];

            if(count($results) == 0) {

            $requestedDepartments = DB::table('ocmrequesttestsdetails') 
                          ->leftjoin('Lists', 'Lists.id', '=', 'ocmrequesttestsdetails.department')
                          ->where('ocmrequesttestsdetails.request', $request->rid)
                          ->where('ocmrequesttestsdetails.episode', $request->eid)
                          ->groupBy('ocmrequesttestsdetails.department')
                          ->orderBy('ocmrequesttestsdetails.id')
                          ->pluck('Lists.Text');

                }                                                     

         $testsInfo = DB::table('results') 
                          ->leftjoin('TestDefinitions', 'TestDefinitions.shortname', '=', 'results.Code')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->whereIn('results.sampleid',$sampleIDs)
                          ->groupBy('results.Code')
                          ->orderBy('results.id')
                          ->pluck('TestDefinitions.id'); 




              $testsInfo = DB::table('OCMRequestTestsDetails') 
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'OCMRequestTestsDetails.profileID')
                          ->whereIn('OCMRequestTestsDetails.test',$testsInfo) 
                          ->where('OCMRequestTestsDetails.request', $request->rid)
                          ->where('OCMRequestTestsDetails.episode', $request->eid)->pluck('testprofiles.name'); 

                          foreach($testsInfo as $testsInf) {


                            if($testsInf == 'FBC') {

                        $moretests = DB::table('results') 
                          ->where('results.request', $request->rid)
                          ->where('results.episode', $request->eid)
                          ->where('results.fbc', 1)->pluck('Code'); 

                                foreach($moretests as $moretest) {

                                    $testsInfo[] = 'FBC';

                                }

                            }


                             if($testsInf == 'UE') {

                        $moretests = DB::table('results') 
                          ->where('results.request', $request->rid)
                          ->where('results.episode', $request->eid)
                          ->where('results.egfr', 1)->pluck('Code'); 

                                foreach($moretests as $moretest) {

                                    $testsInfo[] = 'UE';

                                }

                            }


                          } 


               

             $users = DB::table('users')->get();                                         

        
         if($OCMRequest[0]->RequestType != 'BTRequest') {

             
            $connectionInfo_hq = array("Database"=>"CavanTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
            $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);
            
            if( $conn_hq ) {
          
            $sampleid_ = $requestInformation[0]->PhlebotomySampleID;
      

          DB::table('batchproducts')->where('SampleID',$sampleid_)->delete();

        $params = array();
            $options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );

       
                  $sql="select * from ocmRequestDetails where SampleID = '$sampleid_'  ";
                    
                    $res=sqlsrv_query($conn_hq,$sql, $params, $options );
                    $count1 = sqlsrv_num_rows($res);

                    if($count1 > 0) {
                        while ($rows4 = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                            $Identifier1 =  $rows4['indentifier'];
                            $uid =  $rows4['uid'];


                            $qql="select * from ocmbtproductsissued where uid= '$uid'";
                            $conn3=sqlsrv_query($conn_hq,$qql, $params, $options );
                            $count3 = sqlsrv_num_rows($conn3);
        
                            if($count3 > 0){

                     
                                while ($rows3 = sqlsrv_fetch_array($conn3, SQLSRV_FETCH_ASSOC)) {

                                    $identifier1 =  $rows3['identifier'];

                    $qql="select * from BatchProducts where Identifier= '$identifier1'";
                    $conn4=sqlsrv_query($conn_hq,$qql, $params, $options );
                    $count4 = sqlsrv_num_rows($conn4);

                    if($count4 > 0){

                    while ($rows4 = sqlsrv_fetch_array($conn4, SQLSRV_FETCH_ASSOC)) {

                    $BatchNumber =  $rows4['BatchNumber'];
                    $Product =  $rows4['Product'];
                    $Identifier =  $rows4['Identifier'];
                    $UnitVolume =  $rows4['UnitVolume'];
                    $DateExpiry =  $rows4['DateExpiry'];
                    $DateReceived =  $rows4['DateReceived'];
                    $UnitGroup =  $rows4['UnitGroup'];
                    $Concentration =  $rows4['Concentration'];
                    $Chart =  $rows4['Chart'];
                    $PatName =  $rows4['PatName'];
                    $DoB =  $rows4['DoB'];
                    $Age =  $rows4['Age'];
                    $Sex =  $rows4['Sex'];
                    $Addr0 =  $rows4['Addr0'];
                    $Addr1 =  $rows4['Addr1'];
                    $Addr2 =  $rows4['Addr2'];
                    $Ward =  $rows4['Ward'];
                    $Clinician =  $rows4['Clinician'];
                    $PatientGroup =  $rows4['PatientGroup'];
                    $SampleID =  $rows4['SampleID'];
                    $Typenex =  $rows4['Typenex'];
                    $AandE =  $rows4['AandE'];
                    $EventCode =  $rows4['EventCode'];
                    $Comment =  $rows4['Comment'];
                    $EventStart =  $rows4['EventStart'];
                    $EventEnd =  $rows4['EventEnd'];
                    $UserName =  $rows4['UserName'];
                    $RecordDateTime =  $rows4['RecordDateTime'];
                    $LabelPrinted =  $rows4['LabelPrinted'];
                    
                    DB::insert('insert into batchproducts(BatchNumber,Product,Identifier,UnitVolume,DateExpiry,DateReceived,UnitGroup,Concentration,Chart,PatName,DoB,Age,Sex,Addr0,Addr1,Addr2,Ward,Clinician,PatientGroup,SampleID,Typenex,AandE,EventCode,Comment,EventStart,EventEnd,UserName,RecordDateTime,LabelPrinted, uid) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$BatchNumber,$Product,$Identifier,        $UnitVolume,$DateExpiry,$DateReceived,$UnitGroup,$Concentration,$Chart,$PatName,$DoB,$Age,$Sex, $Addr0,$Addr1,$Addr2,$Ward,$Clinician,$PatientGroup,$SampleID,$Typenex,$AandE,$EventCode,$Comment,$EventStart,$EventEnd,$UserName,$RecordDateTime,$LabelPrinted, $uid]);

                    }
                    }           
                }
                }        
               
             }
             
             }


              $uids =  DB::table('btproducts')->pluck('uid')->where('sampleid',$sampleid_);


                    foreach($uids as $uid) {


                        $sql="select * from ocmRequestDetails where uid = '$uid'  ";
                    
                        $res=sqlsrv_query($conn_hq,$sql, $params, $options );
                        $count1 = sqlsrv_num_rows($res);
    
                        if($count1 == 0) {
                        DB::table('btproducts')->where('uid',$uid)->delete();
                        }
                    }




             DB::table('Latests')->where('LabNumber',$sampleid_)->delete();


                  $sql="select * from ocmRequestDetails where SampleID = '$sampleid_'  ";
                    
                    $res=sqlsrv_query($conn_hq,$sql, $params, $options );
                    $count1 = sqlsrv_num_rows($res);

                    if($count1 > 0) {
                        while ($rows4 = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)){
                            $Identifier1 =  $rows4['indentifier'];
                            $uid =  $rows4['uid'];


                            $qql="select * from ocmbtproductsissued where uid= '$uid'";
                            $conn3=sqlsrv_query($conn_hq,$qql, $params, $options );
                            $count3 = sqlsrv_num_rows($conn3);
        
                            if($count3 > 0){

                     
                                while ($rows3 = sqlsrv_fetch_array($conn3, SQLSRV_FETCH_ASSOC)) {

                                    $identifier1 =  $rows3['identifier'];

                    $qql="select * from Latests where ISBT128 = '$identifier1'";
                    $conn4=sqlsrv_query($conn_hq,$qql, $params, $options );
                    $count4 = sqlsrv_num_rows($conn4);

                    if($count4 > 0){

                    while ($rows4 = sqlsrv_fetch_array($conn4, SQLSRV_FETCH_ASSOC)) {

                    $Number =  $rows4['Number'];
                    $Event =  $rows4['Event'];
                    $PatID =  $rows4['PatID'];
                    $PatName =  $rows4['PatName'];
                    $Operator =  $rows4['Operator'];
                    $DateTime =  $rows4['DateTime'];
                    $GroupRH =  $rows4['GroupRH'];
                    $Supplier =  $rows4['Supplier'];
                    $DateExpiry =  $rows4['DateExpiry'];
                    $LabNumber =  $rows4['LabNumber'];
                    $crt =  $rows4['crt'];
                    $cco =  $rows4['cco'];
                    $cen =  $rows4['cen'];
                    $crtr =  $rows4['crtr'];
                    $ccor =  $rows4['ccor'];
                    $cenr =  $rows4['cenr'];
                    $Barcode =  $rows4['Barcode'];
                    $Checked =  $rows4['Checked'];
                    $Notes =  $rows4['Notes'];
                    $EventStart =  $rows4['EventStart'];
                    $EventEnd =  $rows4['EventEnd'];
                    $OrderNumber =  $rows4['OrderNumber'];
                    $Screen =  $rows4['Screen'];
                    $Reason =  $rows4['Reason'];
                    $ISBT128 =  $rows4['ISBT128'];
                    
                       DB::insert('insert into latests (Number,Event,PatID,PatName,Operator,DateTime,GroupRH,
                    Supplier,DateExpiry,LabNumber,crt,cco,cen,crtr,ccor,cenr,Barcode,Checked,Notes,
                    EventStart,EventEnd,OrderNumber,Screen,Reason,ISBT128)
                     values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$Number,$Event,$PatID,$PatName,$Operator
                    ,$DateTime ,$GroupRH,  $Supplier,$DateExpiry,$LabNumber ,$crt,  $cco, $cen,  $crtr , $ccor,$cenr
                    ,$Barcode,$Checked,$Notes, $EventStart,$EventEnd,$OrderNumber, $Screen , $Reason,$ISBT128]);

                    }
                    }           
                }
                    }
                }

            }



                $sql1="select * from ocmRequestDetails where SampleID = '$sampleid_' and status in ('Pending','Process')";
                $res1 = sqlsrv_query($conn_hq, $sql1, $params, $options );
                $count5 = sqlsrv_num_rows($res1);

                if($count5 > 0) {
                    while ($rows6 = sqlsrv_fetch_array($res1, SQLSRV_FETCH_ASSOC)){
                        $units = $rows6['units'];
                        $uid1 = $rows6['uid'];
                        $status = $rows6['status'];

                        $sql2="SELECT * from ocmbtproductsissued where uid = '$uid1'";
                        $res2 = sqlsrv_query($conn_hq, $sql2, $params, $options );
                        $count6 = sqlsrv_num_rows($res2);
                        if($count6 > 0){
                           if($units == $count6){
                                $sql3="update ocmRequestDetails set status = 'Issued' where uid = '$uid1'";
                                $res3 = sqlsrv_query($conn_hq, $sql3, $params, $options );
                                $count7 = sqlsrv_num_rows($res3);

                                if($count7 > 0){
                                   DB::table('btproducts')->where('uid', $uid1)->update(['status' => 'Issued']);
                                }             
                            }
                        }
                    }
                }


                  foreach($OCMPhlebotomies as $OCMPhlebotomy) {

                         DB::table('observations')
                                             ->where('sampleid', $OCMPhlebotomy->PhlebotomySampleID)
                                             ->delete();

                         $tsql1 = "SELECT * from observations where SampleID  = $OCMPhlebotomy->PhlebotomySampleID ";
                         $getList = sqlsrv_query($conn_hq, $tsql1);
                         while ($comments = sqlsrv_fetch_array($getList, SQLSRV_FETCH_ASSOC)) { 


                        DB::insert('insert into observations 
                            (sampleid, department, message, datetime, added) values (?, ?, ?, ?, ?)', 
                        [$OCMPhlebotomy->PhlebotomySampleID, $comments['Discipline'], $comments['Comment'], $comments['Comment'],  date('Y-m-d H:i:s')]);
                            
                            }


                          $tsql11 = "select Bioresults.Code,Bioresults.Comment, ocmMapping.SourceValue  from BioResults inner join ocmMapping on ocmMapping.TargetValue = BioResults.Code and Bioresults.sampleid  = $OCMPhlebotomy->PhlebotomySampleID";
                         $getList1 = sqlsrv_query($conn_hq, $tsql11);
                         while ($comment = sqlsrv_fetch_array($getList1, SQLSRV_FETCH_ASSOC)) { 

                                   DB::update("update results  set 
                                        Comment = '".$comment['Comment']."'  
                                        where 
                                        sampleid = '".$OCMPhlebotomy->PhlebotomySampleID."' 
                                        and 
                                        Code = '".$comment['SourceValue']."'

                                         ");

                             }   


                    
                         }                
          
        
                  }  


            } 
             $sampleIDs;
             $observations = DB::table('observations')
                          ->whereIn('observations.sampleid',$sampleIDs)
                          ->orderBy('observations.added')->get(); 

            $testcomments = DB::table('results') 
                          ->whereIn('results.sampleid',$sampleIDs)
                          ->select('results.sampleid','Lists.Text as department','results.Code','results.Comment','results.RunTime')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->get();  


            $btproducts = DB::table('btproducts') 
                          ->whereIn('btproducts.sampleid',$sampleIDs)
                          ->select('btaddons.name as Product', 'btproducts.created_at', 'btproducts.sampleid', 'btproducts.qty','btproducts.status','btproducts.requiredat')
                          ->leftjoin('btaddons', 'btaddons.id', '=', 'btproducts.pid')
                          ->orderBy('btproducts.created_at','desc')
                          ->get();  


           // $btproducts = DB::table('btproducts') 
           //                ->whereIn('btproducts.sampleid',$sampleIDs)
           //                ->select('btaddons.name as Product', 'btproducts.created_at', 'btproducts.sampleid', 'btproducts.qty','btproducts.status','btproducts.requiredat')
           //                ->leftjoin('btaddons', 'btaddons.id', '=', 'btproducts.pid')
           //                ->leftjoin('batchproducts', 'batchproducts.uid', '=', 'btproducts.uid')
           //                ->orderBy('btproducts.created_at','desc')
           //                ->get();  
                          


            $patientdetails = DB::table('patientdetails')  
                          ->whereIn('patientdetails.labnumber',$sampleIDs)
                          ->get();  


           $kleihauer = DB::table('kleihauer')  
                          ->whereIn('kleihauer.SampleID',$sampleIDs)
                          ->get(); 


           $externalnotes = DB::table('externalnotes')  
                          ->where('externalnotes.mrn',$OCMRequest[0]->Chart)
                          ->get();                                


            $ocmrequestdetailsBT = DB::table('ocmrequestdetails') 
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'ocmrequestdetails.TestID') 
                          ->where('RequestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();                                          


          
         $departments = array_unique(json_decode($departmentsInfo)); 

         $userinfo = auth()->user();


         $products = DB::table('btaddons')->where('inuse',1)->get();


            $fbcresults = DB::table('results')
                        ->where('request', $request->rid)
                        ->where('episode', $request->eid)
                        ->where('fbc', 1)
                        ->where('resulted', 1)
                        ->count();




            $data = [

                    'OCMRequest' => $OCMRequest,
                    'kleihauer' => $kleihauer,
                    'externalnotes' => $externalnotes,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'results' => $results,
                    'requested' => $requested,
                    'observations' => $observations,
                    'testcomments' => $testcomments,
                    'departments' => $departments,
                    'requestedDepartments' => $requestedDepartments,
                    'DoB' => $DoB,  
                    'testsInfo' => $testsInfo,
                    'products' => $products,
                    'ocmrequestdetailsBT' => $ocmrequestdetailsBT,
                    'btproducts' => $btproducts,
                    'patientdetails' => $patientdetails,
                    'users' => $users,
                    'requestInformation' => $requestInformation,
                    'ocmrequestquestionsdetails' => $ocmrequestquestionsdetails, 
                    'btrequestquestions' => $btrequestquestions, 
                    'user' => $userinfo->id,
                    'fbcresults' => $fbcresults

                ];  

          return view('layouts.request')->with('data',$data); 


        
    }



      public function downloadReuqest(Request $request)
    {
        
        if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') { 

      
                $ocmrequest = DB::table('ocmrequest') 
                                     ->select('id')
                                     ->where('ReqestID',$request->rid)
                                     ->where('RequestCreatedBy',Auth::user()->id)
                                     ->get();

                    if(count($ocmrequest) == 0) {

                        return redirect('/home');
                    }                                 


               } 

        $user = auth()->user(); 
        


        $business = DB::table('business')->get();
        $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    'Clinics.name as clinic', 
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d H:i A');

                  
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                       ->select('testprofiles.name','OCMRequestDetails.TestDescription') 
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

          $OCMPhlebotomies = DB::table('OCMPhlebotomy')
                       ->select('OCMPhlebotomy.PhlebotomySampleID')  
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)->get(); 

          $sampleIDs = array();

          foreach($OCMPhlebotomies as $OCMPhlebotomy) {

                $sampleIDs[] = $OCMPhlebotomy->PhlebotomySampleID;
          }                
   
          
          $results = DB::table('results')
                       ->select(
                                'results.Code',
                                'results.sampleid as PhlebotomySampleID',
                                'TestDefinitions.longname as testname',
                                'Lists.Text as department',
                                'results.Code as code',
                                'results.result',
                                'results.Flags',
                                'results.Units',
                                'results.Analyser',
                                'results.NormalLow',
                                'results.NormalHigh',
                                'results.Comments',
                                'results.resulted'
                            )  
                          ->leftjoin('TestDefinitions', 'TestDefinitions.shortname', '=', 'results.Code')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->whereIn('results.sampleid',$sampleIDs)
                          ->groupBy('results.Code')
                          ->orderBy('results.sampleid')
                          ->get(); 
            
            $requested = '';
            
            if(count($results) == 0) {

             $requested = DB::table('ocmrequesttestsdetails')
                       ->select(
                                'TestDefinitions.shortname as testname',
                                'ocmrequesttestsdetails.sampleid'
                            )  
                          ->leftjoin('TestDefinitions', 'TestDefinitions.id', '=', 'ocmrequesttestsdetails.test')
                          ->where('ocmrequesttestsdetails.request', $request->rid)
                          ->where('ocmrequesttestsdetails.episode', $request->eid)
                          ->get(); 
            }              

            $DoB = Carbon::parse($OCMRequest[0]->DoB)->diff(Carbon::now())->y;
            $DoB = $OCMRequest[0]->DoB;

      
            $data = [

                    'business' => $business,
                    'DoB' => $DoB,
                    'OCMRequest' => $OCMRequest,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'results' => $results,
                    'requested' => $requested,
                    'colorscheme' => $user->colorscheme,
                    'font' => $user->font,
                    'font_link' => $user->font_link,
                    'font_weight' => $user->font_weight,
                    'name' => ''           
                ]; 


        // return view ('downloadrequest')->with('data',$data);
        return $dompdf = PDF::loadView('downloadrequest',array('data' =>$data))->stream(); 
        $pdf = PDF::loadView('downloadrequest',array('data' =>$data)); 
        return $pdf->download('Reuqest # '.$request->rid.' - '.$OCMRequest[0]->PatName.'.pdf');
        //return PDF::loadView('downloadrequest')->stream();
    }


     public function PrintRequestExternalLab(Request $request)
    {
         $user = auth()->user(); 
        $business = DB::table('business')->get();
        $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    'Clinics.name as clinic', 
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d H:i A');

                  
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                       ->select('testprofiles.name','OCMRequestDetails.TestDescription') 
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

         $GP = DB::table('GPs')->where('GPs.id', $OCMRequest[0]->gp)->get();


          $OCMPhlebotomies = DB::table('OCMPhlebotomy')
                       ->select('OCMPhlebotomy.PhlebotomySampleID')  
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)->get(); 

          $sampleIDs = array();

          foreach($OCMPhlebotomies as $OCMPhlebotomy) {

                $sampleIDs[] = $OCMPhlebotomy->PhlebotomySampleID;
          }                
   
         



            $DoB = Carbon::parse($OCMRequest[0]->DoB)->diff(Carbon::now())->y;

            $data = [

                    'business' => $business,
                    'DoB' => $DoB,
                    'GP' => $GP,
                    'OCMRequest' => $OCMRequest,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'colorscheme' => $user->colorscheme,
                    'font' => $user->font,
                    'font_link' => $user->font_link,
                    'font_weight' => $user->font_weight         
                ]; 


        // return view ('downloadrequest')->with('data',$data);
        return $pdf = PDF::loadView('printrequestexternallab',array('data' =>$data))->stream(); 
        $pdf = PDF::loadView('printrequestexternallab',array('data' =>$data)); 
        return $pdf->download('Reuqest # '.$request->rid.' - '.$OCMRequest[0]->PatName.'.pdf');
        //return PDF::loadView('downloadrequest')->stream();
    }  


    public function index(Request $request, $state='',$patient='',$ward='')
    {       
  
             if(\App\Http\Controllers\users::roleCheck('Requests','View',0) == 'No' &&
                \App\Http\Controllers\users::roleCheck('Blood Transfusion','View',0) == 'No')   
                { return redirect('/home');} 


           $pendingRequests = DB::table('ocmphlebotomy') 
                                              ->where('PhlebotomySampleCollected', null)
                                              ->pluck('PhlebotomyRequestID'); 
                                              


         if ($request->ajax()) {

            
            $state = $request->state;
            $patient = $request->patient;


        
            $user = auth()->user();  

            if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') {  

                $Self_Created = 1;
               

              } else {

                 $Self_Created = '';
              } 


               if((\App\Http\Controllers\users::roleCheck('Requests','View',0)) == 'Yes') {  

                $viewPermission = 1;
               

              } else {

                 $viewPermission = '';
              } 




               if((\App\Http\Controllers\users::roleCheck('Blood Transfusion','Self_Created',0)) == 'Yes') {  

                $Self_CreatedBT = 1;
               

              } else {

                 $Self_CreatedBT = '';
              } 


               if((\App\Http\Controllers\users::roleCheck('Blood Transfusion','View',0)) == 'Yes') {  

                    $viewPermissionBT = 1;

                } else {

                     $viewPermissionBT = '';
                } 



           if(!empty($request->from_date))
        
            {
            



            $data = DB::table('OCMRequest')
                                    ->select(
                                    'OCMRequest.id',
                                    'OCMRequest.ReqestID',
                                    'OCMRequest.RequestEpisodeID',
                                    'OCMRequest.RequestVisitID',
                                    'OCMRequest.RequestPriority',
                                    
                                     
                                 DB::raw('(SELECT COUNT(*) FROM results WHERE results.request = OCMRequest.ReqestID and results.SignOffBy in ("",null) ) as unsignedResults'),

                                   DB::raw('(SELECT COUNT(*) FROM results WHERE results.request = OCMRequest.ReqestID) as totalResults'),

                                   DB::raw('(SELECT COUNT(*) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID and ocmphlebotomy.PhlebotomySampleCollected = "Yes") as takenSamples'),

                                  DB::raw('(SELECT COUNT(*) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID) as totalSamples'),

                                   

                                     DB::raw('(SELECT max(PhlebotomySampleDateTime) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID) as ExecutionDateTime'),


                                    'OCMRequest.RequestType',
                                    
                                    'PatientIFs.MRN',
                                    'PatientIFs.Chart',
                                    'PatientIFs.PatName as RequestPatientID',
                                    'OCMRequest.RequestPatientID as patient',

                                    'PatientIFs.id as PID',
                                    'Clinicians.id as CID',
                                    'Wards.id as WID',

                                    'Clinics.name as clinic',
                                    'OCMRequest.RequestClinicianID',
                                    'Clinicians.Text as RequestClinician',
                                    'Wards.Text as RequestWardID',

                                    'OCMRequest.RequestState',
                                    'OCMRequest.RequestCreatedDateTime',
                                    'OCMRequest.RequestModifiedDateTime',
                                    'A.name as RequestCreatedBy',
                                    'B.name as RequestModifiedBy',
                                    )
                                    ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                                    ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                                    ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                                    ->leftjoin('users AS A', 'A.id', '=', 'OCMRequest.RequestCreatedBy')
                                    ->leftjoin('users AS B', 'B.id', '=', 'OCMRequest.RequestModifiedBy')
                                     
                                     
                                     ->when(!empty($request->status) , function ($query) use($request){
                                        
                                         return $query->having('OCMRequest.RequestState',$request->status);

                                     })

                                     ->when(!empty($request->ward) , function ($query) use($request){
                                        
                                         return $query->where('OCMRequest.RequestWardID',$request->ward);

                                     })
                                     ->when(!empty($request->clinician) , function ($query) use($request){
                                        
                                         return $query->having('OCMRequest.RequestClinicianID',$request->clinician);

                                     })

                                      ->when(!empty($request->to_date) , function ($query) use($request){
                                        
                                         return $query->whereBetween('OCMRequest.ExecutionDateTime', [$request->from_date, $request->to_date]);

                                     })

                                     ->when(!empty($patient) , function ($query) use($request,$patient){
                                    
                                         if($patient != 'AllPatients') {
                                         return $query->having('OCMRequest.RequestPatientID',$patient);
                                        }
                                     })


                                     

                                      ->when(!empty($user) , function ($query) use($user,$Self_Created,$Self_CreatedBT,$viewPermission,$viewPermissionBT){
                                            

                     

                                              if($Self_Created == 1) {



                                                $query = $query->where('OCMRequest.RequestCreatedBy',$user->id)
                                                          ->where('OCMRequest.RequestType','Request');
                                                     
                                                 }

                                                 else {

                                                $query = $query->where('OCMRequest.RequestType','=','Request');   
                                                 
                                                 } 



                                             if($Self_CreatedBT == 1) {

                                               $query =  $query->orwhere('OCMRequest.RequestCreatedBy',$user->id)
                                                      ->where('OCMRequest.RequestType','BTRequest');
                                                     
                                                 }

                                                 else {

                                                $query = $query->orwhere('OCMRequest.RequestType','=','BTRequest');   
                                                
                                                } 


                                                if($viewPermission != 1) { 

                                                    $query = $query->having('OCMRequest.RequestType','!=','Request');    

                                                }


                                                if($viewPermissionBT != 1) { 

                                                    $query = $query->having('OCMRequest.RequestType','!=','BTRequest');    

                                                }


                                                
                                             return $query;

                                        

                                     }) 


                                       ->when(!empty($request->rtype) , function ($query) use($request){
                                        
                                         return $query->having('OCMRequest.RequestType',$request->rtype);

                                     })


                                        ->when(!empty($request->state) , function ($query) use($request){
                                        
                                         if($request->state == 'Pending') {

                                            return 1; $query->having('OCMRequest.RequestType','12412412');
                                         }

                                     })


                                     
                                    ->get();

            } else {

                     $data = DB::table('OCMRequest')
                                    ->select(
                                    'OCMRequest.id',
                                    'OCMRequest.ReqestID',
                                    'OCMRequest.RequestEpisodeID',
                                    'OCMRequest.RequestVisitID',
                                    'OCMRequest.RequestPriority',
                                    
                                     DB::raw('(SELECT max(PhlebotomySampleDateTime) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID) as ExecutionDateTime'),


                                    'OCMRequest.RequestType',
                                    
                                    'PatientIFs.MRN',
                                    'PatientIFs.Chart',
                                    'PatientIFs.PatName as RequestPatientID',
                                    'OCMRequest.RequestPatientID as patient',
                                    
                                    'PatientIFs.id as PID',
                                    'Clinicians.id as CID',
                                    'Wards.id as WID',

                                    'Clinics.name as clinic',
                                    'OCMRequest.RequestClinicianID',
                                    'Clinicians.Text as RequestClinician',
                                    'Wards.Text as RequestWardID',
                                     
                                DB::raw('(SELECT COUNT(*) FROM results WHERE results.request = OCMRequest.ReqestID and results.SignOffBy in ("",null) ) as unsignedResults'),

                               DB::raw('(SELECT COUNT(*) FROM results WHERE results.request = OCMRequest.ReqestID) as totalResults'),

                                DB::raw('(SELECT COUNT(*) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID and ocmphlebotomy.PhlebotomySampleCollected = "Yes") as takenSamples'),

                                  DB::raw('(SELECT COUNT(*) FROM ocmphlebotomy WHERE ocmphlebotomy.PhlebotomyRequestID = OCMRequest.ReqestID) as totalSamples'),

                                    'OCMRequest.RequestState',
                                    'OCMRequest.RequestCreatedDateTime',
                                    'OCMRequest.RequestModifiedDateTime',
                                    'A.name as RequestCreatedBy',
                                    'B.name as RequestModifiedBy',
                                    )
                                    ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                                    ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                                    ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                                    ->leftjoin('users AS A', 'A.id', '=', 'OCMRequest.RequestCreatedBy')
                                    ->leftjoin('users AS B', 'B.id', '=', 'OCMRequest.RequestModifiedBy')
                                     
                                     ->when(!empty($state) , function ($query) use($state){
                                        
                                        if($state == 'Requested') {
                                        
                                        return $query->having('OCMRequest.RequestState','like','%'.$state.'%')
                                                ->where('OCMRequest.ExecutionDateTime', '<=', Carbon::now());
                                        
                                        }
                                        elseif($state == 'Upcoming') {

                                        return $query->having('OCMRequest.RequestState','=','Requested')
                                                ->where('OCMRequest.ExecutionDateTime', '>', Carbon::now()); 

                                        } 
                                        elseif($state == 'SentToTheLab') {
                                        return $query->having('OCMRequest.RequestState','=','Sent to the lab'); 
                                        } 
                                        elseif($state == 'ReceivedInLab') {
                                        return $query->having('OCMRequest.RequestState','=','Received in the lab');   
                                        }
                                        elseif($state == 'Progress') {
                                        return $query->having('OCMRequest.RequestState','=','In Progress');   
                                        } 
                                        elseif($state == 'Resulted') {
                                        return $query->having('OCMRequest.RequestState','=','Results Ready');   
                                        } 

                                        elseif($state == 'Cancelled') {
                                        return $query->having('OCMRequest.RequestState','=','Cancelled');   
                                        } 
                                        else{

                                            return $query->where('OCMRequest.ExecutionDateTime', '<=', Carbon::now());
                                        }


                                     })

                                       ->when(!empty($patient) , function ($query) use($request,$patient){
                                        
                                             if($patient != 'AllPatients') {
                                             return $query->having('OCMRequest.RequestPatientID',$patient);
                                            }
                                         })



                                        ->when(!empty($ward) , function ($query) use($ward){
                                              
                                             return $query->having('OCMRequest.RequestWardID',$ward);
                                            
                                         })



                                    

                                ->when(!empty($user) , function ($query) use($user,$Self_Created,$Self_CreatedBT,$viewPermission,$viewPermissionBT){
                                            

                     

                                              if($Self_Created == 1) {

                                                $query = $query->where('OCMRequest.RequestCreatedBy',$user->id)
                                                          ->where('OCMRequest.RequestType','Request');
                                                     
                                                 }

                                                 else {

                                                $query = $query->where('OCMRequest.RequestType','=','Request');   
                                                 
                                                 } 



                                             if($Self_CreatedBT == 1) {

                                               $query =  $query->orwhere('OCMRequest.RequestCreatedBy',$user->id)
                                                      ->where('OCMRequest.RequestType','BTRequest');
                                                     
                                                 }

                                                 else {

                                                $query = $query->orwhere('OCMRequest.RequestType','=','BTRequest');   
                                                
                                                } 


                                                if($viewPermission != 1) { 

                                                    $query = $query->having('OCMRequest.RequestType','!=','Request');    

                                                }


                                                if($viewPermissionBT != 1) { 

                                                    $query = $query->having('OCMRequest.RequestType','!=','BTRequest');    

                                                }


                                             return $query;

                                        

                                     })  
                            

                                      ->when(!empty($request->state) , function ($query) use($request,$pendingRequests){
                                        
                                         if($request->state == 'Pending') {

                                         
                                              if(count($pendingRequests) > 0) {

                                                    return $query->whereIn('OCMRequest.ReqestID',[$pendingRequests]);

                                              } else {

                                                    return $query->having('OCMRequest.ReqestID','111111');
                                              }

                                            
                                         }

                                     })
                                
                                    ->get();

            }                                    

            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">';

                                  if($row->RequestType == 'Request') {

                                        $btn .= '<a data="'.$row->ReqestID.'" id="'.route('viewRequest').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="View Request" class="btn btn-secondary viewRequest">
                                     <i class="fas fa-eye"></i>
                                    </a>'; 

                                  } 
                                  elseif($row->RequestType == 'BTRequest') {

                                        $btn .= '<a data="'.$row->ReqestID.'" id="'.route('viewBTRequest').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="View Request" class="btn btn-secondary viewRequest">
                                     <i class="fas fa-eye"></i>
                                    </a>

                                    <a data="'.$row->ReqestID.'" id="'.route('viewBTRequest').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'/RequestProducts" title="Request Products" class="btn btn-danger viewRequest">
                                     <i class="fas fa-fill-drip"></i>
                                    </a>

                                    '; 

                                  }  

                           // $btn .= '  

                                
                           //       <a href="'.route('downloadReuqest').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Download Report" class="btn btn-success">
                           //       <i class="fas fa-download"></i>
                           //      </a>';



                                 $btn .= '  

                                
                                 <a href="'.route('PatientHistory').'/'.$row->patient.'"  title="Patient History" class="btn btn-primary">
                                  <i class="fas fa-chart-area"></i>
                                </a>


                                 <a href="'.route('PatientHistoryBT').'/'.$row->Chart.'"  title="Product History" class="PatientHistoryBT btn btn-danger">
                                 <i class="fas fa-chart-line"></i>
                                </a>

                                ';


                           if($row->RequestState != 'Cancelled') { 

                           $btn .= '
                                <button type="button" id="'.route('requestEpisode').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="Add New Request" class="addEpisode btn btn-warning"><i class="fas fa-plus"></i>
                                </button>';

                                if($row->RequestState == 'Requested') { 


                                   if($row->RequestType == 'Request') {
                                    
                                $btn .= '<button rid="'.$row->ReqestID.'" eid="'.$row->RequestEpisodeID.'"  type="button" title="Print Request" class="externalLab btn btn-default">
                                        <i class="fas fa-map-marked"></i>
                                        </button>';
                                           }  
                                    $btn .= '

                                

                                <a href="'.route('AddSample').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Sample Collection" class="btn btn-danger">
                                 <i class="fas fa-vials"></i>
                                </a>';

                             
                                 if($row->RequestType == 'Request') {

                                        $btn .= ' <a href="'.route('Request').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Edit Request" class="btn btn-primary">
                                 <i class="fas fa-edit"></i>
                                 </a>'; 

                                  } 
                                  elseif($row->RequestType == 'BTRequest') {

                                        $btn .= ' <a href="'.route('BTRequest').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Edit Request" class="btn btn-primary">
                                 <i class="fas fa-edit"></i>
                                 </a>'; 

                                  }  


                                }

                                if($row->RequestState == 'Sent to the lab / Partially') { 

                                    $btn .= '
                                 <a href="'.route('AddSample').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Sample Collection" class="btn btn-danger">
                                 <i class="fas fa-vials"></i>
                                </a>

                                ';

                                }


                                if($row->RequestState == 'Sent to the lab') { 

                                    $btn .= '<a  target="_bank" 
                                    href="'.route('PrintSampleBarCodes').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="Print Barcodes" class="btn btn-info d-none">
                                 <i class="fas fa-print"></i>
                                </a>
                                 <a href="'.route('AddSample').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Sample Collection" class="btn btn-danger">
                                 <i class="fas fa-vials"></i>
                                </a>

                                ';

                                }

                                if($row->RequestState == 'Received in the lab' || $row->RequestState == 'In Progress') { 

                                    $btn .= '  <a href="'.route('AddSample').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'"  title="Sample Collection" class="btn btn-danger">
                                 <i class="fas fa-vials"></i>
                                </a>
                                <a  target="_bank" 
                                    href="'.route('PrintSampleBarCodes').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="Print Barcodes" class="btn btn-info d-none">
                                 <i class="fas fa-print"></i>
                                </a>
    
                                ';

                                }

                                


                                if($row->RequestState != 'Requested' && $row->RequestState != 'Cancelled' && $row->RequestState != 'Sent to the lab / Partially') { 


                                 if($row->RequestType == 'Request') {


                                    $btn .= '
                                        <a style="padding:6px;width:50px;" 
                                        href="'.route('Request').'/'.$row->ReqestID.'/'.$row->RequestEpisodeID.'" title="Add-on" class="btn btn-primary">
                                         <i class="fas fa-vials"></i> 
                                        </a>';

                                    }

                                }


                                if($row->RequestState == 'Sent to the lab' || $row->RequestState == 'Requested') { 

                                    $btn .= '<button type="button" rid="'.$row->ReqestID.'" eid="'.$row->RequestEpisodeID.'" title="Cancel Request" class="delete btn btn-dark"><i class="fas fa-times"></i>
                                </button>
                                 </div>';

                                }

                               

                            }

                               
    
                            return $btn;
                    }) 

                    ->editColumn('RequestState', function($row){ 

                        $States = DB::table('Lists') 
                                 ->select('Lists.id','Lists.Text')
                                 ->where('Lists.InUse', 1)
                                 ->where('Lists.ListType', 'ST')
                                 ->where('Lists.Text','!=','')
                                 ->orderBy('ListOrder')
                                 ->get();


                        if($row->RequestState == 'Requested') {

                            if($row->ExecutionDateTime > Carbon::now()) {
                            
                            return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-warning">Pending</span>';    
                            
                            } else {
                                
                                return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-success">Requested</span>';   
                            }
                            

                        }

                        elseif (strpos($row->RequestState, 'Progress') !== false) {

                            return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-primary">In Progress</span>'; 

                        }
                        elseif ($row->RequestState == 'Sent to the lab') {

                            return '<span  class="state p-1 btn-sm px-0 btn-block text-center bg-secondary">Sent To Lab</span>'; 

                        }
                        elseif ($row->RequestState == 'Sent to the lab / Partially') {

                            return '<span  class="state p-1 btn-sm px-0 btn-block text-center bg-dark">Sent To Lab / Partially</span>'; 

                        }
                        elseif ($row->RequestState == 'Received in the lab') {

                            return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-secondary">Received in the lab</span>'; 

                        }

                        elseif (strpos($row->RequestState, 'Results Ready') !== false) {

                             return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-success">'.$row->RequestState.'</span>'; 

                        }
                         elseif (strpos($row->RequestState, 'Cancelled') !== false) {

                             return '<span class="state p-1 btn-sm px-0 btn-block text-center bg-dark">'.$row->RequestState.'</span>'; 

                        }

                     })

                    ->editColumn('RequestPriority', function($row){ 

                        if($row->RequestPriority == 'Normal') {

                            return '<span class="p-2 btn-block text-center bg-info">'.$row->RequestPriority.'</span>';

                        }
                        elseif($row->RequestPriority == 'Urgent') {

                            return '<span class="p-2 btn-block text-center bg-danger">'.$row->RequestPriority.'</span>';

                        }

                     })

                    ->editColumn('RequestPatientID', function($row){ 

                     

                            return '<a href="'.route('Requests').'/All/'.$row->PID.'" class="text-primary">'.$row->RequestPatientID.'</a>';

                       

                     })

                   


                    ->editColumn('unsignedResults', function($row){ 

                     

                            return $row->unsignedResults.' of '.$row->totalResults;

                       

                     })

                     ->editColumn('takenSamples', function($row){ 

                     

                            return $row->takenSamples.' of '.$row->totalSamples;

                       

                     })


                    ->editColumn('Chart', function($row){ 

                     
                            if($row->RequestType == 'BTRequest') {

                                 return '<span title="Blood Transfusion Request" class="text-center text-danger">'.$row->Chart.'</span>';

                            } else {

                                return $row->Chart;
                            }
                           

                       

                     })


                    ->editColumn('RequestClinician', function($row){ 

                     

                            return '<button id="'.$row->CID.'" class="text-left text-primary clinicianID" style="border:0px;background:none;">'.$row->RequestClinician.'</button>';

                       

                     })

                    ->editColumn('RequestWardID', function($row){ 

                     

                            return '<button id="'.$row->WID.'" class="text-left text-primary wardID" style="border:0px;background:none;">'.$row->RequestWardID.'</button>';

                       

                     })




                    // ->editColumn('ExecutionDateTime', function($row){

                    // return \App\Http\Controllers\Controller::DateTime($row->ExecutionDateTime);
                    // // return htmlspecialchars_decode(date('jS</\s\up> M y H:i:s', strtotime($row->ExecutionDateTime))); 

                        
                    //  })

                    ->editColumn('RequestCreatedDateTime', function($row){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row->RequestCreatedDateTime)->format('d-m-y H:i'); return $created_at; })

                    ->editColumn('RequestModifiedDateTime', function($data){ 
                        if($data->RequestModifiedDateTime != '') {

                            $RequestModifiedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $data->RequestModifiedDateTime)->format('d-m-y H:i'); return $RequestModifiedDateTime;
                            
                        }
                     })

                    ->setRowId('id')
                    ->setRowClass(function ($row) {
                            
                           if($row->takenSamples != $row->totalSamples) {

                                return 'Pending';
                           }

                        })
                    ->rawColumns(['RequestState','action','RequestPriority','ExecutionDateTime','RequestPatientID','RequestClinician','RequestWardID','Chart'])
                    ->make(true);

                    
                  
        }

        
        $business = DB::table('business')->get();
        $GPs = DB::table('GPs')->get();
        $clinicians = DB::table('clinicians')->orderby('Text')->get();
        $wards = DB::table('wards')->orderby('Text')->get();
        $now = Carbon::now();
        $date1 =  $now->format('Y-m-01'); 
        $date2 =  $now->format('Y-m-t'); 


        $data = [
                    'business' => $business,
                    'GPs' => $GPs,
                    'clinicians' => $clinicians,
                    'wards' => $wards,
                    'date1' => $date1,
                    'date2' => $date2


          ];  

        
        return view ('requests')->with('data',$data);
        
    }


     public function BatchRequesting(Request $request)
    {
        
        if(\App\Http\Controllers\users::roleCheck('Batch Requesting','Add',0) == 'No')  
        { return redirect('/home');} 


    
        $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $ExecutionDateTime = $date;
        $Fasting = '';
        $Priority = '';
        $mode = 'off';
        $OCMRequest = '';
        $OCMRequestDetails = '';
        $OCMRequestQuestionsDetails = '';
        $Visits = 0;

        $quicktestprofiles = DB::table('quicktestprofiles') 
                         ->select(
                            'testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf'
                            )
                         ->join('testprofiles', 'testprofiles.id', '=', 'quicktestprofiles.profileID')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'quicktestprofiles.profileID')
                         ->where('testprofiles.InUse', 1)
                         ->where('testprofiles.btcheck', 0)
                         ->orderBy('quicktestprofiles.profileID', 'asc')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();
        
                     
        $quicktestprofilesArray = array();

          foreach($quicktestprofiles as $quicktestprofile) {

                $quicktestprofilesArray[] = $quicktestprofile->id;
          }  

        $testprofiles = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf',
                            'Lists.Text')
                         ->join('Lists', 'Lists.id', '=', 'testprofiles.department')
                         ->leftjoin('users AS A', 'A.id', '=', 'testprofiles.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'testprofiles.updated_by')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'testprofiles.id')
                         ->where('testprofiles.InUse', 1)
                         ->where('testprofiles.btcheck', 0)
                         ->where('Lists.ListType', 'DPT')
                         ->whereNotIn('testprofiles.id',$quicktestprofilesArray)
                         ->orderBy('testprofiles.name')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();

        $Wards = DB::table('Wards') 
                         ->select('Wards.id','Wards.Text')
                         ->where('Wards.InUse', 1)
                         ->get();

        $Clinicians = DB::table('Clinicians') 
                         ->select('Clinicians.id','Clinicians.Text','Clinicians.Title','Clinicians.ForeName','Clinicians.SurName')
                         ->where('Clinicians.InUse', 1)
                         ->get();

        $States = DB::table('Lists') 
                         ->select('Lists.id','Lists.Text')
                         ->where('Lists.InUse', 1)
                         ->where('Lists.ListType', 'ST')
                         ->where('Lists.Text','!=','')
                         ->orderBy('ListOrder')
                         ->get();


        if($request->rid && $request->eid) {
                   
          $mode = 'on';
            
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->join('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d\TH:i');



                       
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

         $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
                          ->where('OCMRequestQuestionsDetails.request', $request->rid)
                          ->where('OCMRequestQuestionsDetails.episode', $request->eid)->get();
    
        } 
            
          $data = [
                    'mode' => $mode,
                    'OCMRequest' => $OCMRequest,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'OCMRequestQuestionsDetails' => $OCMRequestQuestionsDetails,
                    'Clinicians' => $Clinicians,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'Wards' => $Wards,
                    'Fasting' => $Fasting,
                    'Priority' => $Priority,
                    'States' => $States,
                    'quicktestprofiles' => $quicktestprofiles,
                    'testprofiles' => $testprofiles
          ]; 

          return view ('batchrequesting')->with('data',$data);
    }


        public function GetPatientList(Request $request)
    {   
        $search = $request->input('search');
        $clients = DB::table('PatientIFs') 
                        ->where('PatName', 'like', $search.'%' )
                        ->orwhere('DoB', 'like', $search.'%' )
                        ->orderBy('id','desc')
                        ->limit(10)
                        ->get();
        
        $clientList = [];

        foreach ($clients as $client) {
            $clientList[] = ['id' => $client->id, 'text' => $client->PatName];
        }

        return \Response::json($clientList);
    }



        public function GetPatientInfo(Request $request)
    {   
        
         $search = $request->input('search');

         $data = DB::table('PatientIFs')
                         ->select('PatientIFs.Ward as WardID', 
                                  'PatientIFs.Clinician as ClinicianID', 
                                  'OCMRequest.RequestClinicalDetail')
                                    ->leftjoin('OCMRequest', 'OCMRequest.RequestPatientID', '=', 'PatientIFs.id')
                                    ->where('PatientIFs.id',$search)
                                    ->orderBy('OCMRequest.id','desc')
                                    ->limit(1)
                                    ->get();
                
         return \Response::json($data);

        
    }


    

    public function getProfiles(Request $request)
    {

         $panelIDs = explode(',',$request->panelIDs);  
         $existingProfileIDs = explode(',',$request->existingProfileIDs);  


         $response = DB::table('PanelsMapping') 
                         ->select(
                            'PanelsMapping.ProfileID',
                            'testprofiles.name as ProfileName'    
                            )
                         ->leftjoin('testprofiles', 'testprofiles.id', '=', 'PanelsMapping.ProfileID')
                         ->whereIn('PanelsMapping.PanelD', $panelIDs)
                         ->whereNotIn('PanelsMapping.profileID',$existingProfileIDs)
                         ->orderBy('PanelsMapping.profileID', 'asc')
                         ->groupBy('PanelsMapping.ProfileID')
                         ->get();



                    if(count($response) > 0) {                 

                      $ProfileIDs = array();

                          foreach($response as $respo) { 

                                 $ProfileIDs[] = $respo->ProfileID;

                          } 


    if($request->rid != '' && $request->mode == 'add-on') {             


    $OCMRequest = DB::table('OCMRequest')->select('ReqestID','RequestEpisodeID','RequestState')->where('id',$request->rid)->get(); 

    if($OCMRequest[0]->RequestState != 'Requested' && $OCMRequest[0]->RequestState != 'Cancelled')   {



    $ProfileTestMappings = DB::table('ProfileTestMapping')->select('TestDefinitionID')->whereIn('ProfileID',$ProfileIDs)->get();

        if(count($ProfileTestMappings) > 0) {


            $TestIDs = array();

              foreach($ProfileTestMappings as $ProfileTestMapping) { 


                   

                    $TestIDsDUP = DB::table('OCMRequestTestsDetails')
                                               ->select('test')
                                               ->where('request',$OCMRequest[0]->ReqestID)
                                               ->where('episode',$OCMRequest[0]->RequestEpisodeID)
                                               ->where('test',$ProfileTestMapping->TestDefinitionID)->get();

                        if(count($TestIDsDUP) == 0) {

                                 $TestIDs[] = $ProfileTestMapping->TestDefinitionID;
                        }                                               



              } 


              
           $TestDefinitions = DB::table('TestDefinitions')
                                    ->select('id', 'SampleType','Hospital')
                                    ->whereIn('id',$TestIDs)
                                    ->get();
           
                    $response = [];                           

                    foreach($TestDefinitions as $TestDefinition) { 

                             $tid = $TestDefinition->id;
                            // echo '---';
                             $SampleType = $TestDefinition->SampleType;
                            // echo '---';
                             $Hospital = $TestDefinition->Hospital;
                            // echo '---';
                           

                            $profileID = DB::table('ProfileTestMapping')
                                    ->select('ProfileTestMapping.ProfileID')
                                    ->where('ProfileTestMapping.TestDefinitionID',$tid)
                                    ->get();

                                 
                         $departments = DB::table('testprofiles')
                                    ->select('testprofiles.department','testprofiles.name as ProfileName','testprofiles.specialhandling')
                                    ->where('testprofiles.id',$profileID[0]->ProfileID)
                                    ->get();  
                                                                    
                            $department = $departments[0]->department;   
                            $specialhandling = $departments[0]->specialhandling;  
                            $ProfileName = $departments[0]->ProfileName;    


                              $check = DB::table('OCMPhlebotomy')
                               ->select('OCMPhlebotomy.PhlebotomySampleID','Lists.Text as sampletype','facilities.name as hospital') 
                               ->leftjoin('Lists', 'Lists.id', '=', 'OCMPhlebotomy.sampletype')   
                               ->leftjoin('facilities', 'facilities.id', '=', 'OCMPhlebotomy.hospital')     
                               ->where('OCMPhlebotomy.sampletype',$SampleType)
                               ->where('OCMPhlebotomy.hospital',$Hospital)
                               ->where('OCMPhlebotomy.department',$department)
                               ->where('OCMPhlebotomy.PhlebotomyRequestID',$OCMRequest[0]->ReqestID)
                               ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID',$OCMRequest[0]->RequestEpisodeID) 
                               ->groupBy('OCMPhlebotomy.sampletype','OCMPhlebotomy.hospital','OCMPhlebotomy.department')
                               ->get(); 

                               if(count($check) > 0) {


                                    $response[] = array (

                                            'ProfileID' => $profileID[0]->ProfileID,
                                            'ProfileName' => $ProfileName
                                    );                                    

                               }
                                    

                         }

                        $temp = array_unique(array_column($response, 'ProfileID'));
                         $unique_arr = array_intersect_key($response, $temp); 

                         return \Response::json($unique_arr);  
                         
                     }

                     }  else {


                       return \Response::json($response);   

                                 }

                             } else {

                                 return \Response::json($response);  
                             }

                             }
    }


     public function Request(Request $request)
    {
        
    

        if($request->rid != '') {

                if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') { 

            
                $ocmrequest = DB::table('ocmrequest') 
                                     ->select('id')
                                     ->where('ReqestID',$request->rid)
                                     ->where('RequestCreatedBy',Auth::user()->id)
                                     ->get();

                    if(count($ocmrequest) == 0) {

                        return redirect('/home');
                    }                                 


               } 
               

              }  
                
   


        $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $ExecutionDateTime = $date;
        $Fasting = '';
        $Priority = '';
        $mode = 'off';
        $OCMRequest = '';
        $OCMRequestDetails = '';
        $OCMRequestQuestionsDetails = '';
        $Visits = 0;

        $quicktestprofiles = DB::table('quicktestprofiles') 
                         ->select(
                            'testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf'
                            )
                         ->join('testprofiles', 'testprofiles.id', '=', 'quicktestprofiles.profileID')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'quicktestprofiles.profileID')
                         ->where('testprofiles.InUse', 1)

                         ->when(request()->segment(1) == 'Request' , function ($query) use($request){
                                    return $query->where('testprofiles.btcheck',0);
                                    })

                         ->when(request()->segment(1) == 'BTRequest' , function ($query) use($request){
                                    return $query->where('testprofiles.btcheck',1);
                                    })

             

                         ->orderBy('quicktestprofiles.profileID', 'asc')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();
        
                     
        $quicktestprofilesArray = array();

          foreach($quicktestprofiles as $quicktestprofile) {

                $quicktestprofilesArray[] = $quicktestprofile->id;
          }  

       



           


        $testprofiles = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf',
                            'Lists.Text')
                         ->join('Lists', 'Lists.id', '=', 'testprofiles.department')
                         ->leftjoin('users AS A', 'A.id', '=', 'testprofiles.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'testprofiles.updated_by')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'testprofiles.id')
                         ->where('testprofiles.InUse', 1)
                         ->where('Lists.ListType', 'DPT')

                         ->when(request()->segment(1) == 'Request' , function ($query) use($request){
                                    return $query->where('testprofiles.btcheck',0);
                                    })

                         ->when(request()->segment(1) == 'BTRequest' , function ($query) use($request){
                                    return $query->where('testprofiles.btcheck',1);
                                    })

                         ->whereNotIn('testprofiles.id',$quicktestprofilesArray)
                         ->orderBy('testprofiles.name')
                         ->groupBy('ProfileTestMapping.ProfileID')

                         ->get();

        

        

       $clinicians = DB::table('clinicians')->orderby('Text')->get();
        $wards = DB::table('wards')->orderby('Text')->get();




        $States = DB::table('Lists') 
                         ->select('Lists.id','Lists.Text')
                         ->where('Lists.InUse', 1)
                         ->where('Lists.ListType', 'ST')
                         ->where('Lists.Text','!=','')
                         ->orderBy('ListOrder')
                         ->get();


        if($request->rid && $request->eid) {
                   
          $mode = 'on';


          if(\App\Http\Controllers\users::roleCheck('Requests','Update',0) == 'No')  
                { return redirect('/home');} 
            
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->join('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d\TH:i');



                       
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

         $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
                          ->where('OCMRequestQuestionsDetails.request', $request->rid)
                          ->where('OCMRequestQuestionsDetails.episode', $request->eid)->get();
    
         
            if($OCMRequest[0]->RequestState != 'Requested' && $OCMRequest[0]->RequestState != 'Cancelled')   {

                $mode = 'add-on';
            } 
        } 
        else {


              if(\App\Http\Controllers\users::roleCheck('Requests','Add',0) == 'No')  
                { return redirect('/home');} 
        }
            
          $data = [
                    'mode' => $mode,
                    'OCMRequest' => $OCMRequest,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'OCMRequestQuestionsDetails' => $OCMRequestQuestionsDetails,
                    'Clinicians' => $clinicians,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'Wards' => $wards,
                    'Fasting' => $Fasting,
                    'Priority' => $Priority,
                    'States' => $States,
                    'quicktestprofiles' => $quicktestprofiles,
                    'testprofiles' => $testprofiles
          ];  

          return view ('request')->with('data',$data);
    } 





public function getQuestions(Request $request)
    
    {


         if($request->row && $request->patient && $request->profiles && $request->session) {

          $data = DB::table('ProfileQuestionMapping')
              ->select('ProfileQuestionMapping.QuestionID','ProfileQuestions.question','ProfileQuestions.answers')
              ->leftjoin('ProfileQuestions', 'ProfileQuestions.ID', '=', 'ProfileQuestionMapping.QuestionID')
              ->whereIn('ProfileQuestionMapping.ProfileID', $request->profiles)
              ->groupBy('ProfileQuestionMapping.QuestionID')
              ->get();

            if(count($data) > 0) {

                  
                    foreach($data as $value) {

                          $id = DB::table('OCMRequestQuestionsDetails')->max('id')+1;

                          DB::table('OCMRequestQuestionsDetails')->insertOrIgnore([
                                ['id' => $id, 'QuestionID' => $value->QuestionID, 'question' => $value->question, 'answers' => $value->answers, 'session' => $request->session, 'patient' => $request->patient, 'row_id' => $request->row]
                            ]);

                    }

           // return $request->row.' - '.$request->patient.' - '.$request->session;        
            $data = DB::table('OCMRequestQuestionsDetails')
              ->where('OCMRequestQuestionsDetails.session', $request->session)
              ->where('OCMRequestQuestionsDetails.row_id', $request->row)
              ->where('OCMRequestQuestionsDetails.patient', $request->patient)
              ->get();         

            return \Response::json($data);        

            } 

         } 
    }



    public function refreshQuestions(Request $request)
    
    {


         if($request->row && $request->patient && $request->session) {

         return DB::table('OCMRequestQuestionsDetails')
                                 ->where('session', $request->session)
                                 ->where('row_id', $request->row)
                                 ->whereNotIn('patient',[$request->patient])
                                 ->delete();

         } 
    }



public function GetProfileThreshHold(Request $request)
    {

         if($request->id && $request->threshHold && $request->patient) {

          $seconds =  $request->threshHold*60*60;

          $now = Carbon::now();


          $OCMRequest = DB::table('OCMRequestDetails')
                          ->select('OCMRequestDetails.ExecutionDateTime')
                          ->where('OCMRequestDetails.TestID', $request->id)
                          ->where('OCMRequestDetails.PatientID', $request->patient)
                          ->orderBy('OCMRequestDetails.ExecutionDateTime','desc')
                          ->limit(1)
                          ->get();

                       
           
           //return  strtotime($now) . '---' . strtotime($ExecutionDateTime);
                          
           if(count($OCMRequest) > 0) {    

           $ExecutionDateTime = Carbon::parse($OCMRequest[0]->ExecutionDateTime)->second($seconds);  

           if( strtotime($now) < strtotime($ExecutionDateTime))  {



        return response()->json(['error'=> 'This profile was previously requested on **Date** '.Carbon::createFromFormat('Y-m-d H:i:s', $OCMRequest[0]->ExecutionDateTime)->format('d M - H:i A'), 'message' => 'Do you want to continue ordering this Profile ?']);

           } else {

                return response()->json(['error'=> '' ]);
           }


           }  else {

            return response()->json(['error'=> '' ]);
           }            


         } else {
             return response()->json(['error'=> '' ]);
         }

    }



public static function TestCodes($sample='')
    {

            $testcodes = DB::table('OCMRequestTestsDetails') 
                          ->where('OCMRequestTestsDetails.sample', $sample)->pluck('test');
                          ;
            return $testcodes = DB::table('TestDefinitions') 
                          ->whereIn('TestDefinitions.id', $testcodes)->pluck('shortname');
    }


 public static function answers($question='')
    {

           return $testcodes = DB::table('profilequestions') 
                          ->where('profilequestions.id', $question)->pluck('answers');
                          ;
    }   





public static function checkResult($sample='')
    {

            $requestreports = DB::table('requestreports') 
                          ->where('requestreports.sampleid', $sample)->get();
            
            if(count($requestreports) > 0) {

                return 1;
            } 
            else {

                return 0;
            }
            
    }    


public function AddSample(Request $request)
    {   

        if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') { 

      
                $ocmrequest = DB::table('ocmrequest') 
                                     ->select('id')
                                     ->where('ReqestID',$request->rid)
                                     ->where('RequestCreatedBy',Auth::user()->id)
                                     ->get();

                    if(count($ocmrequest) == 0) {

                        return redirect('/home');
                    }                                 


               } 


         if($request->rid && $request->eid) {
                   

        $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $testcodes = array();
        $ExecutionDateTime = $date; 
        $ExecutionDateTime2 = $date; 
        $user = auth()->user();
   
         
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*',
                                    'Clinics.name as clinic', 
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

     
             

                    $ids = array();
                    $testids = array();
                   $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')
                          ->select(
                                    'id',
                                    'test'
                                )    
                          ->where('OCMRequestTestsDetails.request', $request->rid)
                          ->where('OCMRequestTestsDetails.episode', $request->eid)->get(); 

                    


                     foreach($OCMRequestTestsDetails as $key => $OCMRequestTestsDetail) {

                         $ids[] = $OCMRequestTestsDetail->id;
                         $testids[] = $OCMRequestTestsDetail->test;

                     } 

                     $testcodes = DB::table('TestDefinitions') 
                          ->whereIn('TestDefinitions.id', $testids)->pluck('shortname');

                     

                   



                 $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')
                          ->select(
                            'OCMRequestTestsDetails.id',
                            'A.Text as Sample',
                            'facilities.name as Hospital',
                            'OCMRequestTestsDetails.sampletype',
                            'OCMRequestTestsDetails.containerID',
                            'OCMRequestTestsDetails.hospital',
                            'OCMRequestTestsDetails.department',
                            'B.Text as specialhandling',
                            'C.Text as Department',
                            'testprofiles.name as ProfileID',
                            'OCMPhlebotomy.PhlebotomySampleID as PhlebotomySampleID',
                            'OCMPhlebotomy.PhlebotomySampleCollected as PhlebotomySampleCollected',
                            'OCMPhlebotomy.PhlebotomySampleDateTime as PhlebotomySampleDateTime',
                            'OCMPhlebotomy.print as print',
                            'OCMPhlebotomy.PhlebotomyComment as comment',
                            )  

                          ->leftjoin('Lists AS A', 'A.id', '=', 'OCMRequestTestsDetails.sampletype')
                          ->leftjoin('Lists AS B', 'B.id', '=', 'OCMRequestTestsDetails.specialhandling')   
                          ->leftjoin('Lists AS C', 'C.id', '=', 'OCMRequestTestsDetails.department')    
                          ->leftjoin('facilities', 'facilities.id', '=', 'OCMRequestTestsDetails.hospital')
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'OCMRequestTestsDetails.profileID')
                          ->leftjoin('OCMPhlebotomy', 'OCMPhlebotomy.containerID', '=', 'OCMRequestTestsDetails.containerID')
                          ->whereIn('OCMRequestTestsDetails.id', $ids)
                          ->where('request', $request->rid)
                          ->where('episode', $request->eid)
                          ->groupBy([
                            'OCMRequestTestsDetails.sampletype',
                            'OCMRequestTestsDetails.hospital',
                            'OCMRequestTestsDetails.department',
                            'OCMRequestTestsDetails.containerID'
                            ])
                          ->orderBy('id')
                          ->get(); 

                

            foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {

                  $sampletype =   $OCMRequestTestsDetail->sampletype;



           $testids = array();
           $OCMRequestTestsDetails_ = DB::table('OCMRequestTestsDetails')
                  ->select('test' )    
                  ->where('OCMRequestTestsDetails.request', $request->rid)
                  ->where('OCMRequestTestsDetails.episode', $request->eid)
                  ->where('OCMRequestTestsDetails.sampletype', $sampletype)->get(); 

             foreach($OCMRequestTestsDetails_ as $key => $OCMRequestTestsDetail) {

                 $testids[] = $OCMRequestTestsDetail->test;

             } 

             $testcodes[$sampletype] = DB::table('TestDefinitions') 
                  ->whereIn('TestDefinitions.id', $testids)->pluck('shortname');

            }


                $testDetails = array();

                foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {
                        
                    $testList = array();
         

                      $samplesInfos = DB::table('OCMRequestTestsDetails')
                          ->select(
                                'OCMRequestTestsDetails.test',
                                'OCMRequestTestsDetails.sampletype',
                                'OCMRequestTestsDetails.units',
                                'OCMRequestTestsDetails.capacity'
                            )
                          ->where('OCMRequestTestsDetails.containerID', $OCMRequestTestsDetail->containerID)
                          ->where('OCMRequestTestsDetails.sampletype', $OCMRequestTestsDetail->sampletype)
                          ->where('OCMRequestTestsDetails.hospital', $OCMRequestTestsDetail->hospital)
                          ->where('OCMRequestTestsDetails.department', $OCMRequestTestsDetail->department)
                          ->where('request', $request->rid)
                          ->where('episode', $request->eid)
                          ->orderBy('OCMRequestTestsDetails.units')
                          ->get(); 

                     $count = 0; 
                     $count2 = 0;

                  
                    foreach($samplesInfos as $key => $samplesInfo)   {   


                       $units =  $samplesInfo->units;
                       $capacity =  $samplesInfo->capacity;
                       $test =  $samplesInfo->test;
                            

                                // echo $key;
                                // echo '--';
                                // echo $units;
                                // echo '--';
                                // echo $samplesInfo->test;
                                // echo '<br>';
                                  
                                if($count == 0){

                                 $id = uniqid();  

                                 $count +=  $units;
                                 $remaning = $capacity-$units;
                                   // echo $test;
                                   //  echo '--';
                                    DB::update("update OCMRequestTestsDetails  set sample = '".$id."'  where test = '".$test."' 
                                        and request = '".$request->rid."'
                                        and episode = '".$request->eid."'
                                        and containerID = '".$OCMRequestTestsDetail->containerID."'
                                         ");
                                  
                                    } elseif ($units  <= $remaning){
  
                                  $count +=  $units;
                                  $remaning = $remaning-$units; 
                                    // echo $test;
                                    //        echo '-----';
                                            DB::update("update OCMRequestTestsDetails  set sample = '".$id."'  where test = '".$test."' 
                                        and request = '".$request->rid."'
                                        and episode = '".$request->eid."'
                                        and containerID = '".$OCMRequestTestsDetail->containerID."'
                                         ");
                                    

                                    }  elseif ($count > 0){

                                  $id = uniqid();   

                                   $count +=  $units;
                                    DB::update("update OCMRequestTestsDetails  set sample = '".$id."'  where test = '".$test."' 
                                        and request = '".$request->rid."'
                                        and episode = '".$request->eid."'
                                        and containerID = '".$OCMRequestTestsDetail->containerID."'
                                         ");
                                   // echo $test;
                                   // echo '--'; 
                                   

                                  }  

                                
                            

                                

                         } 

                  

                        

                }   
        
                     
                
                 $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')
                          ->select(
                            'OCMRequestTestsDetails.id',
                            'A.name as Sample',
                            'facilities.name as Hospital',
                            'OCMRequestTestsDetails.sampletype',
                            'OCMRequestTestsDetails.sample',
                            'OCMRequestTestsDetails.containerID',
                            'OCMRequestTestsDetails.hospital',
                            'OCMRequestTestsDetails.department',
                            'B.Text as specialhandling',
                            'C.Text as Department',
                            'testprofiles.name as ProfileID',
                            'OCMPhlebotomy.PhlebotomySampleID as PhlebotomySampleID',
                            'OCMPhlebotomy.PhlebotomySampleCollected as PhlebotomySampleCollected',
                            'OCMPhlebotomy.PhlebotomySampleDateTime as PhlebotomySampleDateTime',
                            'OCMPhlebotomy.print as print',
                            'OCMPhlebotomy.PhlebotomyComment as comment',
                            )  

                          ->leftjoin('containers AS A', 'A.id', '=', 'OCMRequestTestsDetails.containerID')
                          ->leftjoin('Lists AS B', 'B.id', '=', 'OCMRequestTestsDetails.specialhandling')   
                          ->leftjoin('Lists AS C', 'C.id', '=', 'OCMRequestTestsDetails.department')    
                          ->leftjoin('facilities', 'facilities.id', '=', 'OCMRequestTestsDetails.hospital')
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'OCMRequestTestsDetails.profileID')
                          ->leftjoin('OCMPhlebotomy', 'OCMPhlebotomy.PhlebotomySampleID', '=', 'OCMRequestTestsDetails.sampleID')
                          ->whereIn('OCMRequestTestsDetails.id', $ids)
                          ->where('request', $request->rid)
                          ->where('episode', $request->eid)
                          
                          ->groupBy([
                            'OCMRequestTestsDetails.sampletype',
                            'OCMRequestTestsDetails.hospital',
                            'OCMRequestTestsDetails.department',
                            'OCMRequestTestsDetails.containerID',
                            'OCMRequestTestsDetails.sample'
                            ])
                          ->orderBy('id')
                          ->get(); 



                $PhlebotomySampleID =  $OCMRequestTestsDetails[0]->PhlebotomySampleID;

                $PhlebotomySampleCollected = array();
                foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {

                    if($OCMRequestTestsDetail->PhlebotomySampleCollected == 'Yes') {
                    $PhlebotomySampleCollected[]  = $OCMRequestTestsDetail->PhlebotomySampleCollected;  
                    } 
                }

                if(count($PhlebotomySampleCollected) == count($OCMRequestTestsDetails)) {

                    $SampleCollected = 'Yes';


                }  else {

                    $SampleCollected = 'No';

                }              



                if($PhlebotomySampleID == '') {
                    
                    $BarcodesGenerated = 'No';

                } else {
                    
                    $BarcodesGenerated = 'Yes'; 
                }


                   // return $testDetails;

           // return $OCMRequestTestsDetails;    
            $data = [
                    'datetime' => $date,
                    'BarcodesGenerated' => $BarcodesGenerated,
                    'testcodes' => $testcodes,
                    'SampleCollected' => $SampleCollected,
                    'OCMRequest' => $OCMRequest,
                    'OCMRequestTestsDetails' => $OCMRequestTestsDetails      
                ];  

          return view ('samples')->with('response',$data);


        } 

          
    } 


    public function PatientConfirmation(Request $request)
    {

        if($request->rid && $request->eid) {

        $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$request->rid,$request->eid,0,0,0,'Request','Patient has been confirmed for the Request # '.$request->rid.'/'.$request->eid.'.']);  

       return DB::update("update OCMRequest  set PatientConfirmation =  1  where ReqestID = '".$request->rid."' and RequestEpisodeID = '".$request->eid."' ");

        }

    } 


    public function AddOnExistingProfile(Request $request)
    {

        if($request->id && $request->profile) {
                   
         $OCMRequest = DB::table('OCMRequest')
                          ->select('ReqestID','RequestEpisodeID') 
                          ->where('id', $request->id)
                          ->get();

         if(count($OCMRequest) > 0) {

                $OCMRequest = DB::table('OCMRequestTestsDetails')
                          ->where('request', $OCMRequest[0]->ReqestID)
                          ->where('episode', $OCMRequest[0]->RequestEpisodeID)
                          ->where('profileID', $request->profile)
                          ->get();

                return \Response::json(count($OCMRequest));          
         }                 

        }

    }    

     public function PrintSampleBarCodes(Request $request)
    {   

        if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') { 

      
                $ocmrequest = DB::table('ocmrequest') 
                                     ->select('id')
                                     ->where('ReqestID',$request->rid)
                                     ->where('RequestCreatedBy',Auth::user()->id)
                                     ->get();

                    if(count($ocmrequest) == 0) {

                        return redirect('/home');
                    }                                 


               } 


        if($request->rid && $request->eid) {
                   
        $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $testcodes = array();
        $ExecutionDateTime = $date; 
        $ExecutionDateTime2 = $date; 
        $user = auth()->user();
   
         
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    DB::raw('DATE_FORMAT(PatientIFs.DoB, "%d/%m/%Y") as DoB'),
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d H:i A');

                  
            
           $Samples = DB::table('OCMPhlebotomy')  
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)
                           ->when(!empty($request->sid) , function ($query) use($request){
                                    return $query->where('OCMPhlebotomy.PhlebotomySampleID',$request->sid);
                                    })
                          ->get();    




          $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')
                          ->select(
                            'OCMRequestTestsDetails.id',
                            'A.name as Sample',
                            'OCMRequestTestsDetails.sampletype',
                            'facilities.name as Hospital',
                            'B.Text as specialhandling',
                            'OCMRequestTestsDetails.sample',
                            'C.Text as Department',
                            'testprofiles.name as ProfileID',
                            'OCMPhlebotomy.PhlebotomySampleID as PhlebotomySampleID',
                            'OCMPhlebotomy.PhlebotomySampleDateTime as PhlebotomySampleDateTime',
                            'OCMPhlebotomy.print as print',
                            'OCMPhlebotomy.PhlebotomyComment as comment',
                            )  

                          ->leftjoin('containers AS A', 'A.id', '=', 'OCMRequestTestsDetails.containerID')
                          ->leftjoin('Lists AS B', 'B.id', '=', 'OCMRequestTestsDetails.specialhandling')   
                          ->leftjoin('Lists AS C', 'C.id', '=', 'OCMRequestTestsDetails.department')     
                          ->leftjoin('facilities', 'facilities.id', '=', 'OCMRequestTestsDetails.hospital')
                          ->leftjoin('testprofiles', 'testprofiles.id', '=', 'OCMRequestTestsDetails.profileID')
                          ->leftjoin('OCMPhlebotomy', 'OCMPhlebotomy.PhlebotomySampleID', '=', 'OCMRequestTestsDetails.sampleID')
                          ->where('OCMRequestTestsDetails.request', $request->rid)
                          ->where('OCMRequestTestsDetails.episode', $request->eid)
                          ->groupBy(['OCMRequestTestsDetails.sampleID'])
                          ->orderBy('id')
                          ->get(); 



            

            foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {

                  $sampletype =   $OCMRequestTestsDetail->sampletype;



           $testids = array();
           $OCMRequestTestsDetails_ = DB::table('OCMRequestTestsDetails')
                  ->select('test' )    
                  ->where('OCMRequestTestsDetails.request', $request->rid)
                  ->where('OCMRequestTestsDetails.episode', $request->eid)
                  ->where('OCMRequestTestsDetails.sampletype', $sampletype)->get(); 

             foreach($OCMRequestTestsDetails_ as $key => $OCMRequestTestsDetail) {

                 $testids[] = $OCMRequestTestsDetail->test;

             } 

             $testcodes[$sampletype] = DB::table('TestDefinitions') 
                  ->whereIn('TestDefinitions.id', $testids)->pluck('shortname');

            }


         $data = [
                    'datetime' => $date,
                    'Samples' => $Samples,
                    'OCMRequest' => $OCMRequest,
                    'testcodes' => $testcodes,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'ExecutionDateTime2' => $ExecutionDateTime2,
                    'OCMRequestTestsDetails' => $OCMRequestTestsDetails      
                ];  

          return view ('printsamplebarcodes')->with('response',$data);


        } 

          
    } 
   

    public function RequestInfo(Request $request)
    {
                       

        if($request->rid && $request->eid) {
                   

            
           $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    'Clinics.name as clinic', 
                                    'Wards.Text as Ward', 
                                    'Clinicians.Text as Clinician',
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->leftjoin('Wards', 'Wards.id', '=', 'OCMRequest.RequestWardID')
                          ->leftjoin('Clinicians', 'Clinicians.id', '=', 'OCMRequest.RequestClinicianID')
                          ->leftjoin('Clinics', 'Clinics.id', '=', 'OCMRequest.clinic')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

          $ExecutionDateTime =  $OCMRequest[0]->ExecutionDateTime; 
          $ExecutionDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $ExecutionDateTime)->format('Y-m-d H:i A');

                  
          $OCMRequestDetails = DB::table('OCMRequestDetails')
                       ->select('testprofiles.name','OCMRequestDetails.TestDescription') 
                          ->join('testprofiles', 'testprofiles.id', '=', 'OCMRequestDetails.TestID')  
                          ->where('OCMRequestDetails.RequestID', $request->rid)
                          ->where('OCMRequestDetails.RequestEpisodeID', $request->eid)->get();

          $OCMPhlebotomies = DB::table('OCMPhlebotomy')
                       ->select('OCMPhlebotomy.PhlebotomySampleID')  
                          ->where('OCMPhlebotomy.PhlebotomyRequestID', $request->rid)
                          ->where('OCMPhlebotomy.PhlebotomyRequestEpisodeID', $request->eid)->get(); 

          $sampleIDs = array();

          foreach($OCMPhlebotomies as $OCMPhlebotomy) {

                $sampleIDs[] = $OCMPhlebotomy->PhlebotomySampleID;
          }                
   
         
          $results = DB::table('results')
                       ->select(
                                'results.Code',
                                'results.sampleid as PhlebotomySampleID',
                                'TestDefinitions.longname as test',
                                'Lists.Text as department',
                                'results.Code as code',
                                'results.result',
                                'results.Flags',
                                'results.Units',
                                'results.Analyser',
                                'results.NormalLow',
                                'results.NormalHigh',
                                'results.Comments'
                            )  
                          ->leftjoin('TestDefinitions', 'TestDefinitions.shortname', '=', 'results.Code')
                          ->leftjoin('Lists', 'Lists.id', '=', 'results.department')
                          ->whereIn('results.sampleid',$sampleIDs)
                          ->groupBy('results.Code')
                          ->orderBy('results.sampleid')
                          ->get(); 


        

            $data = [

                    'OCMRequest' => $OCMRequest,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'results' => $results           
                ];  

           return \Response::json($data);


        } 
            
          
    } 


 



     public function saveRequests(Request $request)
    {


        $user = auth()->user();
        $session = $request->input('session');

        $patients = $request->input('patient');
        $clinician = $request->input('clinician');
        $clinicalDetail = $request->input('clinicalDetail');
        $ward = $request->input('ward');
        $bed = $request->input('bed');
        $priority = $request->input('priority');
        $fasting = 'No';
        $outofhours = 'No';
        
        $datetime = $request->input('datetime');
        $notes = $request->input('notes');

        $tests = $request->input('test');
        $description = $request->input('description'); 

        $validator = Validator::make($request->all(), [
            'test.*' => 'required',            
            'datetime' => 'required',
            'patient.*' => 'required',  
            'clinician.*' => 'required',  
            'ward.*' => 'required',  
            'bed.*' => 'required',  
            'priority.*' => 'required'
        ]);

          if ($validator->passes()) {




        foreach($patients as $key => $patient) {


             $patientinfo = DB::table('PatientIFs')
                                        ->select('DoB')
                                        ->where('id',$patient)
                                        ->get();

                 if(count($patientinfo) > 0) {
                 $DoB = $patientinfo[0]->DoB;                                         
                  $currentyear = Carbon::now()->year;
                  $birthyear = Carbon::createFromFormat('Y-m-d', $DoB)->year;

                  $years = $currentyear-$birthyear;
                 
                 if($years > 16) {

                    $category = 'Adult';

                 } else {

                    $category = 'Child';
                 } 
                 } else {

                    $category = '';
                                   
                 }


            $ReqestID = DB::table('OCMRequest')->max('ReqestID')+1;
            if($ReqestID == 1) { 

                $ReqestID = DB::table('Options')->where('Description','MinRequestID')->select('Contents')->get(); 
                $ReqestID = $ReqestID[0]->Contents;

             }

            $RequestEpisodeID = 0;


            $id = DB::table('OCMRequest')->max('id')+1;
            $RequestEpisodeID = DB::table('OCMRequest')->where('RequestPatientID',$patient)->max('RequestEpisodeID')+1;

            DB::insert('insert into OCMRequest 
                (id, ReqestID, RequestEpisodeID, RequestPatientID, RequestClinicianID, RequestWardID, bed, RequestClinicalDetail, RequestFasting, outofhours, RequestNotes, RequestPriority, ExecutionDateTime, RequestCreatedBy, RequestCreatedDateTime, RequestState,RequestType) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $ReqestID, $RequestEpisodeID, $patient, $clinician[$key], $ward[$key], $bed[$key], $clinicalDetail[$key], $fasting, $outofhours, $notes, $priority[$key], $datetime, $user->id, date('Y-m-d H:i:s'), 'Requested', 'Request' ]);


            
              foreach($tests as $key => $test)
            {

            $iid = DB::table('OCMRequestDetails')->max('id')+1;
            DB::insert('insert into OCMRequestDetails (id, RequestID, RequestEpisodeID, TestID, TestDescription, ExecutionDateTime, PatientID) values (?, ?, ?, ?, ?, ?, ?)', 
            [$iid, $ReqestID, $RequestEpisodeID, $test, $description[$key], $datetime, $patient]);



             $TestDefinitionIDs = DB::table('ProfileTestMapping')
                                        ->where('ProfileID',$test)
                                        ->select('TestDefinitionID')
                                        ->get(); 
              

                                         
            foreach($TestDefinitionIDs as $TestDefinitionID) {

                 $TestDefinitionID = $TestDefinitionID->TestDefinitionID;

                  $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                         'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID)
                                        ->get();

                           $info = DB::table('testprofiles')
                                                ->select('department','specialhandling')
                                                ->where('id',$test)
                                                ->get();                                          

                         $units = $TestDefinitions[0]->units;
                         $SampleType = $TestDefinitions[0]->SampleType;
                         $Hospital = $TestDefinitions[0]->Hospital; 
                         $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                         $childrenContainer = $TestDefinitions[0]->childrenContainer; 
                         

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();                                           

                 $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID)->get();

                 if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient, created_by) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient , $user->id]);

                    }

            }





                }




        foreach($tests as $key => $test)
            {

             foreach($TestDefinitionIDs as $TestDefinitionID) { 


                $TestDefinitionID = $TestDefinitionID->TestDefinitionID;        

                  $RTIDs = DB::table('ReflexTestMapping')->select('TestDefinitionID2')->where('TestDefinitionID1',$TestDefinitionID)->get();


                 if(count($RTIDs) > 0) { 

                    foreach($RTIDs as $RTID) {

                         $TestDefinitionID2 = $RTID->TestDefinitionID2;
                            
                          $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                         'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID2)
                                        ->get();

                           $info = DB::table('testprofiles')
                                                ->select('department','specialhandling')
                                                ->where('id',$test)
                                                ->get();                                          

                         $units = $TestDefinitions[0]->units;
                         $SampleType = $TestDefinitions[0]->SampleType;
                         $Hospital = $TestDefinitions[0]->Hospital; 
                         $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                         $childrenContainer = $TestDefinitions[0]->childrenContainer; 
                         

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();                                           

                 $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID2)->get();

                 if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient, created_by) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID2, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient , $user->id]);

                    }


                    }

                 }

                 }

             }




                DB::update("
                
                update OCMRequestQuestionsDetails 
                set 
                
                request = '$ReqestID',
                episode = '$RequestEpisodeID'

                where session = $session and patient = $patient

                ");




            }

               return response()->json(['success'=>'Data Saved.']);


           }
                  
        return response()->json(['error'=>$validator->errors()->first()]);

    }


     public function saveSampleStatus(Request $request)
    {       
            
            $ids = explode(',',$request->id);


         $PhlebotomySampleIDs =  DB::table('OCMPhlebotomy')->where('PhlebotomySampleCollected','!=',null)->where('PhlebotomySampleID',$request->value)->get(); 

            if(count($PhlebotomySampleIDs) > 0) {

                 return response()->json(['success'=> 0, 'msg'=> 'Barcode already scanned']);
             }


             $user = auth()->user();   
            //if($request->id != $request->value)  {
            if (!in_array($request->value, $ids)) {               

                return response()->json(['success'=> 0, 'msg'=> 'Barcode Not Matched']);

            } else {



            $PhlebotomySampleID = DB::table('OCMPhlebotomy')
           ->where('PhlebotomySampleID',$request->value)
           ->select('PhlebotomySampleID')->limit(1)->get(); 

           if(count($PhlebotomySampleID) > 0) {

           DB::update("update OCMRequestTestsDetails  set sampletakenby =  '".$user->id."' where sampleID = '".$request->value."'");

            DB::update("update OCMPhlebotomy  set PhlebotomySampleDateTime =  '".date('Y-m-d H:i:s')."', PhlebotomySampleCollected = 'Yes' where PhlebotomySampleID = '".$request->value."'");





              $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')  
              ->where('request', $request->rid)
              ->where('episode', $request->eid)
              ->get();


              foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {


                 $TestDefinitions = DB::table('TestDefinitions')->select('shortname')
                                    ->where('id', $OCMRequestTestsDetail->test)->get();
                    $test = $TestDefinitions[0]->shortname; 

                    $existResults = DB::table('results')->select('id')
                                    ->where('sampleid', $OCMRequestTestsDetail->sampleID)
                                    ->where('Code', $test)
                                    ->where('department', $OCMRequestTestsDetail->department)
                                    ->where('request', $request->rid)
                                    ->where('episode', $request->eid)
                                    ->where('patient', $OCMRequestTestsDetail->patient)->get();

                    if(count($existResults) == 0) {



                    $id = DB::table('results')->max('id')+1; 

                     DB::insert('insert into results 
                        (id, sampleid, Code, department, request, episode, patient) 
                        values (?, ?, ?, ?, ?, ?, ?)', 
                        [$id, $OCMRequestTestsDetail->sampleID, $test, $OCMRequestTestsDetail->department, $request->rid, $request->eid, $OCMRequestTestsDetail->patient]);  
                    }     

              }

   

             $ocmRequest = DB::table('OCMRequest')
              ->where('OCMRequest.ReqestID', $request->rid)
              ->where('OCMRequest.RequestEpisodeID', $request->eid)
              ->get();
               
           
           $RequestClinicianID = $ocmRequest[0]->RequestClinicianID; 
           $RequestPatientID = $ocmRequest[0]->RequestPatientID; 
           $RequestWardID = $ocmRequest[0]->RequestWardID; 
           $RequestClinicianID = $ocmRequest[0]->RequestClinicianID; 
           $RequestClinicalDetail = $ocmRequest[0]->RequestClinicalDetail;  
           $outofhours = $ocmRequest[0]->outofhours;
           $RequestPriority = $ocmRequest[0]->RequestPriority; 
           $RequestNotes = $ocmRequest[0]->RequestNotes; 
           $Antibiotics = $ocmRequest[0]->Antibiotics; 
           $IntendedAntibiotics = $ocmRequest[0]->IntendedAntibiotics; 
           
            if($outofhours == 'Yes') {

                    $outofhours = 1;
                 } 
                 else {
                    $outofhours = 0;
                 }


                  if($RequestPriority == 'Normal') {

                    $RequestPriority = 0;
                 } 
                 else {
                    $RequestPriority = 1;
                 }


           $ClinicianDetails = DB::table('Clinicians')
              ->select('Text')
              ->where('id', $RequestClinicianID)
              ->get();

           $Clinician = $ClinicianDetails[0]->Text;    

           $WardDetails = DB::table('Wards')
              ->select('Text')
              ->where('id', $RequestWardID)
              ->get();
            
            $Ward = $WardDetails[0]->Text;      
           

           $PatientDetails = DB::table('PatientIFs')
              ->where('id', $RequestPatientID)
              ->get();

           $Chart = $PatientDetails[0]->Chart;
           $PatName = $PatientDetails[0]->PatName;
           $Sex = $PatientDetails[0]->Sex;
           $DoB = $PatientDetails[0]->DoB;
           $Address0 = $PatientDetails[0]->Address0;
           $Address1 = $PatientDetails[0]->Address1;
           $Address2 = $PatientDetails[0]->Address2;
           $Address3 = $PatientDetails[0]->Address3;
           $GP = $PatientDetails[0]->GP;
           $AandE = $PatientDetails[0]->AandE;


              
           //
           

            $connectionInfo_hq = array("Database"=>"CavanTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
            $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);

            if( $conn_hq ) {
                

             $tsql = "SELECT max(ID) as serverID FROM ocmRequest";
             $getProducts = sqlsrv_query($conn_hq, $tsql);
             $serverID = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
             if($serverID['serverID'] == null) {

                    $serverID = 1;

             } else {

                    $serverID = $serverID['serverID']+1;
             }      


             $tsql = "SELECT * FROM ocmRequest where RequestID = '$request->rid' ";
                                 $getlist = sqlsrv_query($conn_hq, $tsql);
                                  $row222 = sqlsrv_fetch_array($getlist, SQLSRV_FETCH_ASSOC);

                       if(empty($row222) == 1) {


              $tsql = "INSERT INTO ocmRequest (ID, RequestID, OrderingClinician, RequestState, Chart, PatName, Sex, DoB, Addr0, Addr1, Addr2, Addr3, Ward, Clinician, ClDetails, RooH, AandE, Urgent, GP, Hospital,notes,antibiotics,intendedantibiotics) 
                             VALUES 
                            (
                                $serverID,
                                $request->rid, 
                                '".$Clinician."', 
                                'Sent to the lab', 
                                '".$Chart."', 
                                '".$PatName."', 
                                '".$Sex."', 
                                '".$DoB."', 
                                '".$Address0."', 
                                '".$Address1."', 
                                '".$Address2."', 
                                '".$Address3."', 
                                '".$Ward."', 
                                '".$Clinician."',  
                                '".$RequestClinicalDetail."',
                                '".$outofhours."',
                                '".$AandE."',
                                '".$RequestPriority."',
                                '".$GP."',
                                'Cavan',
                                '".$RequestNotes."',
                                '".$Antibiotics."',
                                '".$IntendedAntibiotics."'  

                            )";
                            $insertReview = sqlsrv_query($conn_hq, $tsql);

                        }



            $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')
              ->where('request', $request->rid)
              ->where('episode', $request->eid)
              ->get();


              $tsql1 = "delete from ocmrequestdetails where RequestID = '$request->rid' ";
                        sqlsrv_query($conn_hq, $tsql1);

              foreach($OCMRequestTestsDetails as $OCMRequestTestsDetail) {

                 $tsql = "SELECT max(ID) as serverID FROM ocmRequestDetails";
                 $getProducts = sqlsrv_query($conn_hq, $tsql);
                 $serverID = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
                 if($serverID['serverID'] == null) {

                        $serverID = 1;

                 } else {

                        $serverID = $serverID['serverID']+1;
                 }

                 $profileID = $OCMRequestTestsDetail->profileID;
                 $test = $OCMRequestTestsDetail->test;
                 $sampletype = $OCMRequestTestsDetail->sampletype;
                 $department = $OCMRequestTestsDetail->department;
                 $hospital = $OCMRequestTestsDetail->hospital;
                 $sampleID = $OCMRequestTestsDetail->sampleID;

                    $OCMPhlebotomy = DB::table('OCMPhlebotomy')->select('PhlebotomySampleDateTime','PhlebotomySampleCollected')->where('PhlebotomySampleID', $sampleID)->get();
                    $PhlebotomySampleDateTime = $OCMPhlebotomy[0]->PhlebotomySampleDateTime; 
                    $PhlebotomySampleCollected = $OCMPhlebotomy[0]->PhlebotomySampleCollected; 

                    $TestDefinitions = DB::table('TestDefinitions')->select('shortname','Hospital')->where('id', $test)->get();
                    $test = $TestDefinitions[0]->shortname; 
                    $facility = $TestDefinitions[0]->Hospital;
                    $extInt = DB::table('facilities')->select('type')->where('id', $facility)->get(); 

                    if($extInt[0]->type == 'External') {

                        $facility = 1;
                    } 
                    else {

                        $facility = 0;
                    }

                    $sampletype = DB::table('Lists')->select('Code')->where('id', $sampletype)->get();
                    $sampletype = $sampletype[0]->Code; 

                    $department = DB::table('Lists')->select('Code')->where('id', $department)->get();
                    $department = $department[0]->Code; 

                    $testprofiles = DB::table('testprofiles')->select('name')->where('id', $profileID)->get();
                    $profile = $testprofiles[0]->name;  


                    $ocmrequestdetails = DB::table('ocmrequestdetails')->select('TestDescription')
                    ->where('TestID', $profileID)
                    ->where('RequestID', $request->rid)
                    ->where('RequestEpisodeID', $request->eid)
                    ->get();
                    $TestDescription = $ocmrequestdetails[0]->TestDescription;  



                    
                if($PhlebotomySampleCollected == 'Yes') {

                 
                 $tsql = "INSERT INTO ocmRequestDetails (ID, RequestID, SampleID, SampleDate, TestCode, TestDescription, SampleType, DepartmentID, ProfileID, Programmed, external_) 
                             VALUES 
                            ($serverID, $request->rid, $sampleID, '".$PhlebotomySampleDateTime."', '".$test."',' ".$TestDescription."', '".$sampletype."', '".$department."', '".$profile."', 0, '".$facility."')";
                            $insertReview = sqlsrv_query($conn_hq, $tsql);

                             DB::update("update results  set 
                                      external = '".$facility."'    
                                     where  
                                     Code = '".$test."' and
                                     request = '".$request->rid."' and sampleid = '".$sampleID."' ");

                         }   


                }

            $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
              ->where('request', $request->rid)
              ->where('episode', $request->eid)
              ->get();



               $del = "delete from ocmQuestions  where rid = ".$request->rid;
               sqlsrv_query($conn_hq, $del);

              foreach($OCMRequestQuestionsDetails as $OCMRequestQuestionsDetails) {

                
                $tsql = "SELECT max(id) as qid FROM ocmQuestions";
                 $getProducts = sqlsrv_query($conn_hq, $tsql);
                 $qid = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
                 if($qid['qid'] == null) {

                        $qid = 1;

                 } else {

                        $qid = $qid['qid']+1;
                 }

             $tsql = "INSERT INTO ocmQuestions (id, rid, eid, question, answer, date_time) 
                     VALUES 
                    ($qid, $OCMRequestQuestionsDetails->request, $OCMRequestQuestionsDetails->episode, 
                         '".$OCMRequestQuestionsDetails->question."' , 
                         '".$OCMRequestQuestionsDetails->answer."' , 
                          '".date('Y-m-d H:i:s') ."' 
                        
                        )";
                    $insertReview = sqlsrv_query($conn_hq, $tsql);

              }


             }
             //  







            return response()->json(['success'=> 1, 'msg'=> 'Barcode Matched', 'checked'=> $request->value]);

           } else {

             return response()->json(['success'=> 0, 'msg'=> 'Barcode Not Matched']);
           }
       }

            

             
        
          
    } 

     public function CheckAddOnAvailability(Request $request)
    {
        
        
        if($request->id != '' && $request->rid != '') {   


         $ids = explode(',',$request->id);  

           



           
            
         $ReqestID = DB::table('OCMRequest')
           ->where('id',$request->rid)
           ->select('ReqestID')->get(); 


          $patient = DB::table('ocmrequestdetails')
           ->where('RequestID',$ReqestID[0]->ReqestID)
           ->select('PatientID')->get(); 


           foreach($ids as $profiles) {


                    if($profiles == 21) {

                        $date = Carbon::now()->subHours(4);
                        $profiletestmapping = DB::table('profiletestmapping')
                       ->whereIn('ProfileID', $ids)
                       ->select('TestDefinitionID');

                       $profiletestmapping[0]->TestDefinitionID;


                    } else {

                       $date = Carbon::now()->subDays(3);
                       $profiletestmapping = DB::table('profiletestmapping')
                       ->whereIn('ProfileID', $ids)
                       ->select('TestDefinitionID'); 
                         
                     }

           }

        




          $SampleType = DB::table('testdefinitions')
           ->whereIn('id',$profiletestmapping)
           ->pluck('SampleType');

           $hospital = DB::table('testdefinitions')
           ->whereIn('id',$profiletestmapping)
           ->pluck('hospital'); 



            

          $labels = DB::table('ocmrequesttestsdetails')
           ->select('ocmphlebotomy.PhlebotomySampleDateTime as taken','ocmrequesttestsdetails.sampleid', 'ocmrequesttestsdetails.profileID','Lists.Text as sampletype','facilities.name as hospital') 
           ->leftjoin('ocmphlebotomy', 'ocmphlebotomy.PhlebotomySampleID', '=', 'ocmrequesttestsdetails.sampleid')  
           ->leftjoin('Lists', 'Lists.id', '=', 'ocmrequesttestsdetails.sampletype')   
           ->leftjoin('facilities', 'facilities.id', '=', 'ocmrequesttestsdetails.hospital')   
           ->where('ocmrequesttestsdetails.patient',$patient[0]->PatientID)
           ->where('ocmphlebotomy.PhlebotomySampleDateTime', '>=', $date)
           ->whereIn('ocmrequesttestsdetails.sampletype', $SampleType)
           ->whereIn('ocmrequesttestsdetails.hospital', $hospital)

           ->orderBy('ocmphlebotomy.PhlebotomySampleDateTime','desc')
           ->groupBy('ocmrequesttestsdetails.sampleid')->get(); 


             return \Response::json($labels); 

        }

    }       


     public function checkPendingSamples(Request $request)
    {

             


        $totalSamples = DB::table('OCMPhlebotomy')
           ->where('PhlebotomyRequestID',$request->rid)
           ->select('PhlebotomySampleID')->get(); 

        $collectedSamples = DB::table('OCMPhlebotomy')
           ->where('PhlebotomyRequestID',$request->rid)
           ->where('PhlebotomySampleCollected',null)
           ->select('PhlebotomySampleID')->get(); 
              
            if(count($totalSamples) == count($collectedSamples)) {

                return 1;
            } 
            elseif(count($collectedSamples) > 0) {

               $items = DB::table('OCMPhlebotomy')
                    ->select('PhlebotomySampleDateTime')
                    ->whereBetween('PhlebotomySampleDateTime', [now()->subMinutes(5), now()])->get();   

                        if(count($items) > 0) {

                            return $collectedSamples;
                        } 
                        else {

                            return 1;
                        }
                         
                        
                        
            } 
            else {

                return 1;
            }
    }


    public function createRequestFromPendingSamples(Request $request)
    {

       
       $OCMRequest = OCMRequest::where('ReqestID', $request->rid)->get();
       $id = $OCMRequest[0]->id;
       $RequestEpisodeID = $OCMRequest[0]->RequestEpisodeID+1;

                if($id > 0) {

                    $ocmID = DB::table('OCMRequest')->max('id')+1;  
                    $ocmRID = DB::table('OCMRequest')->max('ReqestID')+1;  
                    $OCMRequest = OCMRequest::find($id); 
                    $new_data = $OCMRequest->replicate();
                    $new_data->id = $ocmID;
                    $new_data->ReqestID = $ocmRID;
                    $new_data->RequestEpisodeID = $RequestEpisodeID; 
                    $new_data->PatientConfirmation = 0; 
                    $new_data->RequestState = 'Requested';
                    $new_data->save();


                    $PhlebotomySampleIDs = DB::table('OCMPhlebotomy')
                           ->where('PhlebotomyRequestID',$request->rid)
                           ->where('PhlebotomySampleCollected',null)
                           ->select('PhlebotomySampleID')->get(); 


                            if(count($PhlebotomySampleIDs) > 0) {

                                foreach($PhlebotomySampleIDs as $PhlebotomySampleID) {


                                    DB::update("update ocmphlebotomy  set 
                                      PhlebotomyRequestID = '".$ocmRID."',
                                       PhlebotomyRequestEpisodeID = '".$RequestEpisodeID."'       
                                     where  PhlebotomySampleID = '".$PhlebotomySampleID->PhlebotomySampleID."' ");


                                     DB::update("update ocmrequesttestsdetails  set  
                                    request = '".$ocmRID."',
                                    episode = '".$RequestEpisodeID."'
                                     where request = '".$request->rid."' and  sampleID = '".$PhlebotomySampleID->PhlebotomySampleID."' ");  


                                     DB::update("update results  set  
                                    request = '".$ocmRID."',
                                    episode = '".$RequestEpisodeID."'
                                     where request = '".$request->rid."' and  sampleID = '".$PhlebotomySampleID->PhlebotomySampleID."' ");   
                                
                                 }

                                return 1; 
                                
                            } 

                

                }
                
            
            


        
    }        

     public function checkSampleStatus(Request $request)
    {
            
        $PhlebotomySampleIDs = DB::table('OCMPhlebotomy')
           ->where('PhlebotomyRequestID',$request->rid)
           ->where('PhlebotomyRequestEpisodeID',$request->eid)
           ->where('PhlebotomySampleCollected',null)
           ->select('PhlebotomySampleID')->get(); 

            if(count($PhlebotomySampleIDs) > 0) {

              $PhlebotomySampleIDArray = array();

              foreach($PhlebotomySampleIDs as $PhlebotomySampleID) { 


                    $PhlebotomySampleIDArray[] = $PhlebotomySampleID->PhlebotomySampleID;

              }     
             
             DB::update("update OCMRequest  set  RequestState = 'Sent to the lab' where  ReqestID = '".$request->rid."' and RequestEpisodeID = '".$request->eid."'  ");

             return response()->json(['sampleID'=> $PhlebotomySampleIDArray]);

            } else {

                    

                 $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$request->rid,$request->eid,0,0,0,'Request','Samples have been collected for the Request # '.$request->rid.'/'.$request->eid.'.']);   

                DB::update("update OCMRequest  set  RequestState = 'Sent to the lab' where  ReqestID = '".$request->rid."' and RequestEpisodeID = '".$request->eid."'  ");


                return response()->json(['sampleID'=> '']);
            }

            
        
          
    } 


     public function discardSamplesInfo(Request $request)
    {
            
             
           DB::update("update OCMRequest  set  RequestState = 'Requested' where  ReqestID = '".$request->rid."' and RequestEpisodeID = '".$request->eid."'  ");

             DB::update("update OCMRequestTestsDetails  set  sampleID = null where  request = '".$request->rid."' and episode = '".$request->eid."'  ");
         
         return DB::table('OCMPhlebotomy')->where('PhlebotomyRequestID', $request->rid)->where('PhlebotomyRequestEpisodeID', $request->eid)->delete();        
        
          
    } 


     public function discardSamplesID(Request $request)
    {
                
            
        $profiles = DB::table('ocmrequesttestsdetails')->where('sampleID', $request->id)->pluck('profileID');

            if(count($profiles) > 0) {


                DB::table('ocmrequestdetails')->whereIn('TestID', $profiles)->delete();  

            }

            DB::table('OCMPhlebotomy')->where('PhlebotomySampleID', $request->id)->delete();
            DB::table('ocmrequesttestsdetails')->where('sampleID', $request->id)->delete();
            DB::table('results')->where('sampleid', $request->id)->delete();


         

             $data = DB::table('OCMPhlebotomy')->select('PhlebotomyRequestID')
            ->where('PhlebotomyRequestID',$request->rid)
            ->where('PhlebotomySampleCollected','=',null) 
            ->get();

            if(count($data) == 0) {

               return DB::update("update OCMRequest  set  RequestState = 'Sent to the lab' where  ReqestID = $request->rid ");

            } else {

                return 0;
            }

          
    } 





     public function SaveProfileQuestions(Request $request)
    {
            

     if($request->id != '' && $request->answer != '')  {


           return  DB::update("update OCMRequestQuestionsDetails  set answer = '".$request->answer."' where id = '".$request->id."'");
        } 
            
          
    } 


     public function ClearProfileQuestions(Request $request)
    {
            

     if($request->session != '')  {

        $data = DB::table('OCMRequestQuestionsDetails')->select('request','episode')->where('request','!=',null)->where('session', $request->session)->get();

        if(count($data) > 0) {

            $requestID = $data[0]->request;
            $episodeID = $data[0]->episode;

            $state = DB::table('OCMRequest')->select('RequestState')->where('ReqestID', $requestID)->where('RequestEpisodeID', $episodeID)->get();

            if(count($state) > 0) { 


                    if($state[0]->RequestState == 'Requested') {

                        return DB::table('OCMRequestQuestionsDetails')->where('session', $request->session)->delete(); 
                    }
            }

         DB::table('OCMRequestQuestionsDetails')->where('request',null)->where('session', $request->session)->delete(); 
             

        } else {

            return DB::table('OCMRequestQuestionsDetails')->where('session', $request->session)->delete(); 
        }


        

        } 
            
          
    } 


    public function GetProfileQuestions(Request $request)
    
    {
                
     if($request->id) {

         $ids = explode(',',$request->id);
                
           $data = DB::table('ProfileQuestionMapping')
              ->select('ProfileQuestionMapping.QuestionID','ProfileQuestions.question','ProfileQuestions.answers')
              ->leftjoin('ProfileQuestions', 'ProfileQuestions.ID', '=', 'ProfileQuestionMapping.QuestionID')
              ->whereIn('ProfileQuestionMapping.ProfileID', $ids)
              ->get();

            if(count($data) > 0) {

                  
                foreach($data as $value) {

                  $id = DB::table('OCMRequestQuestionsDetails')->max('id')+1;

                  DB::table('OCMRequestQuestionsDetails')->insertOrIgnore([
                        ['id' => $id, 'QuestionID' => $value->QuestionID, 'question' => $value->question, 'answers' => $value->answers, 'session' => $request->session]
                    ]);

                }

                    
                     $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')->select('request','episode')->where('request','!=',null)->where('session', $request->session)->get();

                        if(count($OCMRequestQuestionsDetails) > 0) {

                            $requestID = $OCMRequestQuestionsDetails[0]->request;
                            $episodeID = $OCMRequestQuestionsDetails[0]->episode;


                            $state = DB::table('OCMRequest')->select('RequestState')->where('ReqestID', $requestID)->where('RequestEpisodeID', $episodeID)->get();

                                if(count($state) > 0) { 


                                        if($state[0]->RequestState != 'Requested' && $state[0]->RequestState != 'Cancelled') {

                                           
                                            $data = DB::table('OCMRequestQuestionsDetails')
                                              ->where('OCMRequestQuestionsDetails.session', $request->session)
                                              ->where('request',null)
                                              ->get();  

                                             return \Response::json($data);     

                                        }
                                }

                                  
                        } else {

                             $data = DB::table('OCMRequestQuestionsDetails')
                                  ->where('OCMRequestQuestionsDetails.session', $request->session)
                                  ->get();   
                    
                                return \Response::json($data);  

                          

                        }


                                     

            }

        } 
            
          
    } 




public function getBTQuestions(Request $request)
    
    {
                
     if($request->id) {

         $ids = explode(',',$request->id);
                
           $data = DB::table('btquestionmapping')
              ->select('btquestionmapping.QuestionID','ProfileQuestions.question','ProfileQuestions.answers')
              ->leftjoin('ProfileQuestions', 'ProfileQuestions.ID', '=', 'btquestionmapping.QuestionID')
              ->whereIn('btquestionmapping.ProfileID', $ids)
              ->orderBy('ProfileQuestions.question')
              ->get();

            if(count($data) > 0) {

        
                     return view('layouts.loadbtquestions', compact('data'))->render();  

                                     

            }

        } 
            
          
    } 




     public function requestEpisode(Request $request)
    {


        if((\App\Http\Controllers\users::roleCheck('Requests','Self_Created',0)) == 'Yes') { 

      
                $ocmrequest = DB::table('ocmrequest') 
                                     ->select('id')
                                     ->where('ReqestID',$request->rid)
                                     ->where('RequestCreatedBy',Auth::user()->id)
                                     ->get();

                    if(count($ocmrequest) == 0) {

                        return redirect('/home');
                    }                                 


               } 
               

        if($request->rid && $request->eid) {
             

             $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $ExecutionDateTime = $date;
        $Fasting = '';
        $Priority = '';
        $mode = 'clone';
        $OCMRequest = '';
        $OCMRequestDetails = '';
        $OCMRequestQuestionsDetails = '';
        $Visits = 0;


        $quicktestprofiles = DB::table('quicktestprofiles') 
                         ->select(
                            'testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf'
                            )
                         ->join('testprofiles', 'testprofiles.id', '=', 'quicktestprofiles.profileID')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'quicktestprofiles.profileID')
                         ->where('testprofiles.InUse', 1)
                         ->orderBy('quicktestprofiles.profileID', 'asc')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();
        
                     
        $quicktestprofilesArray = array();

          foreach($quicktestprofiles as $quicktestprofile) {

                $quicktestprofilesArray[] = $quicktestprofile->id;
          }  

        $testprofiles = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf',
                            'Lists.Text')
                         ->join('Lists', 'Lists.id', '=', 'testprofiles.department')
                         ->leftjoin('users AS A', 'A.id', '=', 'testprofiles.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'testprofiles.updated_by')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'testprofiles.id')
                         ->where('testprofiles.InUse', 1)
                         ->where('Lists.ListType', 'DPT')
                         ->whereNotIn('testprofiles.id',$quicktestprofilesArray)
                         ->orderBy('testprofiles.name')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();

        $Wards = DB::table('Wards') 
                         ->select('Wards.id','Wards.Text')
                         ->where('Wards.InUse', 1)
                         ->get();

        $Clinicians = DB::table('Clinicians') 
                         ->select('Clinicians.id','Clinicians.Text','Clinicians.Title','Clinicians.ForeName','Clinicians.SurName')
                         ->where('Clinicians.InUse', 1)
                         ->get();

        $States = DB::table('Lists') 
                         ->select('Lists.id','Lists.Text')
                         ->where('Lists.InUse', 1)
                         ->where('Lists.ListType', 'ST')
                         ->where('Lists.Text','!=','')
                         ->orderBy('ListOrder')
                         ->get();
            
          $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.*',
                                   'OCMRequest.id as OID',
                                    'PatientIFs.*', 
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.ReqestID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP) as Visits')
                                    )  
                          ->join('PatientIFs', 'PatientIFs.id', '=', 'OCMRequest.RequestPatientID')
                          ->where('ReqestID', $request->rid)
                          ->where('RequestEpisodeID', $request->eid)
                          ->get();

    
            
          $data = [
                    'mode' => $mode,
                    'OCMRequest' => $OCMRequest,
                    'OCMRequestDetails' => $OCMRequestDetails,
                    'OCMRequestQuestionsDetails' => $OCMRequestQuestionsDetails,
                    'Clinicians' => $Clinicians,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'Wards' => $Wards,
                    'Fasting' => $Fasting,
                    'Priority' => $Priority,
                    'States' => $States,
                    'testprofiles' => $testprofiles,
                    'quicktestprofiles' => $quicktestprofiles
          ];  

          return view ('request')->with('data',$data);

                
        }
    } 




    public function requestPatient(Request $request)
    {


        if($request->pid) {
             

        $now = Carbon::now();
        $date =  $now->format('Y-m-d\TH:i'); 
        $ExecutionDateTime = $date;
        $Fasting = '';
        $Priority = '';
        $mode = 'clonePatient';
        $OCMRequest = '';
        $OCMRequestDetails = '';
        $OCMRequestQuestionsDetails = '';
        $Visits = 0;


        $quicktestprofiles = DB::table('quicktestprofiles') 
                         ->select(
                            'testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf'
                            )
                         ->join('testprofiles', 'testprofiles.id', '=', 'quicktestprofiles.profileID')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'quicktestprofiles.profileID')
                         ->where('testprofiles.InUse', 1)
                         ->orderBy('quicktestprofiles.profileID', 'asc')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();
        
                     
        $quicktestprofilesArray = array();

          foreach($quicktestprofiles as $quicktestprofile) {

                $quicktestprofilesArray[] = $quicktestprofile->id;
          }  

        $testprofiles = DB::table('testprofiles') 
                         ->select('testprofiles.id',
                            'testprofiles.name',
                            'testprofiles.dppHours',
                            'testprofiles.diagnostics',
                            'testprofiles.rcf',
                            'Lists.Text')
                         ->join('Lists', 'Lists.id', '=', 'testprofiles.department')
                         ->leftjoin('users AS A', 'A.id', '=', 'testprofiles.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'testprofiles.updated_by')
                         ->join('ProfileTestMapping', 'ProfileTestMapping.ProfileID', '=', 'testprofiles.id')
                         ->where('testprofiles.InUse', 1)
                         ->where('Lists.ListType', 'DPT')
                         ->whereNotIn('testprofiles.id',$quicktestprofilesArray)
                         ->orderBy('testprofiles.name')
                         ->groupBy('ProfileTestMapping.ProfileID')
                         ->get();

        $Wards = DB::table('Wards') 
                         ->select('Wards.id','Wards.Text')
                         ->where('Wards.InUse', 1)
                         ->get();

        $Clinicians = DB::table('Clinicians') 
                         ->select('Clinicians.id','Clinicians.Text','Clinicians.Title','Clinicians.ForeName','Clinicians.SurName')
                         ->where('Clinicians.InUse', 1)
                         ->get();

        $States = DB::table('Lists') 
                         ->select('Lists.id','Lists.Text')
                         ->where('Lists.InUse', 1)
                         ->where('Lists.ListType', 'ST')
                         ->where('Lists.Text','!=','')
                         ->orderBy('ListOrder')
                         ->get();
        
        $PatientIFs = DB::table('PatientIFs') 
                         ->select('PatientIFs.*')
                         ->where('PatientIFs.id',$request->pid)
                         ->get();    


    
            
          $data = [
                    'mode' => $mode,
                    'OCMRequest' => '',
                    'OCMRequestDetails' => '',
                    'OCMRequestQuestionsDetails' => '',
                    'Clinicians' => $Clinicians,
                    'ExecutionDateTime' => $ExecutionDateTime,
                    'Wards' => $Wards,
                    'Fasting' => $Fasting,
                    'Priority' => $Priority,
                    'States' => $States,
                    'testprofiles' => $testprofiles,
                    'quicktestprofiles' => $quicktestprofiles,
                    'Patient' => $PatientIFs
          ];  

          return view ('request')->with('data',$data);

                
        }
    } 






    public function changeStatus(Request $request)
    {

        if($request->id && $request->state) {

         $user = auth()->user();   
             DB::update("
                update OCMRequest 
                set 
                
                RequestState = '".$request->state."',
                RequestModifiedBy = '".$user->id."',
                RequestModifiedDateTime = '".date('Y-m-d H:i:s')."'

                where id = '".$request->id."'
                ");


            $OCMRequest = DB::table('OCMRequest')
                          ->select('OCMRequest.ReqestID','OCMRequest.RequestEpisodeID','OCMRequest.RequestPatientID')  
                          ->where('id', $request->id)
                          ->get();
            $ReqestID = $OCMRequest[0]->ReqestID;
            $RequestEpisodeID = $OCMRequest[0]->RequestEpisodeID;
            $RequestPatientID = $OCMRequest[0]->RequestPatientID;
           

            return response()->json(['success'=>'Status Updated.']);
                
        }
    }  




      public function PatientList(Request $request)
    {   


           if ($request->ajax()) {



                if(!empty($request->mrn) || !empty($request->wards) || !empty($request->clinicians))
                  {


                      $data = DB::table('PatientIFs')
                         ->select('PatientIFs.*', 
                                  'PatientIFs.Ward as WardID', 
                                  'PatientIFs.Clinician as ClinicianID', 
                                  'Wards.Text as Wards',  
                                  'Clinicians.Text as Clinicians',

                               

                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id order by OCMRequest.id desc limit 1) as RequestClinicalDetail'),
                                    
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP)+1 as Visits')
                                    )
                                    ->leftjoin('Wards', 'Wards.id', '=', 'PatientIFs.Ward')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'PatientIFs.Clinician')
                                    //->where('PatientIFs.state','Admitted')
                                    ->when(!empty($request->mrn) , function ($query) use($request){
                                    return $query->where('PatientIFs.Chart',$request->mrn);
                                    })
                                    ->when(!empty($request->wards) , function ($query) use($request){
                                    return $query->where('PatientIFs.Ward',$request->wards);
                                    })
                                    ->when(!empty($request->clinicians) , function ($query) use($request){
                                    return $query->where('PatientIFs.Clinician',$request->clinicians);
                                    })
                                    ->get();
                  } 

                else {
                

                 $user = auth()->user();
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
                  //print_r($result);
                 $id = implode(",",$result);

            }

                $data = DB::table('PatientIFs')
                         ->select('PatientIFs.*', 
                                  'PatientIFs.Ward as WardID', 
                                  'PatientIFs.Clinician as ClinicianID', 
                                  'Wards.Text as Wards',  
                                  'Clinicians.Text as Clinicians',


                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP)+1 as Visits')
                                    )
                                   // ->where('PatientIFs.state','Admitted')
                                    ->leftjoin('Wards', 'Wards.id', '=', 'PatientIFs.Ward')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'PatientIFs.Clinician')
                                    ->orderByRaw("FIELD(PatientIFs.id , $id) Desc");
                               

                     }           

                    return Datatables::of($data)
                                ->addColumn('action', function($data){

                                     $RequestClinicalDetails = DB::table('OCMRequest')
                                             ->select('OCMRequest.RequestClinicalDetail')
                                             ->where('OCMRequest.RequestPatientID',$data->id)
                                             ->limit(1)
                                             ->orderBy('OCMRequest.id','desc')
                                             ->get();
                                        
                                        if(count($RequestClinicalDetails) > 0) {
                                            $RequestClinicalDetail = $RequestClinicalDetails[0]->RequestClinicalDetail;
                                        } else {
                                            $RequestClinicalDetail = '';
                                        }

                                            $btns = '<div class="btn-group" role="group" >
                                            <button  
                                                id="'. $data->id .'" 
                                                name="'. $data->PatName .'"  
                                                ward="'. $data->WardID .'"  
                                                bed="'. $data->BedNumber .'"  
                                                Chart="'. $data->Chart .'"  
                                                clinician="'. $data->ClinicianID .'" 
                                                clinicianDetails = "'. $RequestClinicalDetail .'" 
                                                Sex="'. $data->Sex .'" 
                                                DoB="'. $data->DoB .'" 
                                                MRN="'. $data->MRN .'" 
                                                Address0="'. $data->Address0 .'"
                                                Visits="'. $data->Visits .'" 
                                                    class="addPatient btn btn-secondary"><i class="bx bx-check"></i></button>
                                            </div>';

                                            return $btns;
                                }) 
                        
                            ->rawColumns(['action'])
                            ->make(true);

                    
                  
        }
        
        return view ('Request');
    }



     public function PatientList2(Request $request)
    {   


           if ($request->ajax()) {



                 if(!empty($request->mrn) || !empty($request->wards) || !empty($request->clinicians))
                  {

                      $data = DB::table('PatientIFs')
                         ->select('PatientIFs.*', 
                                  'PatientIFs.Ward as WardID', 
                                  'PatientIFs.Clinician as ClinicianID', 
                                   'Wards.Text as Wards',  
                                  'Clinicians.Text as Clinicians',

                               

                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id order by OCMRequest.id desc limit 1) as RequestClinicalDetail'),
                                    
                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP)+1 as Visits')
                                    )
                                    ->leftjoin('Wards', 'Wards.id', '=', 'PatientIFs.Ward')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'PatientIFs.Clinician')
                                   // ->where('PatientIFs.state','Admitted')
                                    ->when(!empty($request->mrn) , function ($query) use($request){
                                    return $query->where('PatientIFs.Chart',$request->mrn);
                                    })
                                    ->when(!empty($request->wards) , function ($query) use($request){
                                    return $query->where('PatientIFs.Ward',$request->wards);
                                    })
                                    ->when(!empty($request->clinicians) , function ($query) use($request){
                                    return $query->where('PatientIFs.Clinician',$request->clinicians);
                                    })
                                    ->get();
                  } 

                else {
            
               
                 $user = auth()->user();
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
                  //print_r($result);
                 $id = implode(",",$result);

            }

                $data = DB::table('PatientIFs')
                         ->select('PatientIFs.*', 
                                  'PatientIFs.Ward as WardID', 
                                  'PatientIFs.Clinician as ClinicianID', 
                                  'Wards.Text as Wards',  
                                  'Clinicians.Text as Clinicians',


                                    DB::raw('(SELECT COUNT(*) FROM OCMRequest WHERE OCMRequest.RequestPatientID = PatientIFs.id and OCMRequest.ExecutionDateTime < CURRENT_TIMESTAMP)+1 as Visits')
                                    )
                                    //->where('PatientIFs.state','Admitted')
                                    ->leftjoin('Wards', 'Wards.id', '=', 'PatientIFs.Ward')
                                    ->leftjoin('Clinicians', 'Clinicians.id', '=', 'PatientIFs.Clinician')
                                    ->orderByRaw("FIELD(PatientIFs.id , $id) Desc");
                               

                     }           

                   
                   return Datatables::of($data)
                                ->addColumn('action', function($data){

                                     $RequestClinicalDetails = DB::table('OCMRequest')
                                             ->select('OCMRequest.RequestClinicalDetail')
                                             ->where('OCMRequest.RequestPatientID',$data->id)
                                             ->limit(1)
                                             ->orderBy('OCMRequest.id','desc')
                                             ->get();
                                        
                                        if(count($RequestClinicalDetails) > 0) {
                                            $RequestClinicalDetail = $RequestClinicalDetails[0]->RequestClinicalDetail;
                                        } else {
                                            $RequestClinicalDetail = '';
                                        }

                                            $btns = '<div class="btn-group" role="group" >
                                            <button  
                                                id="'. $data->id .'" 
                                                name="'. $data->PatName .'"  
                                                ward="'. $data->WardID .'"  
                                                bed="'. $data->BedNumber .'"  
                                                Chart="'. $data->Chart .'"  
                                                clinician="'. $data->ClinicianID .'" 
                                                clinicianDetails = "'. $RequestClinicalDetail .'" 
                                                Sex="'. $data->Sex .'" 
                                                DoB="'. $data->DoB .'" 
                                                MRN="'. $data->MRN .'" 
                                                Address0="'. $data->Address0 .'"
                                                Visits="'. $data->Visits .'" 
                                                    class="addPatient btn btn-secondary"><i class="bx bx-check"></i></button>
                                            </div>';

                                            return $btns;
                                }) 
                        
                            ->rawColumns(['action'])
                            ->make(true);

                    
                  
        }
        
        return view ('Request');
    }



     public function saveRequestSamples(Request $request)
    {

        $user = auth()->user();
        $ids = $request->input('id');
        $requestID = $request->input('requestID');
        $episode = $request->input('episode');
        $prints = $request->input('print');
        $comments = $request->input('comments');
        $specialhandling = $request->input('specialhandling');
        $printS = $request->input('printS');
        
        
          $validator = Validator::make($request->all(), [

            'id.*'  => 'required'
        ]);


           if ($validator->passes()) {

                    foreach($ids as $key => $id) {

                     $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')->select('test','sampletype','hospital','department','containerID')->where('sample',$id)->get(); 

                    //     $PhlebotomyID = DB::table('OCMPhlebotomy')->max('PhlebotomyID')+1;                     

                    //     $ID = DB::table('OCMPhlebotomy')
                    //         ->whereDate('PhlebotomyCreatedDateTime', Carbon::today())
                    //         ->max('PhlebotomySampleID')+1;
                       
                    // if($ID == 1) { 

                        
                    //     $ID = sprintf( "%d%d", date('ymd'), 1001 );

                    //  }

                     $PhlebotomyID = DB::table('OCMPhlebotomy')->max('PhlebotomyID')+1;                     

                        $ID = DB::table('OCMPhlebotomy')->max('PhlebotomySampleID')+1;
                       
                        if($ID == 1) { 

                        $ID = DB::table('Options')->where('Description','MinSampleID')->select('Contents')->get(); 
                        $ID = $ID[0]->Contents;

                         }



                DB::insert('insert into OCMPhlebotomy (PhlebotomyID, specialhandling, PhlebotomyRequestID, PhlebotomyRequestEpisodeID, sampletype, hospital, department, PhlebotomySampleID, PhlebotomyComment, PhlebotomyCreatedBy, PhlebotomyCreatedDateTime, containerID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                [$PhlebotomyID, $specialhandling[$key], $requestID, $episode,  $OCMRequestTestsDetails[0]->sampletype, $OCMRequestTestsDetails[0]->hospital, $OCMRequestTestsDetails[0]->department, $ID, $comments[$key], $user->id, date('Y-m-d H:i:s'), $OCMRequestTestsDetails[0]->containerID  ]);


                         DB::update("
                            update OCMRequestTestsDetails 
                            set 
                            sampleID = '$ID'
                            where 
                            request = '".$requestID."' and
                            episode = '".$episode."' and
                            sample = '".$id."' 
                            ");  

                    }


                $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$requestID,$episode,0,0,0,'Request','Samples Labels have been generated for the Request # '.$requestID.'/'.$episode.'.']); 
                   
                    
                if($printS == 'yes') {

                    return response()->json(['success'=>'Request Samples Info Added.', 'rid' => $requestID, 'eid' => $episode ]);

                } else {

                     return response()->json(['success'=>'Request Samples Info Added.', 'rid' => $requestID, 'eid' => $episode]);
                }
                

                }

        return response()->json(['error'=>$validator->errors()->first()]);


    }



     public function saveRequestSamplesNotes(Request $request)
    {

        $user = auth()->user();
        $id = $request->input('id');
        $requestID = $request->input('requestID');
        $episode = $request->input('episode');
        $comments = $request->input('comments');
        
        
        foreach($comments as $key => $comment) {

                        
          // DB::update("update OCMPhlebotomy  set PhlebotomyComment =  '".$comment."' where OCMRequestTestsDetailsID = '".$id[$key]."'");


         }

        
    }



     public function saveRequest(Request $request)
    {
            
        $user = auth()->user();
        $session = $request->input('session');
        $patient = $request->input('patient');
        $clinician = $request->input('clinician');
        $clinic = $request->input('clinic');
        $clinicalDetail = $request->input('clinicalDetail');
        $ward = $request->input('ward');
        $bed = $request->input('bed');
        $datetimes = $request->input('datetime');
        $fasting = $request->input('fasting');
        $outofhours = $request->input('outofhours');
        $priority = $request->input('priority');
        $notes = $request->input('notes');
        $rtype = $request->input('rtype');
        $bttype = $request->input('bttype');

        $antibiotics = $request->input('antibiotics');
        $intendedantibiotics = $request->input('intendedantibiotics');

        $tests = $request->input('test');
        $description = $request->input('description'); 
        $reason = $request->input('reason'); 


        foreach($tests as $key => $test_) {


                  $testprofiles = DB::table('testprofiles')->select('mandatory','name')->where('id',$test_)->get();
                  
                  if($testprofiles[0]->mandatory == 1 && $description[$key] == '') {

                     return response()->json(['error'=>'Description is mandatory for '.$testprofiles[0]->name]);

                  }
        }    



        $validator = Validator::make($request->all(), [
            'patient' => 'required',
            'clinician' => 'required',
            'clinicalDetail' => 'required',
            'clinic' => 'required_without:ward',
            'ward' => 'required_without:clinic',
            'fasting' => 'required',
            'outofhours' => 'required',
            'priority' => 'required',
            'datetime.*'  => 'required',
            'test.*'  => 'required'
        ]);

        



        if ($validator->passes()) {


           if(!empty($antibiotics)) {

                $Antibiotics = implode(',',$antibiotics);
           }  
           else {

                $Antibiotics = '';
           }


            if(!empty($intendedantibiotics)) {

                $IntendedAntibiotics = implode(',',$intendedantibiotics);
           }  
           else {

                $IntendedAntibiotics = '';
           }



           $patientinfo = DB::table('PatientIFs')
                                        ->select('DoB')
                                        ->where('id',$patient)
                                        ->get();

                 if(count($patientinfo) > 0) {
                 $DoB = $patientinfo[0]->DoB;                                         
                  $currentyear = Carbon::now()->year;
                  $birthyear = Carbon::createFromFormat('Y-m-d', $DoB)->year;

                  $years = $currentyear-$birthyear;
                 
                 if($years > 16) {

                    $category = 'Adult';

                 } else {

                    $category = 'Child';
                 } 
                 } else {

                    $category = '';
                                   
                 }


            $RequestEpisodeID = 0;

             foreach($datetimes as $key => $datetime)
            {

             
                 $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
                                                    ->where('session',$session)
                                                    ->where('answer',null)
                                                    ->select('answer')->get();

                  if(count($OCMRequestQuestionsDetails) > 0 ) {

                     return response()->json(['error'=> 'Please answer all questions.']);

                  }                                  


             $ReqestID = DB::table('OCMRequest')->max('ReqestID')+1;
            if($ReqestID == 1) { 

                $ReqestID = DB::table('Options')->where('Description','MinRequestID')->select('Contents')->get(); 
                $ReqestID = $ReqestID[0]->Contents;

             }

            // return $tests;

            if (in_array('103', $tests) && $bttype == 'bgnotfound') { 


                   if(!in_array('104', $tests)) {

                        array_push($tests,"104");
                   } 
                   


             }


            $id = DB::table('OCMRequest')->max('id')+1;

            //$RequestEpisodeID += 1;
            $RequestEpisodeID = DB::table('OCMRequest')->where('RequestPatientID',$patient)->max('RequestEpisodeID')+1;

            DB::insert('insert into OCMRequest 
                (id, Antibiotics, IntendedAntibiotics, ReqestID, RequestEpisodeID, RequestPatientID, RequestClinicianID, clinic, RequestWardID, bed, RequestClinicalDetail, RequestFasting, outofhours, RequestNotes, RequestPriority, ExecutionDateTime, RequestCreatedBy, RequestCreatedDateTime, RequestState, RequestType) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $Antibiotics, $IntendedAntibiotics, $ReqestID, $RequestEpisodeID, $patient, $clinician, $clinic, $ward, $bed, $clinicalDetail, $fasting, $outofhours, $notes, $priority, $datetime, $user->id, date('Y-m-d H:i:s'), 'Requested', $rtype]);




            foreach($tests as $key => $test)
            {



          if($bttype == 'bgnotfound') {



        if($test == 104) {


            $ReqestID2 = $ReqestID+1;                
            $start = date('Y-m-d H:i');
            $datetime_ = date('Y-m-d H:i',strtotime('+10 minutes',strtotime($start)));

            DB::insert('insert into OCMRequest 
            (id, Antibiotics, IntendedAntibiotics, ReqestID, RequestEpisodeID, RequestPatientID, RequestClinicianID, clinic, RequestWardID, bed, RequestClinicalDetail, RequestFasting, outofhours, RequestNotes, RequestPriority, ExecutionDateTime, RequestCreatedBy, RequestCreatedDateTime, RequestState, RequestType) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id+1, $Antibiotics, $IntendedAntibiotics, $ReqestID2, $RequestEpisodeID, $patient, $clinician, $clinic, $ward, $bed, $clinicalDetail, $fasting, $outofhours, $notes, $priority, $datetime, $user->id, $datetime, 'Requested', $rtype]);




                }
             

                   

              }
                            
                          
           

            $patientInfo = DB::table('PatientIFs')->select('PatName')->where('id',$patient)->get(); 
            $profileInfo = DB::table('testprofiles')->select('name')->where('id',$test)->get();   
            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [$ReqestID,$RequestEpisodeID,0,0,$patient,'Request', 'Profile '.$profileInfo[0]->name.' added with this Reason "'.$reason[$key].'". Request # '.$ReqestID.'/'.$RequestEpisodeID.' for the Patient : '.$patientInfo[0]->PatName.'.']);     

            $iid = DB::table('OCMRequestDetails')->max('id')+1;
            DB::insert('insert into OCMRequestDetails (id, RequestID, RequestEpisodeID, TestID, TestDescription, reason,  ExecutionDateTime, PatientID) values (?, ?, ?, ?, ?, ?, ?, ?)', 
            [$iid, $ReqestID, $RequestEpisodeID, $test, $description[$key], $reason[$key], $datetime, $patient]);



             $TestDefinitionIDs = DB::table('ProfileTestMapping')
                                        ->where('ProfileID',$test)
                                        ->select('TestDefinitionID')
                                        ->groupBy('TestDefinitionID')
                                        ->get(); 
              

                                         
            foreach($TestDefinitionIDs as $TestDefinitionID) {

                 $TestDefinitionID = $TestDefinitionID->TestDefinitionID;

                  $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                            'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID)
                                        ->get();

                  $info = DB::table('testprofiles')
                                        ->select('department','specialhandling')
                                        ->where('id',$test)
                                        ->get();                                          

                 $units = $TestDefinitions[0]->units;
                 $SampleType = $TestDefinitions[0]->SampleType;
                 $Hospital = $TestDefinitions[0]->Hospital; 
                 $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                 $childrenContainer = $TestDefinitions[0]->childrenContainer; 

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();                      

                 $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID)->get();

                 if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient, created_by) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient , $user->id]);

                            }


                    }





                }


                if($bttype == 'bgnotfound') {

                      DB::update("update ocmrequest  set  
                                ExecutionDateTime = '".$datetime_."'
                                 where ReqestID = '".$ReqestID2."'"); 


                             DB::update("update ocmrequesttestsdetails  set  
                                request = '".$ReqestID2."',
                                episode = '".$RequestEpisodeID."'
                                 where request = '".$ReqestID."' and  profileID = '104' "); 

                                 DB::update("update ocmrequestdetails  set  
                                RequestID = '".$ReqestID2."'
                  
                                 where RequestID = '".$ReqestID."' and  TestID = '104' "); 


                                  DB::update("update ocmrequesttestsdetails  set  
                                request = '".$ReqestID2."',
                                episode = '".$RequestEpisodeID."'
                                 where request = '".$ReqestID."' and  profileID = '105' "); 

                                 DB::update("update ocmrequestdetails  set  
                                RequestID = '".$ReqestID2."'
                                 where RequestID = '".$ReqestID."' and  TestID = '105' "); 


                                 DB::update("update ocmrequesttestsdetails  set  
                                request = '".$ReqestID2."',
                                episode = '".$RequestEpisodeID."'
                                 where request = '".$ReqestID."' and  profileID = '106' "); 

                                 DB::update("update ocmrequestdetails  set  
                                RequestID = '".$ReqestID2."'
                                 where RequestID = '".$ReqestID."' and  TestID = '106' "); 
                }




        foreach($tests as $key => $test)
            {

             foreach($TestDefinitionIDs as $TestDefinitionID) { 


                $TestDefinitionID = $TestDefinitionID->TestDefinitionID;        

                  $RTIDs = DB::table('ReflexTestMapping')->select('TestDefinitionID2')->where('TestDefinitionID1',$TestDefinitionID)->get();


                 if(count($RTIDs) > 0) { 

                    foreach($RTIDs as $RTID) {

                         $TestDefinitionID2 = $RTID->TestDefinitionID2;
                            
                          $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                         'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID2)
                                        ->get();

                           $info = DB::table('testprofiles')
                                                ->select('department','specialhandling')
                                                ->where('id',$test)
                                                ->get();                                          

                         $units = $TestDefinitions[0]->units;
                         $SampleType = $TestDefinitions[0]->SampleType;
                         $Hospital = $TestDefinitions[0]->Hospital; 
                         $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                         $childrenContainer = $TestDefinitions[0]->childrenContainer; 
                         

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();  


                         $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID2)->get();

                         if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient, created_by) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID2, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient , $user->id]);

                    }


                    }

                 }

                 }

             }




              


                         if(count($datetimes) > 1) {

                            if($datetimes[0] != $datetime) {

                                 
                                $i = 0; 
                                $pin = ""; 
                                while($i < 13){
                                $pin .= mt_rand(0, 9);
                                $i++;
                                }   

                                $OCMRequestQuestionsDetails = OCMRequestQuestionsDetails::where('session',$session)->get();

                                foreach($OCMRequestQuestionsDetails as $key => $value)
                                {

                                

                                $ocmRQDID = DB::table('OCMRequestQuestionsDetails')->max('id')+1;    
                                $OCMRequestQuestionsDetail = OCMRequestQuestionsDetails::find($value->id);    
                                $new_data = $OCMRequestQuestionsDetail->replicate();
                                $new_data->request = $ReqestID;
                                $new_data->episode = $RequestEpisodeID; 
                                $new_data->session = $pin;  
                                $new_data->id = $ocmRQDID;
                                $new_data->save();

                                }

                            } else {

                            DB::update("
                            update OCMRequestQuestionsDetails 
                            set 
                            request = '$ReqestID',
                            episode = '$RequestEpisodeID'

                            where session = $session
                            ");
                            }
  
                         } else {



                            DB::update("
                            update OCMRequestQuestionsDetails 
                            set 
                            request = '$ReqestID',
                            episode = '$RequestEpisodeID'

                            where session = $session
                            ");
                            

                         }


             }

            $patientInfo = DB::table('PatientIFs')->select('PatName')->where('id',$patient)->get();   
            $controller = App::make('\App\Http\Controllers\activitylogs');
           $data = $controller->callAction('addLogs', [$ReqestID,$RequestEpisodeID,0,0,$patient,'Request','New Request # '.$ReqestID.'/'.$RequestEpisodeID.' for the Patient : '.$patientInfo[0]->PatName.' has been added.']); 

    
                 return response()->json(['success'=>'Request Added.', 'url'=> 'Requested' ]);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     } 






     public function updateRequest(Request $request)
    {
        
        $user = auth()->user();
        $id = $request->input('id');
        $patient = $request->input('patient');
        $clinician = $request->input('clinician');
        $session = $request->input('session');
        $clinic = $request->input('clinic');
        $clinicalDetail = $request->input('clinicalDetail');
        $ward = $request->input('ward');
        $bed = $request->input('bed');
        $datetimes = $request->input('datetime');
        $fasting = $request->input('fasting');
        $priority = $request->input('priority');
        $notes = $request->input('notes');
        $rtype = $request->input('rtype');

        $antibiotics = $request->input('antibiotics');
        $intendedantibiotics = $request->input('intendedantibiotics');
        

        $tests = $request->input('test');
        $description = $request->input('description');

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'patient' => 'required',
            'clinician' => 'required',
            'clinicalDetail' => 'required',
            'clinic' => 'required_without:ward',
            'ward' => 'required_without:clinic',
            'bed' => 'required',
            'fasting' => 'required',
            'priority' => 'required',
            'datetime.*'  => 'required',
            'test.*'  => 'required'
        ]);

    
        if ($validator->passes()) {
                


           if(!empty($antibiotics)) {

                $Antibiotics = implode(',',$antibiotics);
           }  
           else {

                $Antibiotics = '';
           }


            if(!empty($intendedantibiotics)) {

                $IntendedAntibiotics = implode(',',$intendedantibiotics);
           }  
           else {

                $IntendedAntibiotics = '';
           }


                 $patientinfo = DB::table('PatientIFs')
                                        ->select('DoB')
                                        ->where('id',$patient)
                                        ->get();

                 if(count($patientinfo) > 0) {
                 $DoB = $patientinfo[0]->DoB;                                         
                  $currentyear = Carbon::now()->year;
                  $birthyear = Carbon::createFromFormat('Y-m-d', $DoB)->year;

                  $years = $currentyear-$birthyear;
                 
                 if($years > 16) {

                    $category = 'Adult';

                 } else {

                    $category = 'Child';
                 } 
                 } else {

                    $category = '';
                                   
                 }


                 $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
                                                    ->where('session',$session)
                                                    ->where('answer',null)
                                                    ->select('answer')->get();

                  if(count($OCMRequestQuestionsDetails) > 0 ) {

                     return response()->json(['error'=> 'Please answer all questions.']);

                  }  



             foreach($datetimes as $key => $datetime)
            {

            DB::update("
                update OCMRequest 
                set 
                RequestPatientID = '$patient',
                Antibiotics = '$Antibiotics',
                IntendedAntibiotics = '$IntendedAntibiotics',
                RequestClinicianID = '$clinician',
                clinic = '$clinic',
                RequestWardID = '$ward',
                bed = '$bed',
                RequestClinicalDetail = '$clinicalDetail',
                RequestFasting = '$fasting',
                RequestNotes = '$notes',
                RequestPriority = '$priority',
                ExecutionDateTime = '$datetime',
                RequestModifiedBy = '".$user->id."',
                RequestModifiedDateTime = '".date('Y-m-d H:i:s')."',
                RequestType = '$rtype'

                where id = $id
                ");

            }
    $data = DB::table('OCMRequest')->select('ReqestID','RequestEpisodeID')->where('id',$id)->get();
    $ReqestID = $data[0]->ReqestID;
    $RequestEpisodeID = $data[0]->RequestEpisodeID;

    DB::table('OCMRequestTestsDetails')->where('request', $ReqestID)->where('episode', $RequestEpisodeID)->delete(); 
    DB::table('OCMRequestDetails')->where('RequestID', $ReqestID)->where('RequestEpisodeID', $RequestEpisodeID)->delete(); 
    DB::table('OCMPhlebotomy')->where('PhlebotomyRequestID', $ReqestID)->where('PhlebotomyRequestEpisodeID', $RequestEpisodeID)->delete(); 


foreach($tests as $key => $test)
            {

            $iid = DB::table('OCMRequestDetails')->max('id')+1;
            DB::insert('insert into OCMRequestDetails (id, RequestID, RequestEpisodeID, TestID, TestDescription, ExecutionDateTime, PatientID) values (?, ?, ?, ?, ?, ?, ?)', 
            [$iid, $ReqestID, $RequestEpisodeID, $test, $description[$key], $datetime, $patient]);



             $TestDefinitionIDs = DB::table('ProfileTestMapping')
                                        ->where('ProfileID',$test)
                                        ->select('TestDefinitionID')
                                        ->groupBy('TestDefinitionID')
                                        ->get(); 
              

                                         
            foreach($TestDefinitionIDs as $TestDefinitionID) {

                 $TestDefinitionID = $TestDefinitionID->TestDefinitionID;

                  $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                            'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID)
                                        ->get();

                  $info = DB::table('testprofiles')
                                        ->select('department','specialhandling')
                                        ->where('id',$test)
                                        ->get();                                          

                 $units = $TestDefinitions[0]->units;
                 $SampleType = $TestDefinitions[0]->SampleType;
                 $Hospital = $TestDefinitions[0]->Hospital; 
                 $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                 $childrenContainer = $TestDefinitions[0]->childrenContainer; 

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();                      

                 $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID)->get();

                 if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient ]);

                    }


            }

                }






            foreach($tests as $key => $test)
            {

             foreach($TestDefinitionIDs as $TestDefinitionID) { 


                $TestDefinitionID = $TestDefinitionID->TestDefinitionID;        

                  $RTIDs = DB::table('ReflexTestMapping')->select('TestDefinitionID2')->where('TestDefinitionID1',$TestDefinitionID)->get();


                 if(count($RTIDs) > 0) { 

                    foreach($RTIDs as $RTID) {

                         $TestDefinitionID2 = $RTID->TestDefinitionID2;
                            
                          $TestDefinitions = DB::table('TestDefinitions')
                                        ->select(
                                         'TestDefinitions.SampleType',
                                            'TestDefinitions.units',
                                            'TestDefinitions.Hospital',
                                            'TestDefinitions.adultsContainer',
                                            'TestDefinitions.childrenContainer'
                                            )
                                        ->where('TestDefinitions.id',$TestDefinitionID2)
                                        ->get();

                           $info = DB::table('testprofiles')
                                                ->select('department','specialhandling')
                                                ->where('id',$test)
                                                ->get();                                          

                         $units = $TestDefinitions[0]->units;
                         $SampleType = $TestDefinitions[0]->SampleType;
                         $Hospital = $TestDefinitions[0]->Hospital; 
                         $adultsContainer = $TestDefinitions[0]->adultsContainer; 
                         $childrenContainer = $TestDefinitions[0]->childrenContainer; 
                         

                 if($category == 'Adult') {

                    $containerID = $TestDefinitions[0]->adultsContainer; 
                 } 
                 else {

                     $containerID = $TestDefinitions[0]->childrenContainer;    
                 }
                      
                  $max_vol = DB::table('containers')->select('max_vol')->where('id',$containerID)->get();  


                         $TDID = DB::table('OCMRequestTestsDetails')->where('request',$ReqestID)->where('episode',$RequestEpisodeID)->where('test',$TestDefinitionID2)->get();

                         if(count($TDID) == 0) {

                    $TDID = DB::table('OCMRequestTestsDetails')->max('id')+1;
                    DB::insert('insert into OCMRequestTestsDetails 
                                            (id, profileID, request, episode, test, sampletype, containerID, capacity, units, hospital, department, specialhandling, patient) 
                                            values 
                                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                                            [$TDID, $test, $ReqestID, $RequestEpisodeID, $TestDefinitionID2, $SampleType, $containerID, $max_vol[0]->max_vol, $units, $Hospital, $info[0]->department, $info[0]->specialhandling, $patient ]);

                    }


                    }

                 }

                 }

             }


                    DB::update("
                    update OCMRequestQuestionsDetails 
                    set 
                    request = '$ReqestID',
                    episode = '$RequestEpisodeID'

                    where session = $session
                    ");
                


                $status = 'Requested';


                 $patientInfo = DB::table('PatientIFs')->select('PatName')->where('id',$patient)->get();   
                $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$ReqestID,$RequestEpisodeID,0,0,$patient,'Request','Request # '.$ReqestID.'/'.$RequestEpisodeID.' for the Patient : '.$patientInfo[0]->PatName.' has been updated.']); 


                 return response()->json(['success'=>'Request Updated.', 'url'=> $status ]);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     } 










      public function addOnRequest(Request $request)
    {
        
        $user = auth()->user();
        $id = $request->input('id');
        $session = $request->input('session');
        $notes = $request->input('notes');
        $sampleid = $request->input('sampleid');
        $addon = $request->input('addon');

        $tests = $request->input('test');
        $description = $request->input('description');

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'test.*'  => 'required'
        ]);

        
        foreach($addon as $key => $addo) {

                    if($addo == 'Yes') {

                        $sampleids[] = $sampleid[$key];

                    }
        }




        if ($validator->passes()) {
                

                 $OCMRequestQuestionsDetails = DB::table('OCMRequestQuestionsDetails')
                                                    ->where('session',$session)
                                                    ->where('answer',null)
                                                    ->select('answer')->get();

                  if(count($OCMRequestQuestionsDetails) > 0 ) {

                     return response()->json(['error'=> 'Please answer all questions.']);

                  }  



 
    $data = DB::table('OCMRequest')->select('ReqestID','RequestEpisodeID','ExecutionDateTime','RequestPatientID')->where('id',$id)->get();
            $ReqestID = $data[0]->ReqestID;
            $RequestEpisodeID = $data[0]->RequestEpisodeID;
            $ExecutionDateTime = $data[0]->ExecutionDateTime;
            $RequestPatientID = $data[0]->RequestPatientID;

        
        

            foreach($tests as $key => $test)
            {


                $OCMRequestDetailsData = DB::table('OCMRequestDetails')
                                                        ->select('TestID')
                                                        ->where('TestID',$test)
                                                        ->where('RequestID',$ReqestID)
                                                        ->where('RequestEpisodeID',$RequestEpisodeID)
                                                        ->get();     


                 if(count($OCMRequestDetailsData) == 0 ) {


           $iid = DB::table('OCMRequestDetails')->max('id')+1;
            DB::insert('insert into OCMRequestDetails (id, RequestID, RequestEpisodeID, TestID, TestDescription, ExecutionDateTime, PatientID) values (?, ?, ?, ?, ?, ?, ?)', 
            [$iid, $ReqestID, $RequestEpisodeID, $test, $description[$key], $ExecutionDateTime, $RequestPatientID]);

                

                 }

            }   



          
foreach($sampleids as $sampleid) {



                  $OCMRequestTestsDetails = DB::table('OCMRequestTestsDetails')->where('sampleid',$sampleid)->limit(1)->get();
                 // print_r($OCMRequestTestsDetails);   

              foreach($tests as $test) { 

                    
           $ProfileTestMappings = DB::table('ProfileTestMapping')->select('TestDefinitionID')->where('ProfileID',$test)->get();


              foreach($ProfileTestMappings as $ProfileTestMapping) { 



            $TestDefinitions = DB::table('TestDefinitions')
                                ->select('shortname')
                                ->where('id',$ProfileTestMapping->TestDefinitionID)
                                ->where('SampleType',$OCMRequestTestsDetails[0]->sampletype)
                                ->where('Hospital',$OCMRequestTestsDetails[0]->hospital)
                                ->get();

            $department = DB::table('testprofiles')
                                ->select('department')
                                ->where('id',$test)
                                ->get();                                


                $ocmRID = DB::table('results')->max('id')+1;    
                $post = Results::find($sampleid);     
                $newPost = $post->replicate();
                $newPost->RunTime = null;
                $newPost->Comment = null;
                $newPost->id = $ocmRID;
                $newPost->sampleid = $sampleid;
                $newPost->resulted = 0;
                $newPost->result = '';
                $newPost->Flags = '';
                $newPost->Units = '';
                $newPost->NormalHigh = '';
                $newPost->NormalLow = '';
                $newPost->Code = $TestDefinitions[0]->shortname;
                $newPost->save();  


                $ocmRID = DB::table('ocmrequesttestsdetails')->max('id')+1;    
                $post = OCMRequestTestDetails::find($sampleid);     
                $newPost = $post->replicate();
                $newPost->profileID = $test;
                $newPost->test = $ProfileTestMapping->TestDefinitionID;
                $newPost->id = $ocmRID;
                $newPost->sampleid = $sampleid;
                $newPost->save();                           

                        
                    


 $connectionInfo_hq = array("Database"=>"CavanTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
            $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);
            
            if( $conn_hq ) {

              
                $tsql = "SELECT max(ID) as serverID FROM ocmRequestDetails";
                 $getProducts = sqlsrv_query($conn_hq, $tsql);
                 $serverID = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
                 if($serverID['serverID'] == null) {

                        $serverID = 1;

                 } else {

                        $serverID = $serverID['serverID']+1;
                 }



                     $sampletype = DB::table('Lists')->select('Code')->where('id', $OCMRequestTestsDetails[0]->sampletype)->get();
                    $sampletype = $sampletype[0]->Code; 

                    $testprofiles = DB::table('testprofiles')->select('name')->where('id', $test)->get();
                    $profile = $testprofiles[0]->name;  



                    $department1 = DB::table('Lists')->select('Code')->where('id', $department[0]->department)->get();
                    $department = $department1[0]->Code; 

            $OCMPhlebotomy = DB::table('OCMPhlebotomy')->select('PhlebotomySampleDateTime')->where('PhlebotomySampleID', $sampleid)->get();
                    $PhlebotomySampleDateTime = $OCMPhlebotomy[0]->PhlebotomySampleDateTime; 


                  $tsql = "INSERT INTO ocmRequestDetails (ID, RequestID, SampleID, TestCode, SampleType, DepartmentID, ProfileID, Programmed,addon,SampleDate) 
                             VALUES 
                            (
                                $serverID, 
                                '".$ReqestID."', 
                                '".$sampleid."', 
                                '".$TestDefinitions[0]->shortname."',
                                '".$sampletype."',
                                '".$department."',
                                '".$profile."',
                                0,
                                1,
                                '".$PhlebotomySampleDateTime."'
                            )";
                            $insertReview = sqlsrv_query($conn_hq, $tsql);



                        }



                                                

              }   
                                                

              }       


           }


                    DB::update("
                    update OCMRequest 
                    set 
                    RequestState = 'In Progress'

                    where ReqestID = $ReqestID
                    ");


                     DB::update("
                    update OCMRequestQuestionsDetails 
                    set 
                    request = '$ReqestID',
                    episode = '$RequestEpisodeID'

                    where session = $session
                    ");
                


                $status = 'Requested';
                $status = 'All';


                $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$ReqestID,$RequestEpisodeID,0,0,0,'Request','Profile has been added to the Request # '.$ReqestID.'/'.$RequestEpisodeID.'.']);   

                 return response()->json(['success'=>'Request Updated.', 'url'=> $status ]);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     } 






      public function getClinicList(Request $request)
    {   
        $id = $request->input('id');
        $Clinics = DB::table('Clinics') 
                        ->where('patient', $id )
                        ->orderBy('name','desc')
                        ->get();
        
        $ClinicsList = [];

        foreach ($Clinics as $Clinic) {
            $ClinicsList[] = ['id' => $Clinic->id, 'text' => $Clinic->name];
        }

        return \Response::json($ClinicsList);
    }






       public function deleteRequest(Request $request)
    {   
          

        $ReqestID = $request->input('ReqestID');  
        $RequestEpisodeID = $request->input('RequestEpisodeID'); 
        $reason = $request->input('reason');  
         
          $RequestPatientID = DB::table('OCMRequest')
                          ->select('OCMRequest.RequestPatientID')  
                          ->where('ReqestID', $ReqestID)
                          ->where('RequestEpisodeID', $RequestEpisodeID)
                          ->get();
            $RequestPatientID = $RequestPatientID[0]->RequestPatientID; 


            $patientInfo = DB::table('PatientIFs')->select('PatName')->where('id',$RequestPatientID)->get();   
            $controller = App::make('\App\Http\Controllers\activitylogs');
            $data = $controller->callAction('addLogs', [$ReqestID,$RequestEpisodeID,0,0,$RequestPatientID,'Request','Request # '.$ReqestID.'/'.$RequestEpisodeID.' for the Patient : '.$patientInfo[0]->PatName.' has been cancelled.']); 

        return  DB::update("update OCMRequest set RequestState =  'Cancelled'  where ReqestID = '".$ReqestID."' and RequestEpisodeID = '".$RequestEpisodeID."' ");
               

    } 



     public function PrintExternalLab(Request $request)
    {   
          

        $ReqestID = $request->input('ReqestID'); ;  
        $RequestEpisodeID = $request->input('RequestEpisodeID'); 
        $notes = $request->input('notes');  
        $gp = $request->input('gp');  

        $PatientID = DB::table('OCMRequest')->select('RequestPatientID') 
                        ->where('ReqestID', $ReqestID )
                        ->where('RequestEpisodeID', $RequestEpisodeID )
                        ->get();
        

         $PatientID = $PatientID[0]->RequestPatientID;
         $PatientInfo = DB::table('PatientIFs')
                        ->where('id', $PatientID )
                        ->get();

        if($PatientInfo[0]->PatPhone != '') {


        // $controller = App::make('\App\Http\Controllers\Controller');
        // $data = $controller->callAction('sendSMS', [$PatientInfo[0]->PatPhone,'Dear '.$PatientInfo[0]->PatName.' This is a test message.']); 

        }                



       return  DB::update("update OCMRequest set gp =  '$gp', gpnotes =  '$notes'  where ReqestID = '".$ReqestID."' and RequestEpisodeID = '".$RequestEpisodeID."' ");
              

    } 



     public function assignUser(Request $request)
    {   
          
        $userinfo = auth()->user();
        $user = $request->input('user');  
        $subject1 = $request->input('subject'); 
        $message = $request->input('message'); 
        $requestid = $request->input('requestid'); 
        $episodeid = $request->input('episodeid'); 
        $sampleid = $request->input('sampleid'); 
        
       
       $from = DB::table('users')->select('name')->where('id',$userinfo->id)->get();
       $to = DB::table('users')->select('name')->where('id',$user)->get();
                        

       $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$requestid,$episodeid,0,0,0,'Request','Request # '.$request->rid.' '.$request->eid. ' has been assigned by '.$from[0]->name.' to '.$to[0]->name]);   

       return  DB::insert('insert into signoffsmessages 
                (userID, subject, message, request, episode, sampleid, datetime, assignedby) values (?, ?, ?, ?, ?, ?, ?, ?)', 
            [$user, $subject1, $message, $requestid, $episodeid, $sampleid, date('Y-m-d H:i:s'), $userinfo->id ]);
           

    }


    public function markAsRead(Request $request)
    {   
          
        $id = $request->input('id');  

         
         return  DB::update("update signoffsmessages set datetimeread =  '". date('Y-m-d H:i:s')."' where id = $id ");
          

    } 


     public function showMessages(Request $request)
    {   
          

        $userinfo = auth()->user();
        $userid = $userinfo->id;
        $requestid = $request->input('requestid'); 
        $episodeid = $request->input('episodeid'); 
       


          $signoffsmessages = DB::table('signoffsmessages') 
                        ->select('signoffsmessages.*',
                                    'A.name as assignedfrom',
                                    'B.name as assignedto')
                        
                                    ->leftjoin('users AS A', 'A.id', '=', 'signoffsmessages.assignedby')
                                    ->leftjoin('users AS B', 'B.id', '=', 'signoffsmessages.userID')

                        //->where('userID', $userinfo->id )
                        //->orwhere('assignedby', $userinfo->id )

                        ->where(function($query) use ($userid){
                         $query->where('userID', '=', $userid);
                         $query->orwhere('assignedby', '=', $userid);
                        })
                        ->where(function($query) use ($requestid,$episodeid){
                         $query->where('request', '=', $requestid);
                         $query->where('episode', '=', $episodeid);
                        })

                       // ->where('request', $requestid )
                       // ->where('episode', $episodeid )
                        
                        ->orderby('id','desc')
                        ->get();

            return \Response::json($signoffsmessages);                        


              
              

    } 




       public function showFinalReport(Request $request)
    {   
          

        $userinfo = auth()->user();
        $userid = $userinfo->id;
        $requestid = $request->input('id'); 

          $results = DB::table('results')->where('sampleid',$requestid)
                        
                        ->orderby('id','desc')
                        ->get();

            $observations = DB::table('observations')->where('sampleid',$requestid)
                        
                        ->orderby('id','desc')
                        ->get();


              $data = [

                    'results' => $results,
                     'observations' => $observations

                ];

            return \Response::json($data);                        



    } 



      public function authorizeUser(Request $request)
    {   
          

        $userinfo = auth()->user();
        $email = $userinfo->email;
        $password = $request->input('password'); 

           if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
            return 1;

              $data = [

                    'resultCount' => 1

                ];
          

        } else {

              $data = [

                    'resultCount' => 0

                ];

          

        }

         return \Response::json($data); 


                                     



    } 





      public function getPendingSampleIDs(Request $request)
    {   
          
        $rid = $request->input('rid');

        $PhlebotomySampleIDs = DB::table('OCMPhlebotomy')
                           ->where('PhlebotomyRequestID',$request->rid)
                           ->where('PhlebotomySampleCollected',null)
                           ->select('PhlebotomySampleID')->get(); 

         return \Response::json($PhlebotomySampleIDs); 



    } 


 public function checkBloodGroupInfo(Request $request)
    {   
          
        $Chart = $request->input('Chart');
      

        

   $connectionInfo_hq = array("Database"=>"CavanTransfusionTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
    $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);
        
            
            if( $conn_hq ) {


                     $sql = "SELECT top(1) * FROM patientdetails where patnum = '".$Chart."' and fgroup is not null order by SampleDate desc";

                     $getProducts = sqlsrv_query($conn_hq, $sql);
                    
                    $counter = 0;

                    while ($row = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC)) {

                            
                            $counter=$counter+1;
                            $fgroup[] = $row['fgroup'];


                    }
  
                    if($counter == 1) {

                                if(count(array_unique($fgroup)) == 1) {


                                        $fgroup = $fgroup[0];
                                        $success = 'yes';

                                } else {

                                        $fgroup = null;
                                        $success = 'no';

                                }

                    } else {

                                        $fgroup = null;
                                        $success = 'no';

                    }  

                    $data = [

                            $success = $success,
                            $fgroup = $fgroup

                        ]; 
                     
    
                    return \Response::json($data); 

                }   

                  $data = [

                            $success = $success,
                            $fgroup = $fgroup

                        ]; 
                     
    
                    return \Response::json($data);                      



    } 


    


    public function viewRequestLog(Request $request)
    {   
          

        $userinfo = auth()->user();
        $id = $userinfo->id;
        $rid = $request->input('rid');


       $from = DB::table('users')->select('name')->where('id',$userinfo->id)->get();
        $ocmrequest = DB::table('ocmrequest')->where('ReqestID',$rid)->get();

                        

       $controller = App::make('\App\Http\Controllers\activitylogs');
               $data = $controller->callAction('addLogs', [$ocmrequest[0]->ReqestID,$ocmrequest[0]->RequestEpisodeID,0,0,$ocmrequest[0]->RequestPatientID,'Request','Request # '.$request->rid. ' has been viewed by '.$from[0]->name.'.']);

         return \Response::json($data); 



    } 


        public function signAll(Request $request)
    {
          
        $userinfo = auth()->user();   
     

         DB::table('results')->whereIn('Code', $request->tests)->where('request',$request->id)
           ->update([
               'SignOffBy' => $userinfo->id,
               'SignOffDateTime' => date('Y-m-d H:i:s')
            ]);

                 $from = DB::table('users')->select('name')->where('id',$userinfo->id)->get();

           foreach($request->tests as $test) {


                    $ocmrequest = DB::table('results')->where('request',$request->id)->where('Code',$test)->get();


                    if(count($ocmrequest) > 0) {

                        $testdefinitions = DB::table('testdefinitions')->where('shortname',$ocmrequest[0]->Code)->get();

                         $controller = App::make('\App\Http\Controllers\activitylogs');
                        $data = $controller->callAction('addLogs', [$ocmrequest[0]->request,$ocmrequest[0]->episode,$ocmrequest[0]->Code,$ocmrequest[0]->sampleid,$ocmrequest[0]->patient,'Request','Results has been signed for the Sample # '.$ocmrequest[0]->sampleid.' and the Test '.$testdefinitions[0]->longname.' of Request # '.$request->id. ' by '.$from[0]->name.'.']);

                    }



           }

    } 


        public function submitProducts(Request $request)
    {
        

        $user = auth()->user();
        $rid = $request->input('rid');
        $eid = $request->input('eid');
        $sampleid = $request->input('sampleid');
        $products = $request->input('products');
        $date = $request->input('date');
        $qty = $request->input('qty');


        $questions = $request->input('question');
        $answer = $request->input('answer');
                
                
          $validator = Validator::make($request->all(), [

            'products.*'  => 'required',
            'date.*'  => 'required',
            'qty.*'  => 'required',
            'question.*'  => 'required',
            'answer.*'  => 'required'

            ]);


               if ($validator->passes()) { 

                $samplesInfo = DB::table('ocmphlebotomy')->where('PhlebotomySampleID',$sampleid)->get();
                $PhlebotomySampleDateTime = $samplesInfo[0]->PhlebotomySampleDateTime;

                     $OCMRequestTestsDetail = DB::table('OCMRequestTestsDetails')
                      ->where('request', $request->rid)
                      ->where('episode', $request->eid)
                      ->where('sampleID', $sampleid)
                      ->get();

                         $profileID = $OCMRequestTestsDetail[0]->profileID;
                         $test = $OCMRequestTestsDetail[0]->test;
                         $sampletype = $OCMRequestTestsDetail[0]->sampletype;
                         $department = $OCMRequestTestsDetail[0]->department;
                         $hospital = $OCMRequestTestsDetail[0]->hospital;
                         $sampleID = $OCMRequestTestsDetail[0]->sampleID;

                   $sampletype = DB::table('Lists')->select('Code')->where('id', $sampletype)->get();
                    $sampletype = $sampletype[0]->Code; 

                    $department = DB::table('Lists')->select('Code')->where('id', $department)->get();
                    $department = $department[0]->Code; 

                    $testprofiles = DB::table('testprofiles')->select('name')->where('id', $profileID)->get();
                    $profile = $testprofiles[0]->name;  





           $connectionInfo_hq = array("Database"=>"CavanTest", "Uid"=>"LabUser", "PWD"=>"DfySiywtgtw$1>)*",'ReturnDatesAsStrings'=> true);
            $conn_hq = sqlsrv_connect('CHLAB02', $connectionInfo_hq);
            
                    if( $conn_hq ) {

                     
                    if($questions) {  
                    foreach ($questions as $key => $question) {
                        

                    DB::insert('insert into btrequestquestions 
                        (rid, eid, question, answer, created_by, created_at) values (?, ?, ?, ?, ?, ?)', 
                        [$rid, $eid, $question, $answer[$key],  $user->id, date('Y-m-d H:i:s')]);


                                $tsql = "SELECT max(id) as qid FROM ocmQuestions";
                             $getProducts = sqlsrv_query($conn_hq, $tsql);
                             $qid = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
                             if($qid['qid'] == null) {

                                    $qid = 1;

                             } else {

                                    $qid = $qid['qid']+1;
                             }

                         $tsql = "INSERT INTO ocmQuestions (id, rid, eid, question, answer, date_time) 
                                 VALUES 
                                ($qid, $rid, $eid, 
                                     '".$question."' , 
                                     '".$answer[$key]."' , 
                                      '".date('Y-m-d H:i:s') ."' 
                                    
                                    )";
                            sqlsrv_query($conn_hq, $tsql);


                    }
                }



                    foreach($products as $key => $product) {

                    
                    $btaddons = DB::table('btaddons')->where('id',$product)->get();
                    $pname = $btaddons[0]->name;        

                    $uid = uniqid();
                     DB::insert('insert into btproducts 
                        (pid, qty, requiredat, sampleid, request, episode, created_at, created_by, status, uid) values (?, ?, ?, ?, ?, ?, ?, ?,?, ? )', 
                        [$product,  $qty[$key], $date[$key], $sampleid, $rid, $eid, date('Y-m-d H:i:s'), $user->id, 'p', $uid]);
                      

                    $tsql = "SELECT max(ID) as serverID FROM ocmRequestDetails";
                     $getProducts = sqlsrv_query($conn_hq, $tsql);
                     $serverID = sqlsrv_fetch_array($getProducts, SQLSRV_FETCH_ASSOC);
                     if($serverID['serverID'] == null) {

                            $serverID = 1;

                     } else {

                            $serverID = $serverID['serverID']+1;
                     }

                     $date2 = $date[$key];
                     $date2 = date("Y-m-d H:i:s", strtotime($date2));


                        $tsql = "INSERT INTO ocmRequestDetails (ID, RequestID, SampleID, SampleDate, TestCode, SampleType, DepartmentID, ProfileID, Programmed, TransA, units, daterequired,status,uid) 
                             VALUES 
                            ($serverID, $rid, $sampleid, '".$PhlebotomySampleDateTime."', '".$pname."',  '".$sampletype."', '".$department."', '".$profile."', 0, 1, ".$qty[$key].", '".$date2."','Pending', '".$uid."')";
                            $insertReview = sqlsrv_query($conn_hq, $tsql);


                    }


                        }


                    return response()->json(['success'=>'Data Saved.']);


               }
                  
            return response()->json(['error'=>$validator->errors()->first()]);
    }



}