@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Business Profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">Business Profile</li>
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
                  <img class="profile-user-img img-fluid img-circle" src="images/{{ $data['business'][0]->file }}" alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ $data['business'][0]->name }}</h3>

                <!-- <p class="text-muted text-center">Public Analyst Lab</p> -->

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Phone</b> <a class="float-right">{{ $data['business'][0]->phone }}</a>
                  </li>
                  <li class="list-group-item">
                    <b>E-Mail</b> <a class="float-right">{{ $data['business'][0]->email }}</a>
                  </li>
                  <li class="list-group-item text-center">
                  <a>{{ $data['business'][0]->address }} {{ $data['business'][0]->city }} {{ $data['business'][0]->state }} {{ $data['business'][0]->country }}</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>


          <div class="col-md-8">

            <div class="card card-primary card-outline card-tabs">
              <div class="card-header pt-4 pb-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Business Info</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Address</a>
                  </li>
                   <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-messages" role="tab" aria-controls="custom-tabs-four-messages" aria-selected="false">Customize Theme</a>
                  </li>
                  <li class="nav-item d-none">
                    <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">Data Management</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                  <div class="tab-pane fade active show" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                  <form id="formBusinessInfo">
                  <h4>Basic Info</h4>
                  <div class="row">  
                  {{ csrf_field() }}
                  <div class="form-group col-md-12">
                    <label>Name <span>*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ $data['business'][0]->name }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Website</label>
                    <input type="text" class="form-control" name="website" value="{{ $data['business'][0]->website }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Email address <span>*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ $data['business'][0]->email }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Phone <span>*</span></label>
                    <input type="text" class="form-control" name="phone" value="{{ $data['business'][0]->phone }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Fax</label>
                    <input type="text" class="form-control" name="fax" value="{{ $data['business'][0]->fax }}">
                  </div>


                  <div class="form-group col-md-6">
                    <label>Registration #</label>
                    <input type="text" class="form-control" name="reg" value="">
                  </div>

 
                  <div class="form-group col-md-6">
                    <label>Change Logo</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="file" id="file">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                    </div>
                  </div>
                  </div>
                </form>

            
                  <button type="button" id="updateBusinessInfo" class="update btn btn-primary float-right">Update Info</button>
           

                  </div>


                  <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab">
                    
                    <form id="formBusinessAddress">
                  <h4>Business Address</h4>
                  <div class="row">  
                  {{ csrf_field() }}
                  <div class="form-group col-md-12">
                    <label>Address <span>*</span></label>
                    <input type="text" class="form-control" name="address" value="{{ $data['business'][0]->address }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label>Town <span>*</span></label>
                    <select class="form-control"  id="city" name="city">
                        <option disabled selected value=""></option>
                      @foreach ($data['towns'] as $town)
                        <option>{{$town->Text}}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label>County <span>*</span></label>
                    <select class="form-control"  id="state" name="state">
                        <option disabled selected value=""></option>
                      @foreach ($data['counties'] as $county)
                        <option>{{$county->Text}}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Country <span>*</span></label>
                    <select class="form-control"  id="country" name="country">
                        <option disabled selected value=""></option>
                      @foreach ($data['countries'] as $country)
                        <option>{{$country->Text}}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label>Postal / Zip Code </label>
                    <input type="text" class="form-control" name="zip" value="{{ $data['business'][0]->zip }}">
                  </div>
                  </div>
                </form>

                <button type="button" id="updateBusinessAddress" class="update btn btn-primary float-right">Update Address</button>
       
                  </div>



               <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab">
                    
                  


                  </div>


                  <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel" aria-labelledby="custom-tabs-four-messages-tab">
                   
          
          
                      
                <form id="themeForm">
                  
                   <ul class="nav nav-tabs" id="custom-tab" role="tablist">
                  
                  <li class="nav-item">
                 
                     <a data="all" class="nav-link text-secondary active px-4" id="colorscheme_" data-toggle="pill" href="#custom-tab-colorscheme_" role="tab" aria-controls="custom-tab-colorscheme_" aria-selected="true">Color Scheme</a>
                     <input type="hidden" name="colorscheme" id="colorscheme" value="#{{Auth::user()->colorscheme}}">

                  </li>


                   <li class="nav-item">
                 
                     <a data="all" class="nav-link text-secondary  px-4" id="font_" data-toggle="pill" href="#custom-tab-font_" role="tab" aria-controls="custom-tab-font_" aria-selected="true">Font Family</a>
                     <input type="hidden" name="font" id="font" value="{{Auth::user()->font}}">
                     <input type="hidden" name="font_link" id="font_link" value="{{Auth::user()->font_link}}">
                     <input type="hidden" name="font_weight" id="font_weight" value="{{Auth::user()->font_weight}}">

                  </li>


                   <li class="nav-item">
                 
                     <a data="all" class="nav-link text-secondary px-4" id="options_" data-toggle="pill" href="#custom-tab-options_" role="tab" aria-controls="custom-tab-options_" aria-selected="true">Other Options</a>
                     <input type="hidden" name="resolution" id="resolution" value="{{Auth::user()->resolution}}">

                  </li>
 

                </ul>


                </form>


                  <div class="tab-content py-1" id="custom-tabContent">
            

                   <div class="tab-pane fade show active" id="custom-tab-colorscheme_" role="tabpanel" aria-labelledby="colorscheme_">
                   <div class="material-color-picker pt-2">
                          <div class="material-color-picker__left-panel">
                              <ol class="color-selector" data-bind="foreach: materialColors">
                                  <li>
                                      <input name="material-color" type="radio" data-bind="attr: { id: 'materialColor' + $index() }, checked: selectedColor, value: color" >
                                      <label data-bind="attr: { for: 'materialColor' + $index(), title: color }, style: { 'color': $data.variations[4].hex }"></label>
                                  </li>
                              </ol>
                          </div>
                          <div class="material-color-picker__right-panel" data-bind="foreach: materialColors">
                              <div class="color-palette-wrapper" data-bind="css: { 'js-active': selectedColor() === color }">
                                  <h2 class="color-palette-header" data-bind="text: color"></h2>
                                  <ol class="color-palette" data-bind="foreach: variations">
                                      <li id="clipboardItem" class="color-palette__item" data-bind="attr: { 'data-clipboard-text': hex }, style: { 'background-color': hex }">
                                          <span data-bind="text: weight"></span>
                                          <span data-bind="text: hex"></span>
                                          <span class="copied-indicator" data-bind="css: { 'js-copied': copiedHex() === hex }">Color copied!</span>
                                      </li>
                                  </ol>
                              </div>
                          </div>
                      </div>
                   </div>

               <div class="tab-pane fade fontTab" id="custom-tab-font_" role="tabpanel" aria-labelledby="font_">
                    
                
                  

                    <div class="row  pt-2 pb-2">
                        
                    <div class="jumbotron mb-1 col-md-6" id="Nunito">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400&display=swap" rel="stylesheet">
                    <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family: 'Nunito', sans-serif;" >Nunito</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="Nunito">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600&display=swap" rel="stylesheet">
                    <h1 id="600" style="margin-bottom:0px;font-weight: 600;font-family: 'Nunito', sans-serif;" >Nunito</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="PT_Sans">
                    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@400&display=swap" rel="stylesheet">
                    <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family: 'PT Sans', sans-serif;" >PT Sans</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="PT_Sans">
                    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&display=swap" rel="stylesheet">
                    <h1 id="700" style="margin-bottom:0px;font-weight: 700;font-family: 'PT Sans', sans-serif;" >PT Sans Bold</h1>
                    </div>

                   


                    <div class="jumbotron mb-1 col-md-6" id="Raleway">
                      <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400&display=swap" rel="stylesheet">
                      <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family:'Raleway', sans-serif;" >Raleway</h1>
                    </div>

                    <div class="jumbotron mb-1 col-md-6" id="Raleway">
                      <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@600&display=swap" rel="stylesheet">
                      <h1 id="600" style="margin-bottom:0px;font-weight: 600;font-family:'Raleway', sans-serif;" >Raleway Bold</h1>
                    </div>



                    <div class="jumbotron mb-1 col-md-6" id="Finlandica">
                      <link href="https://fonts.googleapis.com/css2?family=Finlandica:wght@400&display=swap" rel="stylesheet">
                      <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family:'Finlandica', sans-serif;" >Finlandica</h1>
                    </div>

                    <div class="jumbotron mb-1 col-md-6" id="Finlandica">
                      <link href="https://fonts.googleapis.com/css2?family=Finlandica:wght@600&display=swap" rel="stylesheet">
                      <h1 id="600" style="margin-bottom:0px;font-weight: 600;font-family:'Finlandica', sans-serif;" >Finlandica Bold</h1>
                    </div>



                    <div class="jumbotron mb-1 col-md-6" id="Kantumruy_Pro">
                      <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400&display=swap" rel="stylesheet">
                      <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family: 'Kantumruy Pro', serif;" >Kantumruy Pro</h1>
                    </div>

                    <div class="jumbotron mb-1 col-md-6" id="Kantumruy_Pro">
                      <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@600&display=swap" rel="stylesheet">
                      <h1 id="600" style="margin-bottom:0px;font-weight: 600;font-family: 'Kantumruy Pro', serif;" >Kantumruy Pro Bold</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="Montserrat">
                      <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400&display=swap" rel="stylesheet">
                      <h1 id="400" style="margin-bottom:0px;font-weight: 400;font-family: 'Montserrat', serif;" >Montserrat</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="Montserrat">
                      <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
                      <h1 id="600" style="margin-bottom:0px;font-weight: 600;font-family: 'Montserrat', serif;" >Montserrat Bold</h1>
                    </div>


                    <div class="jumbotron mb-1 col-md-6" id="Titillium_Web">
                      <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet">
                      <h1 id="300" style="margin-bottom:0px;font-weight: 300;font-family: 'Titillium Web', serif;" >Titillium Web</h1>
                    </div>

                    <div class="jumbotron mb-1 col-md-6" id="Titillium_Web">
                      <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@700&display=swap" rel="stylesheet">
                      <h1 id="700" style="margin-bottom:0px;font-weight: 700;font-family: 'Titillium Web', serif;" >Titillium Web Bold</h1>
                    </div>

                     </div>



               </div>


                   <div class="tab-pane fade" id="custom-tab-options_" role="tabpanel" aria-labelledby="options_">
                    
                    <label class="mt-2">Adjust Resolution</label>

                        <div class="qty__ mt-2">
                        <span class="minus bg-dark">-</span>
                        <input type="number" class="count" name="qty" value="{{Auth::user()->resolution}}">
                        <span style="font-size: 16px;font-weight: 700;position: relative;top: 3px;right: 7px;width: 8px;">%</span>
                        <span class="plus bg-dark">+</span>
                    </div>

                   </div>

                      <button type="button" id="same"  class="btn btn-primary float-right  px-4 saveChanges">Apply Changes</button>
                      <button type="button" id="all"  class="btn btn-warning float-right mr-2  px-4 saveChanges">Apply to All Users</button>


                 </div>



                  </div>

                </div>
              </div>
              <!-- /.card -->
            </div>
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
 
// $(document).ready(function() { 
//     $("#city").select2({
//       placeholder:"search here",
//       minimumInputLength:1,
//       allowClear:true,
//       matcher: function(term, text) { 
//         return term.toUpperCase().indexOf(term.toUpperCase())==0; 
//       }
//     }); 
//   });
  
    $("#city").select2();

  var data = @json($data);
          
          var business = data['business'];

          if(business.length > 0) {

              $("#country").val(business[0].country)
              $("#state").val(business[0].state)
              $("#city").val(business[0].city).trigger('change')



          }

        $( "#country" ).select2();
        $( "#state" ).select2();


$(document).ready(function () {

$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });





$("#updateBusinessInfo").click(function (event) {


        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#formBusinessInfo')[0];

        // Create an FormData object
        var data = new FormData(form);

        

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{ route('updateBusinessInfo') }}",
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




$("#updateInvoiceSetting").click(function (event) {


        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#formInvoiceSetting')[0];

        // Create an FormData object
        var data = new FormData(form);

        

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{ route('updateInvoiceSetting') }}",
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


$("#cleanDatabase").click(function (event) {


  swal({
          title: "Are you sure?",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.post("{{ route('cleanDatabase') }}",
            {
                id: '1'
            });
             
               Lobibox.notify('success', {
                      pauseDelayOnHover: true,
                      continueDelayOnInactiveTab: false,
                      position: 'top right',
                      msg: 'Database Cleaned.',
                      icon: 'bx bx-check-circle'
                  });


           } 
        });

})



$("#updateBusinessAddress").click(function (event) {


        //stop submit the form, we will post it manually.
        event.preventDefault();

        // Get form
        var form = $('#formBusinessAddress')[0];

        // Create an FormData object
        var data = new FormData(form);

        

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "{{ route('updateBusinessAddress') }}",
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


    $(document).ready(function(){
        $('.count').prop('disabled', true);
        
        $(document).on('click','.plus',function(){

          if($('.count').val() == 150 || $('.count').val() > 150 ) {

                Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: 'Maximum resolution limit is 150%',
                                icon: 'bx bx-check-circle'
                            });  
                return false;
            }


        $('.count').val(parseInt($('.count').val()) + 5 );

         var resolution = parseInt($('.count').val());
          document.body.style.zoom = resolution+"%";
          $('#resolution').val(resolution)
          setPageHeight();

        });

          $(document).on('click','.minus',function(){

            if($('.count').val() == 70 || $('.count').val() < 70 ) {

                Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: 'Minimum resolution limit is 70%',
                                icon: 'bx bx-check-circle'
                            });  
                return false;
            }

          $('.count').val(parseInt($('.count').val()) - 5 );

             var resolution = parseInt($('.count').val());
              document.body.style.zoom = resolution+"%";
              $('#resolution').val(resolution)
              setPageHeight();

         });


    });





$(".fontTab > .row > .jumbotron").click(function (event) {

      var font = $(this).attr('id'); 
          font = font.replace('_', " ");
      var font_link = $(this).find('link').attr('href');
      var font_weight = $(this).find('h1').attr('id');




     $('#currentLink').attr('href',font_link);
     $('body').attr('style', 'font-weight: '+font_weight+' !important;font-family: '+font+', sans-serif');
      $("body").get(0).style.setProperty("--main-color", $('#colorscheme').val());

     $('#font').val(font); 
     $('#font_link').val(font_link);  
     $('#font_weight').val(font_weight);  

      document.body.style.zoom = $('#resolution').val()+'%';
      setPageHeight();
      
 })



$(".saveChanges").click(function (event) {


         swal({
                  title: "Are you sure you want to apply changes?",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
                })
                .then((willDelete) => {
                  if (willDelete) {

                        //stop submit the form, we will post it manually.
                        event.preventDefault();

                        // Get form
                        var form = $('#themeForm')[0];

                        // Create an FormData object
                        var data = new FormData(form);
                            data.append("limit", $(this).attr('id'));

                        

                        $.ajax({
                            type: "POST",
                            enctype: 'multipart/form-data',
                            url: "{{ route('updateThemeInfo') }}",
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

                  }
                })

        

         
 })


</script>


<script src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.0/knockout-min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.10/clipboard.min.js'></script>
<script >var copiedHex = ko.observable();
var clipboard = new Clipboard('#clipboardItem');

  clipboard.on('success', function(el) {
   // console.clear();
   // console.info('Action:', el.action);
   // console.info('Text:', el.text);
   // console.info('Trigger:', el.trigger);
   // el.clearSelection();
   // copiedHex(el.text);

    $("body").get(0).style.setProperty("--main-color", el.text);
    
    $('#colorscheme').val(el.text); 



    //alert(el.text)
});

///

var selectedColor = ko.observable("Red"); // lazy

ko.applyBindings({
    materialColors: [
        {
            color: "Red",
            variations: [
                {
                    weight: 50,
                    hex: "#FFEBEE"
                },
                {
                    weight: 100,
                    hex: "#FFCDD2"
                },
                {
                    weight: 200,
                    hex: "#EF9A9A"
                },
                {
                    weight: 300,
                    hex: "#E57373"
                },
                {
                    weight: 400,
                    hex: "#EF5350"
                },
                {
                    weight: 500,
                    hex: "#F44336"
                },
                {
                    weight: 600,
                    hex: "#E53935"
                },
                {
                    weight: 700,
                    hex: "#D32F2F"
                },
                {
                    weight: 800,
                    hex: "#C62828"
                },
                {
                    weight: 900,
                    hex: "#B71C1C"
                }
            ]
        },
        {
            color: "Pink",
            variations: [
                {
                    weight: 50,
                    hex: "#FCE4EC"
                },
                {
                    weight: 100,
                    hex: "#F8BBD0"
                },
                {
                    weight: 200,
                    hex: "#F48FB1"
                },
                {
                    weight: 300,
                    hex: "#F06292"
                },
                {
                    weight: 400,
                    hex: "#EC407A"
                },
                {
                    weight: 500,
                    hex: "#E91E63"
                },
                {
                    weight: 600,
                    hex: "#D81B60"
                },
                {
                    weight: 700,
                    hex: "#C2185B"
                },
                {
                    weight: 800,
                    hex: "#AD1457"
                },
                {
                    weight: 900,
                    hex: "#880E4F"
                }
            ]
        },
        {
            color: "Purple",
            variations: [
                {
                    weight: 50,
                    hex: "#F3E5F5"
                },
                {
                    weight: 100,
                    hex: "#E1BEE7"
                },
                {
                    weight: 200,
                    hex: "#CE93D8"
                },
                {
                    weight: 300,
                    hex: "#BA68C8"
                },
                {
                    weight: 400,
                    hex: "#AB47BC"
                },
                {
                    weight: 500,
                    hex: "#9C27B0"
                },
                {
                    weight: 600,
                    hex: "#8E24AA"
                },
                {
                    weight: 700,
                    hex: "#7B1FA2"
                },
                {
                    weight: 800,
                    hex: "#6A1B9A"
                },
                {
                    weight: 900,
                    hex: "#4A148C"
                }
            ]
        },
        {
            color: "Deep Purple",
            variations: [
                {
                    weight: 50,
                    hex: "#EDE7F6"
                },
                {
                    weight: 100,
                    hex: "#D1C4E9"
                },
                {
                    weight: 200,
                    hex: "#B39DDB"
                },
                {
                    weight: 300,
                    hex: "#9575CD"
                },
                {
                    weight: 400,
                    hex: "#7E57C2"
                },
                {
                    weight: 500,
                    hex: "#673AB7"
                },
                {
                    weight: 600,
                    hex: "#5E35B1"
                },
                {
                    weight: 700,
                    hex: "#512DA8"
                },
                {
                    weight: 800,
                    hex: "#4527A0"
                },
                {
                    weight: 900,
                    hex: "#311B92"
                }
            ]
        },
        {
            color: "Indigo",
            variations: [
                {
                    weight: 50,
                    hex: "#E8EAF6"
                },
                {
                    weight: 100,
                    hex: "#C5CAE9"
                },
                {
                    weight: 200,
                    hex: "#9FA8DA"
                },
                {
                    weight: 300,
                    hex: "#7986CB"
                },
                {
                    weight: 400,
                    hex: "#5C6BC0"
                },
                {
                    weight: 500,
                    hex: "#3F51B5"
                },
                {
                    weight: 600,
                    hex: "#3949AB"
                },
                {
                    weight: 700,
                    hex: "#303F9F"
                },
                {
                    weight: 800,
                    hex: "#283593"
                },
                {
                    weight: 900,
                    hex: "#1A237E"
                }
            ]
        },
        {
            color: "Blue",
            variations: [
                {
                    weight: 50,
                    hex: "#E3F2FD"
                },
                {
                    weight: 100,
                    hex: "#BBDEFB"
                },
                {
                    weight: 200,
                    hex: "#90CAF9"
                },
                {
                    weight: 300,
                    hex: "#64B5F6"
                },
                {
                    weight: 400,
                    hex: "#42A5F5"
                },
                {
                    weight: 500,
                    hex: "#2196F3"
                },
                {
                    weight: 600,
                    hex: "#1E88E5"
                },
                {
                    weight: 700,
                    hex: "#1976D2"
                },
                {
                    weight: 800,
                    hex: "#1565C0"
                },
                {
                    weight: 900,
                    hex: "#0D47A1"
                }
            ]
        },
        {
            color: "Light Blue",
            variations: [
                {
                    weight: 50,
                    hex: "#E1F5FE"
                },
                {
                    weight: 100,
                    hex: "#B3E5FC"
                },
                {
                    weight: 200,
                    hex: "#81D4FA"
                },
                {
                    weight: 300,
                    hex: "#4FC3F7"
                },
                {
                    weight: 400,
                    hex: "#29B6F6"
                },
                {
                    weight: 500,
                    hex: "#03A9F4"
                },
                {
                    weight: 600,
                    hex: "#039BE5"
                },
                {
                    weight: 700,
                    hex: "#0288D1"
                },
                {
                    weight: 800,
                    hex: "#0277BD"
                },
                {
                    weight: 900,
                    hex: "#01579B"
                }
            ]
        },
        {
            color: "Cyan",
            variations: [
                {
                    weight: 50,
                    hex: "#E0F7FA"
                },
                {
                    weight: 100,
                    hex: "#B2EBF2"
                },
                {
                    weight: 200,
                    hex: "#80DEEA"
                },
                {
                    weight: 300,
                    hex: "#4DD0E1"
                },
                {
                    weight: 400,
                    hex: "#26C6DA"
                },
                {
                    weight: 500,
                    hex: "#00BCD4"
                },
                {
                    weight: 600,
                    hex: "#00ACC1"
                },
                {
                    weight: 700,
                    hex: "#0097A7"
                },
                {
                    weight: 800,
                    hex: "#00838F"
                },
                {
                    weight: 900,
                    hex: "#006064"
                }
            ]
        },
        {
            color: "Teal",
            variations: [
                {
                    weight: 50,
                    hex: "#E0F2F1"
                },
                {
                    weight: 100,
                    hex: "#B2DFDB"
                },
                {
                    weight: 200,
                    hex: "#80CBC4"
                },
                {
                    weight: 300,
                    hex: "#4DB6AC"
                },
                {
                    weight: 400,
                    hex: "#26A69A"
                },
                {
                    weight: 500,
                    hex: "#009688"
                },
                {
                    weight: 600,
                    hex: "#00897B"
                },
                {
                    weight: 700,
                    hex: "#00796B"
                },
                {
                    weight: 800,
                    hex: "#00695C"
                },
                {
                    weight: 900,
                    hex: "#004D40"
                }
            ]
        },
        {
            color: "Green",
            variations: [
                {
                    weight: 50,
                    hex: "#E8F5E9"
                },
                {
                    weight: 100,
                    hex: "#C8E6C9"
                },
                {
                    weight: 200,
                    hex: "#A5D6A7"
                },
                {
                    weight: 300,
                    hex: "#81C784"
                },
                {
                    weight: 400,
                    hex: "#66BB6A"
                },
                {
                    weight: 500,
                    hex: "#4CAF50"
                },
                {
                    weight: 600,
                    hex: "#43A047"
                },
                {
                    weight: 700,
                    hex: "#388E3C"
                },
                {
                    weight: 800,
                    hex: "#2E7D32"
                },
                {
                    weight: 900,
                    hex: "#1B5E20"
                }
            ]
        },
        {
            color: "Light Green",
            variations: [
                {
                    weight: 50,
                    hex: "#F1F8E9"
                },
                {
                    weight: 100,
                    hex: "#DCEDC8"
                },
                {
                    weight: 200,
                    hex: "#C5E1A5"
                },
                {
                    weight: 300,
                    hex: "#AED581"
                },
                {
                    weight: 400,
                    hex: "#9CCC65"
                },
                {
                    weight: 500,
                    hex: "#8BC34A"
                },
                {
                    weight: 600,
                    hex: "#7CB342"
                },
                {
                    weight: 700,
                    hex: "#689F38"
                },
                {
                    weight: 800,
                    hex: "#558B2F"
                },
                {
                    weight: 900,
                    hex: "#33691E"
                }
            ]
        },
        {
            color: "Lime",
            variations: [
                {
                    weight: 50,
                    hex: "#F9FBE7"
                },
                {
                    weight: 100,
                    hex: "#F0F4C3"
                },
                {
                    weight: 200,
                    hex: "#E6EE9C"
                },
                {
                    weight: 300,
                    hex: "#DCE775"
                },
                {
                    weight: 400,
                    hex: "#D4E157"
                },
                {
                    weight: 500,
                    hex: "#CDDC39"
                },
                {
                    weight: 600,
                    hex: "#C0CA33"
                },
                {
                    weight: 700,
                    hex: "#AFB42B"
                },
                {
                    weight: 800,
                    hex: "#9E9D24"
                },
                {
                    weight: 900,
                    hex: "#827717"
                }
            ]
        },
        {
            color: "Yellow",
            variations: [
                {
                    weight: 50,
                    hex: "#FFFDE7"
                },
                {
                    weight: 100,
                    hex: "#FFF9C4"
                },
                {
                    weight: 200,
                    hex: "#FFF59D"
                },
                {
                    weight: 300,
                    hex: "#FFF176"
                },
                {
                    weight: 400,
                    hex: "#FFEE58"
                },
                {
                    weight: 500,
                    hex: "#FFEB3B"
                },
                {
                    weight: 600,
                    hex: "#FDD835"
                },
                {
                    weight: 700,
                    hex: "#FBC02D"
                },
                {
                    weight: 800,
                    hex: "#F9A825"
                },
                {
                    weight: 900,
                    hex: "#F57F17"
                }
            ]
        },
        {
            color: "Amber",
            variations: [
                {
                    weight: 50,
                    hex: "#FFF8E1"
                },
                {
                    weight: 100,
                    hex: "#FFECB3"
                },
                {
                    weight: 200,
                    hex: "#FFE082"
                },
                {
                    weight: 300,
                    hex: "#FFD54F"
                },
                {
                    weight: 400,
                    hex: "#FFCA28"
                },
                {
                    weight: 500,
                    hex: "#FFC107"
                },
                {
                    weight: 600,
                    hex: "#FFB300"
                },
                {
                    weight: 700,
                    hex: "#FFA000"
                },
                {
                    weight: 800,
                    hex: "#FF8F00"
                },
                {
                    weight: 900,
                    hex: "#FF6F00"
                }
            ]
        },
        {
            color: "Orange",
            variations: [
                {
                    weight: 50,
                    hex: "#FFF3E0"
                },
                {
                    weight: 100,
                    hex: "#FFE0B2"
                },
                {
                    weight: 200,
                    hex: "#FFCC80"
                },
                {
                    weight: 300,
                    hex: "#FFB74D"
                },
                {
                    weight: 400,
                    hex: "#FFA726"
                },
                {
                    weight: 500,
                    hex: "#FF9800"
                },
                {
                    weight: 600,
                    hex: "#FB8C00"
                },
                {
                    weight: 700,
                    hex: "#F57C00"
                },
                {
                    weight: 800,
                    hex: "#EF6C00"
                },
                {
                    weight: 900,
                    hex: "#E65100"
                }
            ]
        },
        {
            color: "Deep Orange",
            variations: [
                {
                    weight: 50,
                    hex: "#FBE9E7"
                },
                {
                    weight: 100,
                    hex: "#FFCCBC"
                },
                {
                    weight: 200,
                    hex: "#FFAB91"
                },
                {
                    weight: 300,
                    hex: "#FF8A65"
                },
                {
                    weight: 400,
                    hex: "#FF7043"
                },
                {
                    weight: 500,
                    hex: "#FF5722"
                },
                {
                    weight: 600,
                    hex: "#F4511E"
                },
                {
                    weight: 700,
                    hex: "#E64A19"
                },
                {
                    weight: 800,
                    hex: "#D84315"
                },
                {
                    weight: 900,
                    hex: "#BF360C"
                }
            ]
        },
        {
            color: "Brown",
            variations: [
                {
                    weight: 50,
                    hex: "#EFEBE9"
                },
                {
                    weight: 100,
                    hex: "#D7CCC8"
                },
                {
                    weight: 200,
                    hex: "#BCAAA4"
                },
                {
                    weight: 300,
                    hex: "#A1887F"
                },
                {
                    weight: 400,
                    hex: "#8D6E63"
                },
                {
                    weight: 500,
                    hex: "#795548"
                },
                {
                    weight: 600,
                    hex: "#6D4C41"
                },
                {
                    weight: 700,
                    hex: "#5D4037"
                },
                {
                    weight: 800,
                    hex: "#4E342E"
                },
                {
                    weight: 900,
                    hex: "#3E2723"
                }
            ]
        },
        {
            color: "Grey",
            variations: [
                {
                    weight: 50,
                    hex: "#FAFAFA"
                },
                {
                    weight: 100,
                    hex: "#F5F5F5"
                },
                {
                    weight: 200,
                    hex: "#EEEEEE"
                },
                {
                    weight: 300,
                    hex: "#E0E0E0"
                },
                {
                    weight: 400,
                    hex: "#BDBDBD"
                },
                {
                    weight: 500,
                    hex: "#9E9E9E"
                },
                {
                    weight: 600,
                    hex: "#757575"
                },
                {
                    weight: 700,
                    hex: "#616161"
                },
                {
                    weight: 800,
                    hex: "#424242"
                },
                {
                    weight: 900,
                    hex: "#212121"
                }
            ]
        },
        {
            color: "Blue Grey",
            variations: [
                {
                    weight: 50,
                    hex: "#ECEFF1"
                },
                {
                    weight: 100,
                    hex: "#CFD8DC"
                },
                {
                    weight: 200,
                    hex: "#B0BEC5"
                },
                {
                    weight: 300,
                    hex: "#90A4AE"
                },
                {
                    weight: 400,
                    hex: "#78909C"
                },
                {
                    weight: 500,
                    hex: "#607D8B"
                },
                {
                    weight: 600,
                    hex: "#546E7A"
                },
                {
                    weight: 700,
                    hex: "#455A64"
                },
                {
                    weight: 800,
                    hex: "#37474F"
                },
                {
                    weight: 900,
                    hex: "#263238"
                }
            ]
        }
    ]
});
//# sourceURL=pen.js
</script>
@endpush