@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Clients
               <a class="btn btn-info btn-sm" href="{{route('home')}}"><i class="fas fa-arrow-left"></i> Go Back </a>
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
                     <h3>Update Client</h3>
                 </div>           
                 <div class="row">
                      <input type="hidden" name="id" value="{{$data['id']}}">
                     <div class="col-md-4">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" placeholder="Enter Name" name="name" class="form-control my-3" value="{{$data['name']}}">
                     </div>
                     <div class="col-md-4">
                        <label for="email">Address</label>
                        <input type="text" placeholder="Enter Address" name="address" class="form-control my-3" value="{{$data['address']}}">
                     </div>
                                   <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select form-control my-3" aria-label="Default select example">
                        <option >{{$data['status']}}</option>
                        <option>Active</option>
                        <option>Deactive</option>
                </select>
                     </div>
                 </div>      
             

             
         
                
               <a href="http://localhost:8080/tickets/public/Client" class="btn btn-warning mt-4">Add Client</a>


                <button type="submit" id="btn1" name="submit" class="btn btn-sm btn-success mt-4 float-right " >Submit</button>    
                    

                   
           
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

                    url: 'Client',
                    data: data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                }).done(function(response){

                     window.location="http://localhost:8080/tickets/public/Clientt";
                   
                });
                
                event.preventDefault();
                
            });
            
            
        });
</script>
@endpush