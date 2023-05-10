@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Net Acquire
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
                                <div class="row">
                                    <div class="col-md-9">
                                        <table id="table">
                                            <thead>
                                                <tr>   
                                                    <th>Chart #</th>
                                                    <th>Date/Time</th>
                                                    <th>Sample ID</th>
                                                    <th>Op Code</th>
                                                    <th>Operator</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>          
                                    <div class="col-md-3">
                                        <div class="ml-3">
                                            <h5 class="text-center">Between Dates</h5>
                                            <input type="date" name="betweendate1" id="betweendate1" class="form-control mt-4">
                                            <input type="date" name="betweendate2" id="betweendate2" class="form-control mt-4">

                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked1">
                                                <label class="form-check-label" for="flexCheckChecked1">
                                                    Haem
                                                </label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked2">
                                                <label class="form-check-label" for="flexCheckChecked2">
                                                    Bio
                                                </label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked3">
                                                <label class="form-check-label" for="flexCheckChecked3">
                                                    Coag
                                                </label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked4">
                                                <label class="form-check-label" for="flexCheckChecked4">
                                                    Log In/Out
                                                </label>
                                            </div>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked5">
                                                <label class="form-check-label" for="flexCheckChecked5">
                                                    Printing
                                                </label>
                                            </div>
                                            
                                            <button class="btn btn-primary mt-3" id="refreshbtn">Refresh</button>
                                            <button class="btn btn-danger mt-3 ml-2" id="cancelbtn">Cancel</button>
                                        </div>
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

    {data: 'chartnum', name: 'chartnum'},
    {data: 'datetime', name: 'datetime'},
   {data: 'sampleid', name: 'sampleid'},
   {data: 'opcode', name: 'opcode'},
   {data: 'operator', name: 'operator'},
   {data: 'details', name: 'details'},
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