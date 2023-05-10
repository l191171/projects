@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

     <!-- Main content -->
    <div class="content">
      <div class="container-fluid">


      <div class="row">


<div class="col-md-12">   
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">

        <div class="">
  <div class="row">
    <div class="col-sm">

    @if(count($d) < 1)
    <div class="alert alert-warning">
        <strong>Sorry!</strong> No Product Found.
    </div>                                      
@else 
    <h3>Patient Details</h3>
        <p>Patient: {{$data[0]->name}}</p>
        <p>Address 1:{{$data[0]->addr1}}</p>
        <p>Address 2:{{$data[0]->addr2}}</p>
        <p>Address 3:{{$data[0]->addr3}}</p>
        <p>Address 4:</p>
    </div>
    <div class="col-sm" style="margin-top:2.5rem;">
      <p>MRN :{{$data[0]->patnum}}</p>
      <p>D.O.B:{{$data[0]->DoB}} </p>
      <p>Age </p>
      <p>Sex: {{$data[0]->sex}}</p>
      <p>Group:{{$data[0]->fgroup}} </p>



    </div>
    <div class="col-sm" style="margin-top:2.5rem;">
   <p>AB Report:{{$data[0]->AIDR}} </p>
   <p>Sample Date:{{$data[0]->SampleDate}} </p>
    </div>
  </div>
  
  <div class="row">
    Remarkss: 
  </div>
 
  @endif


</div>









</div>
</div>
</div>
</div>

<div class="row">


<div class="col-md-12">   
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">

        <div>
  <table id="table_id" class=" ">
<thead>
  <tr>
    <th>Sample Data</th>
    <th>Lab Number</th>
    <th>Result</th>
    <th>Comment</th>
    </tr>  
  </thead>
    <tbody>
    <tr>
      <td>A</td>
      <td>B</td>
      <td>A</td>
      <td>B</td>
      </tr>
    </tbody>
  </table>
</div>  


</div>
</div>
</div>
</div>


<div class="row">


<div class="col-md-12">   
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
<!-- 
        <div>
  <table id="table_id" class=" ">
    <thead>
    <tr>
    <th>Product</th>
    <th>Lab Number</th>
    <th>Result</th>
    <th>Comment</th>
    </tr>
    </thead>
    <tbody>
      <tr>
      <td>A</td>
      <td>B</td>
      <td>A</td>
      <td>B</td>
      </tr>
    </tbody>
  </table>
</div> -->
<div>
@if(count($d) < 1)
    <div class="alert alert-warning">
        <strong>Sorry!</strong> No Product Found.
    </div>                                      
@else
  <table id="table_i" class=" ">
<thead>
  <tr>
    <th>Product</th>
    <th>Group</th>
    <th>Unit Number</th>
    <th>Available To</th>
    <th>Status</th>
    <th>Date Time</th>
    </tr>  
  </thead>
    <tbody>
     
@foreach($d as $d)
    <tr>
      <td>pname</td>
      <td>{{$d->fgroup}}</td>
      <td>{{$d->unitnumber}}</td>
      <td>Pending</td>
      <td>{{$d->status}}</td>
      <td></td>
      </tr>

@endforeach
@endif
</tbody>
  </table>
</div>


</div>
</div>
</div>
</div>



  </div>

      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>   
<script type="text/javascript">
        $(document).ready( function () {
        $('#table_id').DataTable();
    } );
    $(document).ready( function () {
        $('#table_i').DataTable();
    } );
</script>

@endpush
