@include('layouts.header')
 
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Departments
               <a class="btn btn-info btn-sm" href="{{route('Departments')}}"><i class="fas fa-arrow-left"></i> Go Back </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="">Home</a></li>
              <li class="breadcrumb-item active">Departments</li>
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
          
                 <div class="row">
                   <div class="col-md-6">
                     <label for="name">Department Name</label>
                <input type="text" placeholder="Enter Name" name="dname" class="form-control my-3">
                   </div>
                   <div class="col-md-6">
                    <label class="form-label">Status</label>
                      <select name="dstatus" class="form-select form-control my-3" aria-label="Default select example">
                      <option value="0">Choose Status</option>
                    <option>Active</option>
                    <option>Deactive</option>
                </select>
                   </div>
                 </div>
                

                <button type="submit" id="btn1" class="btn btn-success mt-4 float-right" >Submit</button>      

           
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
<script >
  $(document).ready(function() {
         

            $("#btn1").click(function(){

                let myform = document.getElementById("form");
                let data = new FormData(myform);

                $.ajax({

                    url: "Department",
                    data: data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                }).done(function(response){
                 if(response[1] == 'true'){
                  $("#result").html("Department save sucessfully");
                  window.location="Department";
                 }
                });
                event.preventDefault();

            });
            
            
        });
</script>
@endpush