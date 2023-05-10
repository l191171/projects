@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Tickets
               <a class="btn btn-info btn-sm" href="{{(request()->segment(2))}}"><i class="fas fa-sync"></i></a>
               
               <a class="btn btn-info btn-sm" href="{{route('Ticket')}}"><i class="fas fa-plus"></i> Ticket </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">Tickets</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


     <!-- Main content -->
    <div class="content">
   
    
<div class="row">
    <div class="col-md-2">
      <label for="">From</label>
    <input class="form-control" type="date" name='todate' id="todate">
</div>

<div class="col-md-2">

<label for="">To</label>
    <input class="form-control" type="date" name="tilldate" id="tilldate" >
</div>
<div class="col-md-2">

    
<label for="">Status</label>
    <!-- <input  class="form-control" type="text" name="status" id="status"> -->
    <select class="form-control" name="status" id="status" >
                                                    <option value="">Choose a Status</option>
                                                      
                                                        <option value="Closed">Closed</option>
                                                        
                                                        <option value="Processing">Processing</option>
                                                        
                                                        <option value="Opened">Opened</option>
                                                        
                                                        <option value="Completed">Completed</option>
                                                     
                                                  </select>  
</div>
<div class="form-group col-md-2">
  <label for="">Assigned To</label>

                                                  <select class="form-control" name="assignedto" id="assignedto" >
                                                    <option value="">Assigned to </option>
                                                        @foreach ($data as $role)
                                                        <option  >{{$role->email}}</option>
                                                        @endforeach
                                                  </select>      
</div>
<div class="form-group col-md-2">
  <label for="">Assigned By</label>

                                                  <select class="form-control" name="assignedby" id="assignedby" >
                                                    <option value="">Assigned By </option>
                                                        @foreach ($data as $role)
                                                        <option  >{{$role->email}}</option>
                                                        @endforeach
                                                  </select>      
</div>
<div class="form-group col-md-2 mt-4">

<input type="button" id ="submit" value="Submit" class="btn btn-primary">
</div>
</div>    

 
      <div class="container-fluid">

      
                         <div class="card card-primary card-outline">
                            <div class="card-body table-responsive"> 
             <table id="table"  class="table mb-0 table-striped table">
                                 
             <thead>
             
             <tr>
             
             <th>ID</th>
              <th>Client</th>
              <th>Ticket#</th>
              <th>Subject</th>


              <th>Patient</th>
              <th>Request#</th>
              <th>Sample#</th>

              <th>Status</th>
              <th>Requested</th>
              <th>Assigned</th>
              <th>Assigned By</th>

        
              <th>Department</th>
              <th>Priority</th>
              <th>System</th>
              <th>Time</th>
              <th>Timetaken</th>
             
              


              
              </tr>


                 </thead>

                
               </table>
            




                 <!-- Modal -->
                <div class="modal fade" id="selectUsers" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md  modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header bg-primary">
                                <h5 class="modal-title text-white">Assign Ticket to a User <span id="requestText2"></span></h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                                      
                                 

                              <div class="col-md-12">
                                
                                    <input type="hidden" id="tid"> 


                                  </div>

                                <button type="button" class="mt-2 btn btn-primary assignTicketNowBtn float-right">Assign Now</button>
                                        

                            </div>     

                           
                           
                        </div>
                    </div>
                </div> 




        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  </div>
  <!-- /.content-wrapper -->
@extends('layouts.footer')

@push('script')
<!-- DataTables  & Plugins -->

<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
   <script>
   
$(document).ready(function() {
 
   
$.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
   


// let myform=document.getElementById("form");
// let data=new FormData(myform);
 function loadtable () {
  var todate = $("#todate").val();
  var  tilldate= $("#tilldate").val();
  var status = $("#status").val();
  var assignedto = $("#assignedto").val();
  var assignedby = $("#assignedby").val();


 var table = $('#table').DataTable({

"lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50,100, 200, 500, "All"] ],
// dom: 'lBfrtip', //"Bfrtip",
dom: 'lBfrtip',
        buttons: [
          
'copy','excel',
'csv',
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            
              },

              'print',

        ]
,
processing: true,
serverSide: true,
// stateSave: true,

ajax: {
   url: "{{ route('reporte') }}",
   method: 'POST',
   data : {
 todate: todate,
 tilldate:tilldate,
 status:status,
 assignedto:assignedto,
 assignedby:assignedby
}
},

columns: [
  {data: 'id', name: 'id'},
    {data: 'business', name: 'business'},
    {data: 'ticketid', name: 'ticketid'},
    {data: 'subject', name: 'subject'},
   {data: 'patientname', name: 'patientname'},
   {data: 'requestid', name: 'requestid'},
    {data: 'sampleid', name: 'sampleid'},
    {data: 'status', name: 'status'},
    {data: 'username', name: 'username'},
    {data: 'assignedto', name: 'assignedto'},
    {data: 'assignedby', name: 'assignedby'},
    // {data: 'resolved', name: 'resolved'},
      {data: 'department', name: 'department'},
        {data: 'priority', name: 'priority'},
        {data: 'internal', name: 'internal'},
        {data: 'created_at', name: 'created_at'},

        {data: 'timetaken', name: 'timetaken'},
],
"order":[[1, 'asc']],


  
});

 }
  

//  loadtable();s


    $("#submit").click(function(){ 
      var todate = $("#todate").val();
  var  tilldate= $("#tilldate").val();
      $('#table').DataTable().destroy();
      if(todate!="" && tilldate==""){
// $('#tilldate').addClass('d-none');
Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: 'Date is required',
                                icon: 'bx bx-info-circle'
                            });
                            return false;

}
else if(todate=="" && tilldate!=""){
  Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: 'Date is required',
                                icon: 'bx bx-info-circle'
                            });
                            return false;
}

      loadtable();


    })
 
//     $("#submit").click(function(){
      
//       // $('.paused').addClass('d-none');
      
//       // $('.paused').removeClass('d-block');
//       // $('.started').addClass('d-block');
   
// let myform=document.getElementById("form");
// let data=new FormData(myform);
// $.ajax({
                
//         url: "../reporte",
//         data: data,    
//         cache: false,
//         processData: false,
//         contentType: false,
//         type: 'POST',
//         }).done(function (response) {
//             console.log("done");
//                         // if(response > 0) {

//                         //     $("#result").html('Ticket has been completed successfully!')

//                         // //  window.location="../TicketView/"+response;

//                         // }
                   
                       
//                 });
        

//   });

 
   });


   
 
 



                  

    </script>
@endpush