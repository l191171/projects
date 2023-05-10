@include('layouts.header')
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Version </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">User Roles Mapping </li>
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

                             <div class="card card-primary card-outline">
                            <div class="card-body table-responsive">

                      
                           <div class="row">
                                    
                             <div class="col-md-12">  
                    
                                 
                                            {{ csrf_field() }}
                                            <div class="row">
                                            
                                        
                                        <div class="col-md-4">
                                           <input type="hidden" class="form-control" id="id" name="id" value="<?php  if (count($fetch)> 0)  {echo $fetch[0]->id; }?>" />
                                                 <select class="form-control" name="modules" id="role">
                                                <option disabled selected hidden ></option>
                                         
                                                        @foreach ($modules as $mod)
                                                        <option value="{{$mod->modulename}}">{{$mod->modulename}}</option>
                                                        @endforeach
                                                       
                                                 </select>
                                             </div> 
                                             <div class="col-md-4"> 
                                             <input type="text" name="vnumber" id="vnumber" class="form-control" placeholder="#version" value="<?php echo $verno?>">   
                                           </div>
                                           </div> 
                                           <div class="row pt-3"> 
                                           <div class="col-md-12">
                                           <textarea type="text" name="description" id="description" class="form-control" rows="8" placeholder="description"><?php if (count($fetch)> 0)  {echo $fetch[0]->description;
                                              
                                           }?></textarea>    
                                           </div>    
                                           </div>
                                             <?php if (count($fetch)> 0)  {
                                              
                                           ?>
                                            <button class="btn btn-warning mt-2 float-right " type='button' id="button2">UPDATE</button> 
                                           <?php

                                       }else{


                                           ?>
                                            <button class="btn btn-success mt-2 float-right " type='button' id="button1">SAVE</button> 
                                           <?php
                                           } 

                                           ?>
                                            
                                    

                                 </div>

                             </div>
                             
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

<script>
      $(document).ready(function(){
          $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

$( "#role" ).select2({
                placeholder:'Choose Role',
                allowClear:true
               });
  $("#button1").click(function(){
  // alert();
  let form=document.getElementById("form");

  let data=new FormData(form);



  // mod = $('#modules').find(":selected").text();
  mod = $("#role").val();
  vnumber = $("#vnumber").val();
  desc = $("#description").val();
$.ajax({
   url:"{{route('versionsins')}}",
   data:{
    module:mod,
    vnumber: vnumber,
    description: desc
   },
 
   type:'post'

  })
.done(function(response){
               if ($.isEmptyObject(response.error)){
                                         Lobibox.notify('success', {
                                                pauseDelayOnHover: true,
                                                continueDelayOnInactiveTab: false,
                                                position: 'top right',
                                                msg: response.success,
                                                icon: 'bx bx-check-circle'
                                            });

                                         window.location = '{{route("versions")}}';
 
                                    } else {

                                         Lobibox.notify('warning', {
                                                pauseDelayOnHover: true,
                                                continueDelayOnInactiveTab: false,
                                                position: 'top right',
                                                msg: response.error,
                                                icon: 'bx bx-info-circle'
                                            });
                                 
                                    }
  // window.location='';
 console.log(response);
  })



  event.preventDefault();
  });
    $("#button2").click(function(){
  // alert();
  let form=document.getElementById("form");

  let data=new FormData(form);



  // mod = $('#modules').find(":selected").text();
  id = $("#id").val();
  mod = $("#role").val();
  vnumber = $("#vnumber").val();
  desc = $("#description").val();
$.ajax({
   url:"{{route('updateversion')}}",
   data:{
    id:id,
    module:mod,
    vnumber: vnumber,
    description: desc
   },
 
   type:'post'

  })
.done(function(response){
               if ($.isEmptyObject(response.error)){
                                         Lobibox.notify('success', {
                                                pauseDelayOnHover: true,
                                                continueDelayOnInactiveTab: false,
                                                position: 'top right',
                                                msg: response.success,
                                                icon: 'bx bx-check-circle'
                                            });

                                        window.location = '{{route("versions")}}';
 
                                    } else {

                                         Lobibox.notify('warning', {
                                                pauseDelayOnHover: true,
                                                continueDelayOnInactiveTab: false,
                                                position: 'top right',
                                                msg: response.error,
                                                icon: 'bx bx-info-circle'
                                            });
                                 
                                    }
  // window.location='';
 console.log(response);
  })



  event.preventDefault();
  });
fetch=@json($fetch);
// console.log(fetch.length);
if(fetch.length > 0){
modules=fetch[0]['modules'];

$("#role").select2("val", modules);

}

  }); 
</script>

@endpush
