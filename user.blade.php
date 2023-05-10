@include('layouts.header')
   <style type="text/css">
.flex-wrap {
    -webkit-flex-wrap: wrap!important;
    -ms-flex-wrap: wrap!important;
    flex-wrap: wrap!important;
    width: 34%;
    display: inline-block;
    text-align: center;
    top: -3px;
}
#table td:last-child, #table th:last-child {
    text-align: left;
    width: 80% !important;
}
td label:not(.form-check-label):not(.custom-file-label) {
    font-weight: 700;
    width: 70px;
}
td {
    padding: 5px 10px !important;
}
.custom-control {
    top: 0px;
    display: inline-block;
}
.dataTables_info, .dataTables_paginate {
    display: none;
}
#table_filter {
    display: none;
}
  </style>

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">User Info 
                <a class="btn btn-info btn-sm" href="{{route('Users')}}"><i class="fas fa-arrow-left"></i> GO BACK </a>
            </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
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
                            <div class="card-body table-responsive">                  
                                
                             
                                
                                            <div class="row">
                                            <div class="col-md-12"><h4>Basic Info</h4></div>
                                            <div class="col-md-3 form-group ">
                                            <label  class="col-form-label">Name <span>*</span></label>
                                                 <input type="text" class="form-control" id="name" name="name" value="" />
                                             </div>

                                             <div class="col-md-3 form-group ">
                                            <label  class="col-form-label">Email <span>*</span></label>
                                                 <input type="text" class="form-control" id="email" name="email" value="" />
                                             </div> 

                                             <div class="col-md-3 form-group ">
                                            <label  class="col-form-label">Phone </label>
                                                 <input type="text" class="form-control" id="phone" name="phone" value="" />
                                             </div>  

                                             <div class="col-md-3 form-group ">
                                            <label  class="col-form-label">Password @if ($data['editmode'] == 'off') <span>*</span> @endif</label>
                                                 <input autocomplete="off" type="text" class="form-control" id="password" name="password" value="" />
                                             </div>  


                                             <div class="col-md-12  mt-2"></div>

                                             <div class="form-group col-md-3">
                                                <label>Town </label>
                                                <select class="form-control"  id="city" name="city">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['towns'] as $town)
                                                    <option>{{$town->Text}}</option>
                                                    @endforeach
                                                </select>
                                              </div>
                                              <div class="form-group col-md-3">
                                                <label>County </label>
                                                <select class="form-control"  id="state" name="state">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['counties'] as $county)
                                                    <option>{{$county->Text}}</option>
                                                    @endforeach
                                                </select>
                                              </div>
                                              <div class="form-group col-md-3">
                                                <label>Country </label>
                                                <select class="form-control"  id="country" name="country">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['countries'] as $country)
                                                    <option>{{$country->Text}}</option>
                                                    @endforeach
                                                </select>
                                              </div> 

                                             <div class="form-group col-md-3">
                                            <label>Employee No. </label>
                                                 <input type="text" class="form-control" id="zip" name="zip" value="" />
                                             </div>

                                              <div class="col-md-3">
                                            <label>Address</label>
                                                 <textarea class="form-control" id="address" rows="1" name="address" ></textarea>
                                             </div>  

                         

                                             <div class="form-group col-md-3">
                                            <label>Role <span>*</span></label>
                                                  <select class="form-control" name="role" id="role">
                                                    <option value="">Choose a Role</option>
                                                    @if(Auth::user()->role==4)
                                                        @foreach ($data['roles2'] as $role)
                                                        <option value="{{$role->id}}">{{$role->Text}}</option>
                                                        @endforeach
                                                        @else
                                                        @foreach ($data['roles'] as $role)
                                                        <option value="{{$role->id}}">{{$role->Text}}</option>
                                                        @endforeach
                                                        @endif
                                                  </select>      
                                             </div>

                                       


                                             <div class="form-group col-md-3">
                                            <label>Status <span>*</span></label>
                                                 <select class="form-control" id="InUse" name="InUse">
                                                     <option>Active</option>
                                                     <option>InActive</option>
                                                     <option>Pending</option>
                                                 </select>
                                             </div>

                                

                                             
                                        
                                           </div>   
                          
                                          


                            </div>
                        </div> 


                                           <div class="row">
                                                <div class="col-md-12 mb-3">
                                                <button type="button" class="btn btn-primary AddUpdatebtn float-right">Save Now</button>
                                             </div> 
                                           </div>

                                      </form>


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


<script type="text/javascript">
    $(document).ready(function () {

          $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


                $( "#state" ).select2({
                                placeholder:'Choose State',
                                allowClear:true
                               });

                $( "#city" ).select2({
                                placeholder:'Choose City',
                                allowClear:true
                               });

                $( "#country" ).select2({
                                placeholder:'Choose Country',
                                allowClear:true
                               });

                $( "#InUse" ).select2({
                                placeholder:'Choose Status',
                                allowClear:true
                               });

                $( "#role" ).select2({
                                placeholder:'Choose Role',
                                allowClear:true
                               });

                $( "#department" ).select2({
                                placeholder:'Choose Department',
                                allowClear:true
                               });

                $( "#subdepartment" ).select2({
                                placeholder:'Choose a Sub Department',
                                allowClear:true
                               });



          $(document).on('select2:select', '#subdepartment', function () { 

                    if($('#department').val() == '' || $('#department').val() == null) {

                        Lobibox.notify('warning', {
                                            pauseDelayOnHover: true,
                                            continueDelayOnInactiveTab: false,
                                            position: 'top right',
                                            msg: 'Please select a department first.',
                                            icon: 'bx bx-info-circle'
                                        });  
                        $('#subdepartment').val(null).trigger('change');                                          
                        return false;
                    }

          })


         var $select1 = $('#department'),
          $select2 = $('#subdepartment'),
          $options = $select2.find('option');

        $(document).on('select2:select', '#department', function () { 
        $('#subdepartment').val(null).trigger('change');   
          var size = $(this).find("option:selected").data("size");
          $select2.html($options.filter('[data-size="' + size + '"]'));
          $('#subdepartment').val(null).trigger('change'); 
        });




        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');




  var data = @json($data);

        var user = data['user'];

            if(user.length > 0) {

            $('#name').val(user[0].name);
            $('#phone').val(user[0].phone);
            $('#email').val(user[0].email);
            $('#country').val(user[0].country).trigger('change');;
            $('#state').val(user[0].state).trigger('change');;
            $('#city').val(user[0].city).trigger('change');;
            $('#zip').val(user[0].zip);
            $('#role').val(user[0].role).trigger('change');
            $('#department').val(user[0].department).trigger('change');
            $('#subdepartment').val(user[0].subdepartment).trigger('change');
            $('#InUse').val(user[0].status).trigger('change');
            $('#address').val(user[0].address);

            

            }
  

             // if not  edit mode 
            if(user.length == 0) {

                $('.AddUpdatebtn').attr('id','btnAdd');

            } else {
            
                $('.AddUpdatebtn').attr('id','btnUpdate');
            }


            //add and update js code

            $(".AddUpdatebtn").click(function (event) {


                
                    //stop submit the form, we will post it manually.
                    event.preventDefault();

                    // Get form
                    var form = $('#form')[0];

                    // Create an FormData object
                    var data = new FormData(form);


                    // append account names to form 
                     var accountname = $(".accountname");
                    console.log(accountname);
                     
                        for(var i = 0; i < accountname.length; i++){
                            
                             data.append("accountname[]", $(accountname[i]).text());

                        }
                        


                    if(this.id == 'btnAdd') {

                        var url = "{{ route('addUser') }}";        
                       
                    } else {

                        var url = "{{ route('updateUser') }}";   
                        // append customer id to form
                          data.append("uid", user[0].id);

                    }


                    $.ajax({
                        type: "POST",
                        enctype: 'multipart/form-data',
                        url: url,
                        data: data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        timeout: 600000,
                        success: function(data) {
                            //console.log(data);
                                if ($.isEmptyObject(data.error)){
                                     Lobibox.notify('success', {
                                            pauseDelayOnHover: true,
                                            continueDelayOnInactiveTab: false,
                                            position: 'top right',
                                            msg: data.success,
                                            icon: 'bx bx-check-circle'
                                        });
                                window.location = "{{route('Users')}}"; 

                                } else {
                                     Lobibox.notify('warning', {
                                            pauseDelayOnHover: true,
                                            continueDelayOnInactiveTab: false,
                                            position: 'top right',
                                            msg: data.error,
                                            icon: 'bx bx-info-circle'
                                        });
                                }
                            }

                        });


      

        });


     })   
</script>
@endpush