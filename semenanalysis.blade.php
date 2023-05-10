@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Semen Analysis
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
                   <div class="col-md-3 border">
                    <div class="col-md-12 p-2">
                      <label class="form-label">Sample ID</label>
                     
                     <input type="number" name="sid" class="form-control">
                    </div>
                    <div class="col-md-12 p-2">
                      <label class="form-label">MRU</label>
                     <input type="number" name="sid" class="form-control">
                    </div>
                   </div>
                   <div class="col-md-9">
                     <div class="row">
                       <div class="col-md-4">
                         <label class="form-label">Canavan Chart #</label>
                         <input type="text" name="canavan" class="form-control">
                       </div>
                     <div class="col-md-3">
                           <label class="form-label">Surname</label>
                         <input type="text" name="sname" class="form-control">
                       </div>
                       <div class="col-md-3 ">
                           <label class="form-label">Forename</label>
                         <input type="text" name="fname" class="form-control">
                       </div>
                       <div class="col-md-1 mt-4">
                         <a href="#" class="btn btn-primary mt-1">Search</a>
                       </div>

                     </div>
                     <div class="row pt-4" >
                       <div class="col-md-4">
                         <label class="form-label">D.O.B</label>
                         <input type="text" name="dob" class="form-control">
                       </div>
                      <div class="col-md-3">
                         <label class="form-label">Age</label>
                         <input type="text" name="age" class="form-control">
                       </div>
                      <div class="col-md-3">
                         <label class="form-label">Sex</label>
                         <input type="text" name="sex" class="form-control">
                       </div>
                       <div class="col-md-1 mt-4">
                         <a href="#" class="btn btn-primary mt-1">Search</a>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="row">
                   <div class="col-md-12">
                     <label class="form-label"> </label>
                     <input type="text" name="gp" value="GP :cmbGP" class="form-control">
                   </div>
                 </div>
             
                  <div class="row mt-4">
                    <div class="col-md-3">
                      <div class="row">
                           <div class="col-md-12">
                        <label class="form-label">Chart #:</label>
                        <input type="text" name="chart"  class="form-control">
                      </div>
                      </div>
                       <div class="row">
                              <div class="col-md-12">
                        <label class="form-label">Name</label>
                        <input type="text" name="name"  class="form-control">
                      </div>
                       </div>
                       <div class="row">
                             <div class="col-md-12">
                        <label class="form-label">D.O.B</label>
                        <input type="text" name="dob"  class="form-control">
                      </div>
                       </div>
                   
                      <div class="row">
                         <div class="col-md-6">
                        <label class="form-label">Age</label>
                        <input type="text" name="age"  class="form-control">
                      </div>
                                             <div class="col-md-6">
                        <label class="form-label">Sex</label>
                        <input type="text" name="sex"  class="form-control">
                      </div>
                      </div>
                      <div class="row">
                            <div class="col-md-12">
                          <label class="form-label">Address</label>
                          <input type="text" name="address" class="form-control">
                          <input type="text" name="address" class="form-control mt-4">
                        </div>
                      </div>
                    <div class="row">
                         <div class="col-md-12">
                          <label class="form-label">Hospital</label>
                          <input type="text" name="hospital" class="form-control">
                        </div>
                    </div>
                      <div class="row">
                           <div class="col-md-12">
                          <label class="form-label">Ward</label>
                          <input type="text" name="ward" class="form-control">
                        </div>
                      </div>
                      <div class="row">
                          <div class="col-md-12">
                          <label class="form-label">Clinician</label>
                          <input type="text" name="clinician" class="form-control">
                        </div>
                      </div>
                       <div class="row">
                           <div class="col-md-12">
                          <label class="form-label">GP</label>
                          <input type="text" name="gp" class="form-control">
                        </div>
                       </div>
                        <div class="row">
                            <div class="col-md-12">
                          <label class="form-label">Comments</label>
                          <input type="text" name="comments" class="form-control">
                        </div>
                        </div>
                       <div class="row">
                         <div class="col-md-12 mt-3">
                          <textarea class="form-control" name="text"></textarea>
                        </div>
                       </div>
                        

                    </div>
                    <div class="col-md-7 border">
                      <h4>Date</h4>
                      <div class="row">
                        <div class="col-md-6">
                          <label class="form-label">Run</label>
                          <input type="date" name="run" class="form-control">
                        </div>
                      <div class="col-md-6">
                          <label class="form-label">Sample</label>
                          <input type="date" name="sample" class="form-control">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 mt-4">
                          <label class="form-label">Recieved in Lab</label>
                          <input type="date" name="recievedinlab" class="form-control">
                        </div>
                        <div class="col-md-4 mt-4 mx-2">
                          <div class="col-md-12 mt-2">
                            <input type="radio" name="date" class="form-check-input">
                            <label class="form-check-label">Routine</label>
                          </div>
                          <div class="col-md-12 mt-2">
                            <input type="radio" name="date" class="form-check-input">
                            <label class="form-check-label">Out of Hours</label>
                          </div>
                        </div>
                      </div>
                      <div class="border mt-4 p-2">
                        <div class="row">
                          <div class="col-md-6">
                            <label class="form-label">Specimen Type</label>
                            <input type="text" name="stype" class="form-control">
                          </div>
                         <div class="col-md-6">
                            <label class="form-label">Viscosity</label>
                            <input type="text" name="viscosity" class="form-control">
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <label class="form-label">Sperm Count(Milon per mL)</label>
                            <input type="number" name="scount" class="form-control">
                          </div>
                         <div class="col-md-6">
                            <label class="form-label">Volume(mL)</label>
                            <input type="number" name="volume" class="form-control">
                          </div>
                        </div>
                       <div class="row">
                         <div class="col-md-6">
                          <div class="row">
                          <div class="col-md-12">
                            <label class="form-label">PH</label>
                            <input type="number" name="ph" class="form-control">
                          </div>
                          </div>
                          <div class="row">

                          <div class="col-md-9">
                            <label class="form-label">Sperm Morphology</label>
                            <input type="number" name="smorphology" class="form-control">
                          </div>
                          <div class="col-md-3 mt-4">
                            <label class="mt-2">%Normal</label>
                          </div>
                          </div>
                          <div class="row mt-4">
                            <div class="col-md-12">
                              <label class="form-label">Comment (Intesify + Post Vaseclony)</label>
                              <input type="text" name="cin" class="form-control">
                            </div>
                          </div>
                         </div>
                           <div class="col-md-6">
                              <div class="col-md-12 border p-2 mt-4">
                               <h5>Modify</h5>
                               <div class="row">
                                 <div class="col-md-6">
                                  <input type="text" name="gradea" class="form-control">
                               </div>
                                 <div class="col-md-6 mt-2">
                                 <label class="form-label">% Grade A</label>
                               </div>
                               </div>
                                <div class="row">
                                 <div class="col-md-6">
                                  <input type="text" name="gradeb" class="form-control">
                               </div>
                                 <div class="col-md-6 mt-2">
                                 <label class="form-label">% Grade B</label>
                               </div>
                               </div>
                                                              <div class="row">
                                 <div class="col-md-6">
                                  <input type="text" name="gradec" class="form-control">
                               </div>
                                 <div class="col-md-6 mt-2">
                                 <label class="form-label">% Grade C</label>
                               </div>
                               </div>
                                                              <div class="row">
                                 <div class="col-md-6">
                                  <input type="text" name="graded" class="form-control">
                               </div>
                                 <div class="col-md-6 mt-2">
                                 <label class="form-label">% Grade D</label>
                               </div>
                               </div>
                             </div>
                         </div>
                       </div>
                       <div class="col-md-12 mt-4">
                         <textarea class="form-control" name="testarea" rows="4"></textarea>
                       </div>

                      </div>
                        <div class="col-md-12 p-2">
                          <label class="form-label">CI Details:</label>
                          <input type="text" name="cidet" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                     <div class="col-md-12">
                       <a href="#" class="btn btn-primary w-100">Transfusion Details</a>
                     </div>
                    <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">Phone Results</a>
                     </div>
                       <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">Print ?</a>
                     </div>
                       <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">Print & Hold</a>
                     </div>
                        <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">Print</a>
                     </div>
                        <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">FAX</a>
                     </div>
                        <div class="col-md-12 text-center">
                       <a href="#" class="btn btn-primary mt-4 p-3 w-100">Cancel</a>
                     </div>
                    </div>
                  </div>
                
                  <div class="col-md-12 mt-4">
                    <a href="#" class="btn btn-primary">Order Analysis</a>
                    <a href="#" class="btn btn-success">Save & Hold</a>
                    <a href="#" class="btn btn-warning">Save</a>
                    <a href="#" class="btn btn-secondary">Validate</a>
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