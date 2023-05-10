@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Stock Movement
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
         
           <div class="row">
             <div class="col-md-4">
               <label class="form-label">ISBT128</label>
               <input type="text" name="isbt" class="form-control">
             </div>
              <div class="col-md-4">
                <label class="form-label">Expiry</label>
                <input type="text" name="expiry" class="form-control">
             </div>
            <div class="col-md-4">
                <label class="form-label">Group</label>
                <input type="text" name="group" class="form-control">
            </div>
           </div>  
           <div class="row">
             <div class="col-md-4">
               <label class="form-label mt-2">EI Suitable</label>
               <input type="text" name="eisuitable" class="form-control">
             </div>
              <div class="col-md-4">
                <label class="form-label mt-2">Product</label>
                <input type="text" name="product" class="form-control">
             </div>

           </div> 
             <div class="mt-4">
                              <table  id="table"  class="table">
                                <thead>
                                 <tr>
                                   <th>Date Recorded</th>
                                   <th>Unit Status</th>
                                   <th>Transfusion Start Date/Time</th>
                                   <th>Dispatch Details</th>
                                   <th>Operator</th>
                                   <th>Patient ID</th>
                                   <th>Patient Name</th>
                                   <th>Date of Birth</th>
                                   <th>Ward</th>
                                   <th>Notes</th>
                                
                                 </tr>
                                </thead>
                              </table>
             </div>
         
                
              <div class="col-md-12 mt-4">
                <a href="#" class="btn btn-primary">Return to Supplier</a>
                <a href="#" class="btn btn-success">Return to Stock</a>
                <a href="#" class="btn btn-warning">Transfuse</a>
                <a href="#" class="btn btn-primary">Destroy</a>
                <a href="#" class="btn btn-success">Pack Dispatch</a>
                <a href="#" class="btn btn-warning">Remove From Lab Pending Transfusion</a>
              
              </div>
              <div class="col-md-12 mt-4">
                  <a href="#" class="btn btn-primary">Inter-Hospital Transfer</a>
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
<script >
$(document).ready(function() {


   
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    
var table=$('#table').DataTable({
 
// ajax: {
    
//     url: "{{ route('Clients') }}",
    
// },
 columns: [

    {data: 'daterecord', name: 'daterecord'},
    {data: 'unitstatus', name: 'unitstatus'},
    {data: 'transfusionstart', name: 'transfusionstart'},
    {data: 'dispatchdetails', name: 'dispatchdetails'},
    {data: 'operator', name: 'operator'},
    {data: 'patientid', name: 'patientid'},
    {data: 'patientname', name: 'patientname'},
    {data: 'dateofbirth', name: 'dateofbirth'},
    {data: 'ward', name: 'ward'},
    {data: 'notes', name: 'notes'},
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