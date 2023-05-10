@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Transfusion
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


            <form id="form">
            {{ csrf_field() }}
                                                 
                <div class="card card-primary card-outline">
                    <div class="card-body ">  
                    <div class="col-md-12">
                        <h1 class="text-danger text-center">Caution Test System</h1>
                        <h2 class="text-warning text-center">Blood Transfusion Request Products</h2> 
                        <div class="mt-3"> 
                          <table class="table my-3 table-striped">
                              <thead>
                                  <tr>   
                                      <th>Sample ID</th>
                                      <th>Patient Name</th>
                                      <th>Test Code</th>
                                      <th>Units</th>
                                      <th>Sample Date</th>
                                      <th>Date Required</th>
                                      <th>Status</th>
                                  </tr>
                              </thead>
                          </table>
                        </div> 
                        <div class="pending d-flex">
                            <h5 id="pending_h5">Pending Orders: <span id="pending_orders_num">0</span></h5>
                            <h5 class="ml-5" id="process_h5">Process Orders: <span id="process_orders_num">0</span></h5>
                        </div>

                        <h2 class="text-warning text-center">Blood Transfusion Requests</h2> 
                        <div class="mt-3"> 
                          <table class="my-3 table-striped tableSecond">
                              <thead>
                                  <tr>   
                                      <th>MRN</th>
                                      <th>Sample ID</th>
                                      <th>Test Code</th>
                                      <th>Profile ID</th>
                                      <th>Sample Date</th>
                                  </tr>
                              </thead>
                          </table>
                        </div>
                        <div class="pendingSecond d-flex">
                            <h5 id="pendingSecond_h5">Pending Orders: <span id="pendingSecond_orders_num">1</span></h5>
                            <h5 class="ml-5" id="processSecond_h5">Process Orders: <span id="processSecond_orders_num">1</span></h5>
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
<script >
$(document).ready(function() {


   
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

   
var table=$('.table').DataTable({
 
// ajax: {
   
//     url: "{{ route('Clients') }}",
   
// },
 columns: [

  {data: 'sampleid', name: 'sampleid'},
{data: 'patientname', name: 'patientname'},
{data: 'testcode', name: 'testcode'},
 {data: 'units', name: 'units'},
 {data: 'sampledate', name: 'sampledate'},
 {data: 'daterequired', name: 'daterequired'},
   {data: 'status', name: 'status'},

    // {data: 'action', name: 'action', orderable: false, searchable: false},
],
"order":[[0, 'desc']],

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
var table=$('.tableSecond').DataTable({
 
// ajax: {
   
//     url: "{{ route('Clients') }}",
   
// },
 columns: [


  {data: 'mrn', name: 'mrn'},
       {data: 'sampleid', name: 'sampleid'},
      {data: 'testcode', name: 'testcode'},
       {data: 'profileid', name: 'profileid'},
       {data: 'sampledate', name: 'sampledate'},

    // {data: 'action', name: 'action', orderable: false, searchable: false},
],
"order":[[0, 'desc']],

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
  text: "Once deleted, you will not be able to recover this imaginary file!",
  icon: "warning",
  buttons: true,
  dangerMode: true,

}).then((willDelete) => {
  if (willDelete) {
                         $.ajax({
                        type: 'get',
                        url:"Cdelete/"+id,
                        //data: {'id':id},
                        dataType: '',                  
                       success: function(){
                           
                         table.ajax.reload(null, false);

                              }
                            });

   

  }
});

       
    });
     
   




   

});
</script>
@endpush