@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Stock Ordering
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
                
                  <div>
                        <table  id="table"  >
                            <thead>
                               <tr>
                                 <th>Product</th>
                                 <th>O Pos</th>
                                 <th>A Pos</th>
                                 <th>B Pos</th>
                                 <th>AB Pos</th>
                                 <th>O Neg</th>
                                 <th>A Neg</th>
                                 <th>B Neg</th>
                                 <th>AB Neg</th>
                               </tr>
                            </thead>
                        </table>
                  </div>

                  <div class="mt-4">
                                          
                                        <table  id="table1"  >
                                          <thead>
                                           <tr>
                                           <th>Product</th>
                                           <th>Suggest</th>
                                           <th>In Stock</th>
                                           <th>Min</th>
                                           <th name="action">Action</th>
                                           </tr>
                                          </thead>

                                        </table>
                  </div>
                  <div class="row mt-4 mx-2">
                    <div class="col-sm-6">
                    <div class="col-md-12">
                      <h4 >Display</h4>
                    </div>
                    <div class="col-md-12">
                     <input type="radio" name="display" class="form-check-input"><label class="form-check-label">In Stock</label>
                    </div>
                    <div class="col-md-12 mt-1">
                     <input type="radio" name="display" class="form-check-input"><label class="form-check-label">Minimum</label>
                    </div>
                    </div>
                    <div class="col-md-6">
                      <div class="col-md-12">
                        <h4 >View</h4>
                      </div>
                      <div class="col-md-12">
                        <input type="checkbox" name="view" class="form-check-input"><label class="form-check-label">In Free Stock</label>
                      </div>
                       <div class="col-md-12 mt-1">
                        <input type="checkbox" name="view1" class="form-check-input"><label class="form-check-label">Crossmatched</label>
                      </div>
                    </div>
                  </div>
                <div class="row mt-4">
                  <div class="col-md-6">
                    <label class="form-label">FAX Message</label>
                    <input type="text" name="fax" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">FAX Number</label>
                    <input type="number" name="faxno" class="form-control">
                  </div>
                </div>
                <div class="row mt-4 mx-1">
                  <a href="#" class="btn btn-primary">Cancel</a>
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
   {data: 'product', name: 'product'},
   {data: 'opos', name: 'opos'},
   {data: 'apos', name: 'apos'},
   {data: 'bpos', name: 'bpos'},
   {data: 'abpos', name: 'abpos'},

   {data: 'oneg', name: 'oneg'},

   {data: 'aneg', name: 'aneg'},

   {data: 'bneg', name: 'bneg'},

   {data: 'abneg', name: 'abneg'},

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

<script >
$(document).ready(function() {


   
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    
var table=$('#table1').DataTable({
 
// ajax: {
    
//     url: "{{ route('Clients') }}",
    
// },
 columns: [
   {data: 'product', name: 'product'},
   {data: 'suggest', name: 'suggest'},
   {data: 'instock', name: 'instock'},
   {data: 'min', name: 'min'},


    {data: 'action', name: 'action', orderable: false, searchable: false},
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