@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Issue a Batch Product
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
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label mb-0" for="lab">Lab Number:</label>
                    <input type="number" id="lab" class="form-control form-control" placeholder="80000441" disabled>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label mb-0" for="type">Type Nex:</label>
                    <input type="text" id="type" class="form-control form-control" placeholder=""disabled>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 mt-2">
                    <label class="form-label  " for="chart">Chart:</label>
                    <input type="number" id="chart" class="form-control form-control" placeholder="221758"disabled>
                  </div>
                  <div class="col-md-6 mt-2">
                      <label class="form-label " for="Group">Group</label>

                      <select id="sel" class="form-select form-control" aria-label="Default select example" disabled>
                      <option value="1">A Pos</option>
                      <option value="2"></option>
                      <option value="3"></option>
                      <option value="4"></option>
                      </select>
                  </div>
                </div>
                  <div class="row">
                  <div class="col-md-12">
                       <label class="form-label mt-2" for="ae">A/E:</label>
                       <input type="text" id="ae" class="form-control form-control" placeholder=""disabled>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-12">
                  <label class="form-label  mt-2" for="name">Name:</label>
                   <input type="text" id="name" class="form-control form-control" placeholder="fryere caoimini"disabled>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-4">
                     <label class="form-label mt-2" for="dob">D.O.B:</label>
                     <input type="text" id="dob" class="form-control " placeholder="01/12/1993"disabled>
                  </div>
                  <div class="col-md-4">
                   <label class="form-label mt-2" for="age">Age:</label>
                   <input type="text" id="age" class="form-control " placeholder="28Y"disabled>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label mt-2" for="sex">Sex:</label>
                     <input type="text" id="sex" class="form-control " placeholder="F"disabled>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-12">
               <label class="form-label mt-2" for="address">Add 1</label>
               <textarea class="form-control"placeholder="34 ONeil Park" id="address" rows="1"disabled></textarea>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-12">
                    <label class="form-label mt-2 " for="address">2</label>
                    <textarea class="form-control"placeholder="34 ONeil Park" id="address" rows="1"disabled></textarea>
                  </div>

                </div>
                <div class="row">
                <div class="col-md-12">
                  <label class="form-label mt-2 " for="address">3</label>
                  <textarea class="form-control"placeholder="34 ONeil Park" id="address" rows="1"disabled></textarea>
                  </div>

                </div>
               <div class="row pt-5">
                <div class="col-md-3">
                <a class="btn btn-info p-3" >Print Both</a>
                </div>
                <div class="col-md-3">
                <a class="btn btn-success p-3" >Print Label</a>
                </div>
                <div class="col-md-3">
                <a class="btn btn-warning p-3">Print Form</a>
                </div>
                <div class="col-md-3">
                <a class="btn btn-danger p-3" >Cancel</a>
                </div>
                </div> 
              </div>
              <div class="col-md-6">
               <h4 class="text-center">Issue Product</h4>
               <div class="row">
                 <div class="col-md-12">
                   <label class="form-label mb-0" for="identifier">Identifier:</label>
                   <input type="text" id="identifier" class="form-control form-control" placeholder=""disabled>
                 </div>
               </div>
                <div class="row">
                 <div class="col-md-12">
                   <label class="form-label mt-2" for="product">Product:</label>
                   <input type="text" id="product" class="form-control form-control" placeholder=""disabled>
                 </div>
               </div>
                <div class="row">
                 <div class="col-md-12">
                 <label class="form-label mt-2" for="batch">Batch Number:</label>
                <input type="text" id="batch" class="form-control form-control" placeholder=""disabled>
                 </div>
               </div>
                <div class="row">
                 <div class="col-md-12">
                <label class="form-label mt-2" for="group">Group:</label>
                <input type="text" id="group" class="form-control form-control" placeholder=""disabled>
                 </div>
               </div>
                <div class="row">
                 <div class="col-md-12">
              <label class="form-label mt-2" for="status">Status:</label>
              <input type="text" id="status" class="form-control form-control" placeholder=""disabled>
                 </div>
               </div>
                               <div class="row">
                <div class="col-md-12">
                <label class="form-label mt-2" for="Group">Ward</label>

                     <select id="sel" class="form-select form-control" aria-label="Default select example" disabled>
                         <option value="1">Malernity</option>
                         <option value="2"></option>
                         <option value="3"></option>
                         <option value="4"></option>
                     </select>
                  </div>

                </div>
                <div class="row">
                <div class="col-md-12">
                 <label class="form-label mt-2" for="Group">Clinician</label>

                 <select id="sel" class="form-select form-control" aria-label="Default select example" disabled>
                      <option value="1">Dr Ann Leahy</option>
                      <option value="2"></option>
                      <option value="3"></option>
                      <option value="4"></option>
                 </select>
                  </div>

                </div>
                <div class="row">
                <div class="col-md-12">
                  <label class="form-label mt-2" for="sample">Sample Date/Time:</label>
                  <input type="text" id="sample" class="form-control form-control" placeholder="03/08/2022 21:45:00"disabled>
                  </div>

                </div>
                <div class="row">
                <div class="col-md-12">
                 <label class="form-label mt-2" for="recieved">Recieved Date/Time:</label>
                 <input type="text" id="recieved" class="form-control form-control" placeholder="03/08/2022 21:46:07"disabled>
                  </div>

                </div>
              </div>
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

@endpush