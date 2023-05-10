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




<div class="row">

<div class="col-md-6">
<label class="form-label " for="">Search</label>

<div>
    
    <input class="ml-2" type="radio" id="download" name="search">
    <label class=" radio-inline " for="download">Download</label>

    <input class="ml-2" id="historic" type="radio" name="search">
    <label class="radio-inline " for="historic">Historic</label>
    
    <input class="ml-2"  id="both" type="radio" name="search">
    <label class="radio-inline" for="both">Both</label>
    

</div>


<div class="input-group mb-3">
<input class="form-control " type="search"> <a href="#" class="btn btn-primary">Search</a>
</div>

<div>
<label class="form-label" for="soundex">Records</label>
<input class="form-control" style="width:50%;" type="number" name="records" id="records" min="0" >
</div>

</div>


<div class="col-md-3">
<label class="form-label " for="">Search For</label>




<div >
    <input type="radio" name="name" id="name">
    <label class=" radio-inline " for="name">Name</label>
</div>

<div >
    <input type="radio" name="name" id="chart">
    <label class=" radio-inline " for="chart">Chart</label>
</div>

<div >
    <input type="radio" name="name" id="dob">
    <label class=" radio-inline " for="dob">D.O.B</label>
</div>

<div >
    <input type="radio" name="name" id="name_dob">
    <label class=" radio-inline" for="name_dob">Name+D.O.B</label>
</div>

<div>
<label class="form-label" for="soundex">Use Soundex</label>
<input type="checkbox" name="soundex" id="soundex" >
</div>


</div>



<div class="col-md-3 mb-3">
<label class="form-label " for="">How To Search</label>

<div >
    <input type="radio" name="name1" id="exact">
    <label class=" radio-inline ml-1" for="exact">Exact Match</label>
</div>

<div >
    <input type="radio" name="name1" id="lead">
    <label class=" radio-inline ml-1" for="lead">Leading Characters</label>
</div>

<div >
    <input type="radio" name="name1" id="trail">
    <label class=" radio-inline ml-1" for="trail">Trailing Characters</label>
</div>

<div >
    <input type="radio" name="name1" id="all">
    <label class=" radio-inline ml-1" for="all">All Characters</label>
</div>



</div>




</div>


















                 <div class="col-md-12">
                   
                 </div>          
         
             

             
                 <table  class="mb-0 table-striped" id="table">      
                           
                           <thead>
                           
                           <tr>


                           <th>E</th>
                          <th>M</th>
                          <th>C</th>
                          <th>H</th>
                          <th>B</th>
                          <th>S</th>
                          <th>Run Date</th>
                          <th>Run#</th>
                          <th>Chart</th>
                          <th>Name</th>
                          <th>Date of Birth</th>
                          <th>Age</th>
                          <th>Sex</th>
                          <th>Address</th>
                          <th>Ward</th>
                          <th>Clinician</th>
                          <th>GP</th>
                          <th>Hospital</th>
                          <th>Lab No</th>
                      
              
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

  {data: 'E', name: 'E'},
    {data: 'M', name: 'M'},
   {data: 'C', name: 'C'},
   {data: 'H', name: 'H'},
   {data: 'B', name: 'B'},
    {data: 'S', name: 'S'},
   {data: 'rundate', name: 'rundate'},
   {data: 'run#', name: 'run#'},
   {data: 'chart', name: 'chart'},
    {data: 'name', name: 'name'},
   {data: 'B.O.B', name: 'D.O.B'},
   {data: 'age', name: 'age'},
   {data: 'sex', name: 'sex'},
    {data: 'address', name: 'address'},
   {data: 'ward', name: 'ward'},
   {data: 'clinician', name: 'clinician'},
   {data: 'GP', name: 'GP'},
   {data: 'hospital', name: 'hospital'},
   {data: 'lab#', name: 'lab#'}
     
],
"order":[[1, 'desc']], 

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



     
    




   

});  
    </script>





@endpush