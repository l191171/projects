
public function Scanpost(Request $request){
    $connection=array('Database'=>'Patients','ReturnDatesAsStrings'=>true);
    $conn_hq= sqlsrv_connect('DESKTOP-4CJQSNR\SQLEXPRESS',$connection);
if(!$conn_hq){
    return false;
}
    if($request->ajax()){
    // return $request;
    $sid=$request->s;
   $s= array_unique($sid);


    foreach($s as $s){
  
    $sql1 = "Select top 1 * from ocmRequestDetails  WHERE SampleID ='$s' and Programmed = 0";
    $results = sqlsrv_query( $conn_hq, $sql1); 
    

    while ($rows1 = sqlsrv_fetch_array($results, SQLSRV_FETCH_ASSOC)){
        $requestid = $rows1['RequestID'];
    //     $department=$rows1['DepartmentID'];
    //    $code=$rows1['TestCode'];
    //     $daterequired=$rows1['daterequired'];
    //     $sampletype=$rows1['SampleType'];
    //     $units=$rows1['units'];
        // $array[$i] = $rows1['SampleID'];
        
        

    } 
    $q="SELECT * from ocmRequestDetails where RequestID ='$requestid'";
    $r = sqlsrv_query( $conn_hq, $q); 
    
    while ($rows6 = sqlsrv_fetch_array($r, SQLSRV_FETCH_ASSOC)){

        $department=$rows6['DepartmentID'];
       $code=$rows6['TestCode'];
        $daterequired=$rows6['daterequired'];
        $sampletype=$rows6['SampleType'];
        $units=$rows6['units'];
        // $array[$i] = $rows1['SampleID'];
      
    if($department=='Bio'){
        $sql8="Insert into biorequests(SampleID,Code,Programmed,DateTime,SampleType) values('$s','$code',1,'$daterequired','$sampletype')";
        $results9 = sqlsrv_query( $conn_hq, $sql8); 
        // $sql8="Insert into biorequests(SampleID,Code,Programmed,DateTime,SampleType) values('$s','$code',1,'$daterequired','$sampletype')";
        // $results9 = sqlsrv_query( $conn_hq, $sql8); 
            }
            else if($department=='Haem'){
                $sql8="Insert into HaemRequests(SampleID,Programmed,DateTimeOfRecord) values('$s',1,'$daterequired')";
                $results9 = sqlsrv_query( $conn_hq, $sql8); 
                    }
                    else if($department=='Coag'){
                        $sql8="Insert into CoagRequests(SampleID,Code,Units,DateTime) values('$s','$code','$units','$daterequired')";
                        $results9 = sqlsrv_query( $conn_hq, $sql8); 
                            }
          
        

    }     $sql2 = "Select top 1 * from ocmRequest  WHERE RequestID ='$requestid'";
    $results2 = sqlsrv_query( $conn_hq, $sql2); 

    while ($rows2 = sqlsrv_fetch_array($results2, SQLSRV_FETCH_ASSOC)){
        $requestpatid = $rows2['Chart'];
        
        // $array[$i] = $rows1['SampleID'];
        
        

    } 
    // return $requestpatid;
    $sql3 = "Select * from patientifs  WHERE Chart='$requestpatid'";
    $results3 = sqlsrv_query( $conn_hq, $sql3); 
    $array3=[];
    $i=0;
    while ($rows3 = sqlsrv_fetch_array($results3, SQLSRV_FETCH_ASSOC)){
    //    $array3[$i] = $rows3;
    //     $i++;
        $Chart = $rows3['Chart'];
        $PatName=$rows3['PatName'];
        $Sex=$rows3['Sex'];
        $DoB=$rows3['DoB'];
        $Addr0=$rows3['Address0'];
        $Addr1=$rows3['Address1'];
        $Ward=$rows3['Ward'];
        // $i++;
        // $dateOfBirth = "15-06-1995";
        $birthdate = new DateTime($DoB);
        $today   = new DateTime('today');
        $age = $birthdate->diff($today);
        if($age->y<0){
        $age=$age->m.'M';
    }
    else{
        $age=$age->y.'Y';
    }
        // return $age;
        // return $DoB;
        

    } 
    // return $array3;
    $sql4 = "Insert into demographics(SampleID,Chart,PatName,Sex,DoB,Addr0,Addr1,Ward,Age)values('$s','$Chart','$PatName','$Sex','$DoB','$Addr0','$Addr1','$Ward','$age')";
    $results4 = sqlsrv_query( $conn_hq, $sql4); 
    }
    $sql = "UPDATE ocmRequestDetails  SET Programmed = 1 WHERE id IN (" . implode(',', $request->k) . ")";
    $results0 = sqlsrv_query( $conn_hq, $sql); 

}

}
public function ScanSample(Request $request){
    $connection=array('Database'=>'Patients','ReturnDatesAsStrings'=>true);
        $conn_hq= sqlsrv_connect('DESKTOP-4CJQSNR\SQLEXPRESS',$connection);
    if(!$conn_hq){
        return false;
    }
    // if($request->ajax()){

    //     $sid=$request->sid;
    //     return $sid;
    // }
   
    $arr=[];
    if($request->ajax()){

$sid=$request->sid;
  $sql1="select * from ocmRequestDetails where SampleID = '$sid' AND Programmed=0";
        $results0 = sqlsrv_query( $conn_hq, $sql1); 
        // return $results0; 
        $i=0;
        $array=[];
        while ($rows1 = sqlsrv_fetch_array($results0, SQLSRV_FETCH_ASSOC)){
            $array[$i] = $rows1;
            $i++;
            // $array[$i] = $rows1['SampleID'];
            
            

        } 
        // return $array;
        //  $arr=$array[0];  
        // return $arr;  
    // return $array;
    
        // return view('scan')->with('array',$array);
        // return view("scan", ["array"=>$array]);
        $returnHTML = view('ab')->with('array', $array)->render();
return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

   
    // return view('scan');
}

public function scan(){
    return view('scans');
}
