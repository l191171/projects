@include('layouts.header')
 
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">User
               <a class="btn btn-info btn-sm" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Go Back </a>
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


                  <form id="form">
                                       {{ csrf_field() }}
                              
                         <div class="card card-primary card-outline">
                            <div class="card-body ">  

                  <div class="col-md-12">
                    <h3>Update User Info</h3>
                  </div>            
                <div class="row">
                  <input type="hidden" name="id" value="{{$data['id']}}">
                  <div class="col-sm-4">
                  <label for="name">Name</label>
                  <input type="text" placeholder="Enter Name" name="uname" class="form-control my-2" value="{{$data['name']}}">
                  </div>
                  <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" placeholder="Enter Email" name="uemail" class="form-control my-2" value="{{$data['email']}}">
                  </div>
                  <div class="col-md-4">
                    <label for="tel" >Phone </label>
                    <input type="tel" placeholder="Enter Phone Number" name="phoneno" class="form-control my-2" value="{{$data['phonenumber']}}" > 
                  </div>
                </div>

                
                 
                <div class="row">
                <div class="col-md-4">
                  <label class="form-label mt-2">Department Id</label>
                <select name="did" class="form-select form-control my-3"   aria-label="Default select example">
                  <option >{{$data['departmentid']}}</option>
               @foreach($depart as $departs)
                <option value="{{$departs->id}}" >{{$departs->departmentname}}</option>
               @endforeach
                </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label mt-2">Client Id</label>
                  <select name="cid" class="form-select form-control my-3 "aria-label="Default select example">
                  <option >{{$data['clientid']}}</option>
                 @foreach($client as $clients)
                <option  value="{{$clients->id}}">{{$clients->name}}</option>
               @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label mt-2">User Status</label>
                <select name="ustatus" class="form-select form-control my-3"  aria-label="Default select example">
                  <option >{{$data['status']}}</option>
                    <option>Active</option>
                    <option>Deactive</option>
                </select>
                </div>
            </div>
                <a href="http://localhost:8080/tickets/public/User" class="btn btn-warning mt-4">Add User</a>

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
<script type="text/javascript">
 $(document).ready(function() {
         

            $("#btn1").click(function(){
                let myform = document.getElementById("form");
                let data = new FormData(myform);

                $.ajax({

                    url: "Userview",
                    data: data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                }).done(function(response){
                 
                    window.location="http://localhost:8080/tickets/public/Usert";
                 
                })
                
                event.preventDefault();

            });
            
            
        });
</script>
@endpush