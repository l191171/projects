@include('layouts.header')
 
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Tickets
               <a class="btn btn-info btn-sm" href=""><i class="fas fa-arrow-left"></i> Go Back </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item active">Tickets</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


     <!-- Main content -->
    <div class="content">
      <div class="container-fluid">


                  <form method="post" action="{{ url('/search')}}">
                                       {{ csrf_field() }}
                                                 
                         <div class="card card-primary card-outline">
                            <div class="card-body ">  
                           @csrf
    <h1 id="heading">Report Table</h1>
    <div class="wrapper shadow-sm p-2 mb-3 bg-white rounded">

        <div class="grouped justify-content-between p-1">
            <div class="row">
              <div class="col-md-3">
                <label for="name" class="form-label">TicketID</label>
                <input type="text" placeholder="Id" name="tid" id="ticketid" class="form-control" >
              </div>
              <div class="col-md-3">
                <label for="text"  class="form-label">Date</label>
                <input type="datetime-local" class="form-control select-opt" >
              </div>
              <div class="col-md-3">
                <label for="email" class="form-label">UserList</label>
                <select  class="form-select form-control select-opt"  name="Ustatus"   aria-label="Default select example">
                    <option>Active</option>
                    <option>Deactive</option>
                </select>
              </div>
              <div class="col-md-3">
                <label for="email" class="form-label">Priorty</label>
                <select  class="form-select form-control select-opt" name="Ustatus"  aria-label="Default select example">
                    <option>Active</option>
                    <option>Deactive</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                <label for="email" class="form-label">status</label>
                <select  class="form-select form-control select-opt"  name="Ustatus"   aria-label="Default select example">
                    <option>Active</option>
                    <option>Deactive</option>
                </select>
                </div>
                <div class="col-md-3">
                    <label for="email" class="form-label">Department</label>
                      <select  class="form-select form-control select-opt"  name="Ustatus"   aria-label="Default select example">
                        <option>Active</option>
                        <option>Deactive</option>
                      </select>
                </div>
                <div class="col-md-3">
                    <label for="email" class="form-label">Subject</label>
                      <select  class="form-select form-control select-opt"  name="Ustatus" aria-label="Default select example">
                        <option>Active</option>
                        <option>Deactive</option>
                      </select>
                </div>
                <button type="submit" class="btn btn-outline-primary float-right mt-4">Submit</button>
            </div>







        </div>

    <!-- </br> -->

        <div class="group-two d-flex justify-content-between mt-3">
        


</div>


    <!-- <div class="wrapper2">
    </div> -->
    </div>
             <!--Table to show data-->                      
             <table id="example" class="cell-border compact stripe ">
        <thead>
            <tr>
                <th>TicketID</th>
                <th>UserName</th>
                <th>Date Open</th>
                <th>Date Close</th>
                <th>Priorty</th>
                <th>Status</th>
                <th>Department</th>
                <th>Subject</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($products as $item)
    <tr>
        <td>
        
        
    {{$item->ticketid}}

        </td>
        
   
        <td>
        
        
    {{$item->patientname}}

        </td>
        <td>
        
        
        {{$item->created_at}}
    
            </td>      <td>
        
        
        {{$item->updated_at}}
    
            </td>      <td>
        
        
        {{$item->priority}}
    
            </td>      <td>
        
        
        <!-- {{$item->patientname}} -->
        Active
    
            </td>      <td>
        
        
        {{$item->department}}
    
            </td>      <td>
        
        
        {{$item->subject}}
    
            </td>
        
    </tr>
    @endforeach
            <!-- <tr>
                <td>Row 1 Data 1</td>
                <td>Row 1 Data 2</td>
            </tr>
            <tr>
                <td>Row 2 Data 1</td>
                <td>Row 2 Data 2</td>
            </tr> -->
        </tbody>
    </table>
           
                            </div>
                          </div>

                  </form>        

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>



@extends('layouts.footer')

@push('script')
 <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script> 
<script type="text/javascript">
        $(document).ready( function () {
        $('#example').DataTable();
    } );
    

</script>
@endpush