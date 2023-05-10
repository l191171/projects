@include('layouts.header')
  
  <!-- Content Wrapper. Contains page content -->

   <!-- Main content -->
   <div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">

    <form method="post" action="{{ url('/searche')}}">
  @csrf
  <div class="wrapper shadow-sm p-2 mb-3 bg-white rounded">

<div class="grouped justify-content-between p-1">
<div class="row">
              <div class="col-md-3">
        <label for="name" class="form-label">TicketID</label>
        <input type="text" placeholder="Id" name="tid" id="ticketid" class="form-control" >
    </div>
          <div class="col-md-3">
        <label for="text"  class="form-label">Date</label>
        <input type="datetime-local" name="date" class="form-control select-opt" >
    </div>
    <div class="col-md-3">
        <label for="email" class="form-label">UserList</label>
        <select  class="form-select form-control select-opt"  name="ulist"   aria-label="Default select example">
            <option>Active</option>
            <option>Deactive</option>
        </select>
    </div>
    <div class="col-md-3">
        <label for="email">Priorty</label>
        <select  class="form-select form-control" name="upriority"  aria-label="Default select example">
             <option value="0">Option</option>
            <option>Medium</option>
            <option>Low</option>
        </select>
    </div>

</div>
<div class="row">
      <div class="col-md-3">
        <label for="form-label">status</label>
        <select  class="form-select form-control"  name="ustatus"   aria-label="Default select example">
            <option>Active</option>
            <option>Deactive</option>
        </select>
    </div>
    <div class="col-md-3">
    <label for="email" class="form-label">Department</label>
        <select  class="form-select form-control"  name="udepartment"   aria-label="Default select example">
           <option>Active</option>
           <option>Deactive</option>
        </select>
    </div>
    <div class="col-md-3">
      <label for="email" class="form-label">Subject</label>
        <select  class="form-select form-control"  name="usubject" aria-label="Default select example">
          <option>Active</option>
          <option>Deactive</option>
        </select>
    </div>
  <div class="mt-4 "><button type="submit" class="btn btn-outline-primary mt-1 ">Submit</button></div>
</div>

          <div class="grouped">
              <label for="name">TickeID</label>
              <input type="text" placeholder="Id" name="tid" id="ticketid" class="form-control" >
          </div>
          <div>
          <button type="submit" class="btn btn-outline-primary">Submit</button>
      </div>
                              </form>
        
          
                       <div class="card card-primary card-outline">
                          <div class="card-body table-responsive"> 
           <table id="table"  class="table mb-0 table-striped table">
                               
           <thead>
           
           <tr>
           
            <th>ID</th>
            <th>Client</th>
            <th>date#</th>
            <th>Updatet</th>


            <th>Priority</th>
            <th>Department</th>
            <th>Sample#</th>

          
            </tr>

            </thead>
            @foreach ($products as $item)         
         
            <tbody>
<tr>

<td>


{{$item->ticketid}}

</td>


<td>


{{$item->patientname}}

</td>
<td>


{{$item->created_at}}

    </td>    
      <td>


{{$item->updated_at}}

    </td>     
    
    <td>


{{$item->priority}}

    </td>      
    
    <td>


{{$item->department}}

    </td>   
    
    <td>


{{$item->subject}}

    </td>

</tr>
@endforeach
   
</tbody>
           

        
             </table>
          


             </div>
          

</div>
             </div>

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
<script>
        $(document).ready( function () {
        $('#table').DataTable();
    } );
    </script> 

@endpush