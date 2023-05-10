@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Clients
               <a class="btn btn-info btn-sm" href="{{route('Clients')}}"><i class="fas fa-arrow-left"></i> Go Back </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item active">Clients</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


     <!-- Main content -->
    <div class="content">
      <div class="container-fluid">


                  <form id="form" >
                                       {{ csrf_field() }}
                                                 
                         <div class="card card-primary card-outline">
                            <div class="card-body ">  
                 <div class="col-md-12">
                   
                 </div>          
         
             
                

<div class="row ">

<div class="col-md-6">
<label class="form-label " for="">Number</label>
<input class="form-control " type="text">
</div>

<div class="col-md-6">
<label class="form-label " for="">Search</label>
<div class="input-group">
<input class="form-control " type="search"> <a href="#" class="btn btn-primary">Search</a>
</div>




</div>


</div>



<div class="row mt-3">

<div class="col-md-6">
<label class="form-label " for="">Name</label>
<input class="form-control " type="text">

</div>
<div class="col-md-6">
<label class="form-label " for="">Lab Number</label>
<input class="form-control " type="text">

</div>
</div>


<div class="row mt-3">

<div class="col-md-6">
<label class="form-label " for="">Maiden</label>
<input class="form-control " type="text">
</div>

<div class="col-md-3">
<label for="">Sex</label>
<input class=" form-control " type="text">

</div>

<div class="col-md-3">
<label for="">D.O.B</label>
<input class=" form-control " type="text">

</div>


</div>




<div class="row mt-3">

<div class="col-md-6">
<label for="">Address</label>
<input class=" form-control " type="text">
</div>

<div class="col-md-3">
<label for="">Prev. Trans</label>
<input class=" form-control " type="text">

</div>

<div class="col-md-3">
<label for="">Reaction</label>
<input class=" form-control " type="text">

</div>

</div>



<div class="row mt-3">
    <div class="col-md-6">
        <label for="" class="form-label"> </label>
    <input class=" form-control mt-2" type="text">
    </div>

    <div class="col-md-3">
<label for="">Prev. Preg</label>
<input class=" form-control " type="text">

</div>

<div class="col-md-3">
<label for="">EDD</label>
<input class=" form-control " type="text">

</div>



</div>


<div class="row mt-3">





</div>


<div class="row mt-3">

<div class="col-md-12">
<input class=" form-control " type="text" value="NEG">
</div>
</div>



<div class="row mt-3">

<div class="col-md-3">
<label for="">Group</label>
<input class=" form-control " type="text">

</div>

<div class="col-md-3">
<label for="">Kell</label>
<input class=" form-control " type="text">

</div>

<div class="col-md-6">
<label class="form-label " for="">Special Products</label>
<input class="form-control " type="text">
</div>

</div>



<div class="row mt-3">

<div class="col-md-12">
<label for="">Comment</label>
<textarea style="height: 100px;" name="" id="" cols="30" rows="10" class="form-control"></textarea>
</div>
</div>


<div class="row mt-3">
<div class="col-md-6">
<label class="form-label " for="">Ward</label>
<input class="form-control " type="text">
</div>


<div class="col-md-6">
<label class="form-label " for="">Clinician</label>
<input class="form-control " type="text">
</div>

</div>




<div class="row mt-3">
<div class="col-md-6">
<label class="form-label " for="">Clinical Conditions</label>
<input class="form-control " type="text">
</div>

<div class="col-md-6">
<label class="form-label " for="">Surgical Procedures</label>
<input class="form-control " type="text">
</div>
</div>





<div class="row mt-3 mb-4">

</div>


             
                 <!-- <table  class="table mb-0 table-striped table">
                                 
                                 <thead>
                                 
                                 <tr>
                                 
                                  <th>ID</th>
                                  <th>Ticket#</th>
                                  <th>Patient</th>
                                  <th>Request#</th>
                                  <th>Sample#</th>
                                  <th>Subject</th>
                                  <th>Created At</th>
                                  <th>Department</th>
                                  <th>Priority</th>
                                  <th>Actions</th>
                                  </tr>
                    
                    
                                     </thead>
                              
                                   </table> -->
             <div class="col-md-12 text-center">
             <button class="btn btn-primary "> History</button>
             <button class="btn btn-info ml-1"> Copy</button>
             <button class="btn btn-warning ml-1"> Print</button>
             <button class="btn btn-success ml-1"> Submit </button>
             <button class="btn btn-danger ml-1"> Cancel </button>  
             </div>
             <div style="display:flex; justify-content:space-between; flex-wrap:nowrap;">

             <div>
             <button class="btn" style="background-color:#138496; color:white;"> Previous</button>
             </div>



             <div>
             <button  class="btn " style="background-color:red; color:white;"> Next</button>
             </div>
             
             </div>




                   
           
                            </div>
                           </div>
                            <div id="result" class="text-danger"></div>

                  </form>        

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>



@extends('layouts.footer')

@push('script')

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

    
var table=$('.table').DataTable({
 
// ajax: {
    
//     url: "{{ route('Tickets') }}",
    
// },
 columns: [

    {data: 'id', name: 'id'},
    {data: 'ticketid', name: 'ticketid'},
   {data: 'patientname', name: 'patientname'},
   {data: 'requestid', name: 'requestid'},
    {data: 'sampleid', name: 'sampleid'},
    {data: 'subject', name: 'subject'},
    {data: 'created_at', name: 'created_at'},
      {data: 'department', name: 'department'},
        {data: 'priority', name: 'priority'},
    {data: 'action', name: 'action', orderable: false, searchable: false},
],
"order":[[6, 'desc']], 

  dom: "Blfrtip",
                buttons: [
                
                    {
                        title:'Users',
                        text: 'Excel',
                        footer: true,
                        extend: 'excelHtml5',
                        exportOptions: {
                        columns: [':visible :not(:last-child)']
                        },
                    },
                    {
                    title:'Users', 
                    text: 'PDF', 
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: [':visible :not(:last-child)']
                        },
                    footer: true,
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    customize: function (doc) {
                    doc.content[1].table.widths = 
                              Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                          doc.styles.tableBodyEven.alignment = 'center';
                          doc.styles.tableBodyOdd.alignment = 'center'; 
                                
                        }
                    },
                    {
                        text: 'Print',
                        title:'Users',
                        extend: 'print',
                        footer: true,
                        exportOptions: {
                        columns: [':visible :not(:last-child)']
                        },
                    }, 
                    'colvis'   
                ],

                columnDefs: [{
                    orderable: false,
                    targets: -1,
                },
                { "visible": false, "targets": [] }
                ], 

});


table.on('click', '.delete', function () { 
     
     var id=this.id;

             swal({
                title: "Are you sure?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
              }).then((willDelete) => {
                  if (willDelete) {
                 
                 $.ajax({
                  type: 'get',
                  url:"deleteTicket/"+id,
                  dataType: '',                  
                  success: function(){
                      
                     table.ajax.reload( null, false );

                        }
                      }); 

   

  } 
});

       
    });
     
    




   

});  
    </script>

@endpush