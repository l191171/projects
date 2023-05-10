@include('layouts.header')
    <style>
   .input-field {
            width: 100%;
            padding: 10px;
            text-align: center;
        }
        .fa-eye {
            padding: 10px;
            min-width: 40px;
        }

        .input-icons i {
            position: absolute;
        }
          
        .input-icons {
            width: 100%;
            margin-bottom: 10px;
        }
          
  </style>
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">My Profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">My Profile</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


     <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <div class="row">

         <div class="col-md-4">

            <!-- Profile -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="images/{{ Auth::user()->file }}" alt="User profile picture" onerror="this.onerror=null;this.src='{{ asset('images/'.'dp.webp') }}';">
                </div>

                <h3 class="profile-username text-center">{{ Auth::user()->name }}</h3>

                <p class="text-muted text-center">{{$data['role'][0]->name}}</p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Phone</b> <a class="float-right">{{ Auth::user()->phone }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>E-Mail</b> <a class="float-right">{{ Auth::user()->email }}</a>
                  </li>
                  <li class="list-group-item text-center">
                  <a>{{ Auth::user()->address }} {{ Auth::user()->city }} {{ Auth::user()->state }} {{ Auth::user()->country }}</a>
                  </li>

                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>


          <div class="col-md-8">

            <!-- Update Profile -->
            <div class="card card-primary card-outline">
              <div class="card-body">
                <form id="form">
                  <h4>Update Profile</h4>
                  <div class="row"> 
                  {{ csrf_field() }}
                  <div class="form-group col-md-12">
                    <label>Name <span>*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Phone <span>*</span></label>
                    <input type="text" class="form-control" name="phone" value="{{ Auth::user()->phone }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Email address <span>*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}">
                  </div>
                   <div class="form-group col-md-12">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="{{ Auth::user()->address }}">
                  </div>
                   <div class="form-group col-md-6">
                    <label>Town </label>
                    <input type="text" class="form-control" name="town" value="{{ Auth::user()->city }}">
                    
                  </div>
                  <div class="form-group col-md-6">
                    <label>County </label>
                    <input type="text" class="form-control" name="state" value="{{ Auth::user()->state }}">
                
                  </div>
                  <div class="form-group col-md-6">
                    <label>Country </label>
                    <input type="text" class="form-control" name="country" value="{{ Auth::user()->country }}">
                    
                  </div>
                  <div class="form-group col-md-6">
                    <label>Postal / Zip Code </label>
                    <input type="text" class="form-control" name="zip" value="{{ Auth::user()->zip }}">
                  </div>

                  <div class="form-group col-md-6">
                    <label>Change Avatar</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="file">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div>
                  </div>
                 </div> 
                </form>
                <button type="button" id="updateProfile" class="btn btn-primary float-right">Update Profile</button>
                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- Update Password -->
            <div class="card card-primary card-outline" id="PasswordSection">
              <div class="card-body">
                <form id="formPassword">

                <h4>Update Password</h4>
                  <div class="row">
                  {{ csrf_field() }}
                  <div class="form-group col-md-4">
                    <label>Current Password <span>*</span></label>
                    <div class="input-icons">
                    <i class="fas fa-eye" id='pass1'></i>
                    <input type="password" class="form-control input-field" name="current_password" id="current_password" >
                  </div>
                  </div>
                  <div class="form-group col-md-4">
                    <label>New Password <span>*</span></label>
                      <div class="input-icons">
                    <i class="fas fa-eye" id='pass2'></i>
                    <input type="password" class="form-control input-field" name="password" id='password'>
                  </div>
                  </div>
                  <div class="form-group col-md-4">
                    <label>Confirm New Password <span>*</span></label>
                    <div class="input-icons" >
                    <i class="fas fa-eye" id='pass3'></i>
                    <input type="password" class="form-control input-field" name="confirm_password" id='confirm_password'>
                  </div>
                  </div>
                  </div> 
                 </form>
                 <button type="button" id="updatePassword" class="btn btn-primary float-right">Update Password</button>
                </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


          </div>




        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  


@extends('layouts.footer')
@push('script')
<script type="text/javascript">
 
   var data = @json($data);
          
          var profile = data['profile'];

          if(profile.length > 0) {

          if(profile[0].new == 1) {

            $('html, body').animate({
              scrollTop: $("#PasswordSection").offset().top
          }, 2000);
          $('#current_password').focus();  
          }    
          

              $("#country").val(profile[0].country)
              $("#state").val(profile[0].state)
              $("#city").val(profile[0].city)



          }

        $( "#country" ).select2();
        $( "#state" ).select2();
        $( "#city" ).select2(); 


$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

$("#updateProfile").click(function (event) {


        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#form')[0];

        // Create an FormData object
        var data = new FormData(form);

        
        if($("#file").val() != '') {
 
         var fileExtension = ['jpeg', 'jpg','png', 'gif', 'bmp'];
        if ($.inArray($("#file").val().split('.').pop().toLowerCase(), fileExtension) == -1) {

              Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: "Only formats are allowed : "+fileExtension.join(', '),
                                icon: 'bx bx-info-circle'
                            });
            return false;
        }
        }

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{ route('updateMyProfile') }}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(data) {
                  if ($.isEmptyObject(data.error)){
                         Lobibox.notify('success', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: data.success,
                                icon: 'bx bx-check-circle'
                            });

                            location.reload();
  

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


$('#pass1').click(function(){
  // $('#current_password').
  var x=document.getElementById('current_password');
  if(x.type=='password'){
x.type = 'text'
  }
  else{
  x.type = 'password'

  }
});


$('#pass2').click(function(){
  // $('#current_password').
  var x=document.getElementById('password');
  if(x.type=='password'){
x.type = 'text'
  }
  else{
  x.type = 'password'

  }
});


$('#pass3').click(function(){
  // $('#current_password').
  var x=document.getElementById('confirm_password');
  if(x.type=='password'){
x.type = 'text'
  }
  else{
  x.type = 'password'

  }
});


$("#updatePassword").click(function (event) {


        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#formPassword')[0];

        // Create an FormData object
        var data = new FormData(form);

        

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{ route('updateUserPassword') }}",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(data) {
                  if ($.isEmptyObject(data.error)){
                         Lobibox.notify('success', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: data.success,
                                icon: 'bx bx-check-circle'
                            });
                         location.reload();

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


    });

</script>
@endpush