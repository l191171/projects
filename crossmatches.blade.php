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
                                
                            <div style="display:flex; flex-direction:column;">
 <label for="betweendates">Between Dates:</label>
 
 <div class="mb-3">
 <input type="date" >
  <input type="date" >
 <button  class="btn btn-primary">Search</button>
 </div>

 

 </div>


                 <div class="col-md-12">
                   
                 </div>          
         
             

             
                 <table  class="mb-0 table-striped " id="table">      
                           
             <thead>
             
             <tr>

             <th>Unit Number</th>
              <th>Expiry</th>
              <th>Product</th>
              <th>Group</th>
              <th>Pat ID</th>
              <th>A and E</th>
              <th>Name</th>
              <th>OP</th>
              <th>Date</th>
              <th>Sample Date</th>


              </tr>
                 </thead>
                 
          
               </table>
               
  



<button class="btn btn-success ml-5"> Submit </button>
<button class="btn btn-danger ml-1"> Cancel </button>   

                   
           
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

    
var table=$('#table').DataTable({
 
// ajax: {
    
//     url: "{{ route('Tickets') }}",
    
// },
 columns: [

    {data: 'Unit_Number', name: 'Unit_Number'},
    {data: 'Expiry', name: 'Expiry'},
   {data: 'Product', name: 'Product'},
   {data: 'Group', name: 'Group'},
    {data: 'Pat_ID', name: 'Pat_ID'},
    {data: 'A_and_E', name: 'A_and_E'},
    {data: 'Name', name: 'Name'},
      {data: 'OP', name: 'OP'},
        {data: 'Date', name: 'Date'},
    {data: 'Sample_Date', name: 'Sample_Date'}
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

$('.datepicker').datepicker({
  inline: true
});



    </script>





@endpush