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
                                            <label  class="col-form-label">Code <span>*</span></label>
                                                 <input type="text" class="form-control" id="code" name="name" value="" />
                                             </div>

                                             <div class="col-md-3 form-group ml-3">
                                            <label  class="col-form-label">Name<span>*</span></label>
                                                 <input type="text" class="form-control" id="name" name="name" value="" />
                                             </div> 
                                             
                                             
                                                             </div>
                                                             <div class="col-md-3 form-group" style="">
                                                 <input type="button" class="btn btn-primary" id="submit" value="Add Role" />
                                             </div> 
                                       

                                      </form>
</div>
</div>

</div>
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


            $('#submit').on('click',function(){

                
            var code=$('#code').val();
            var name=$("#name").val();
            
            if(code==""){
                Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg:"Code field is required",
                                icon: 'bx bx-info-circle',
                            
                            });
return false;
            }
            if(name==""){
                Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg:"Name Field is Required",
                                icon: 'bx bx-info-circle',
                            
                            });
return false;
            }
$.ajax({
url:"{{ route('addRole') }}",
method:'POST',
data:{
    code:code,
    name:name
}


}).done(function(response){
if(response!=""){
    Lobibox.notify('success', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: "Role Added Successfully",
                                icon: 'bx bx-check-circle',
             
            
                            });
                        }

})
.fail(function(response){
    Lobibox.notify('Warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: "Role could not be Added ",
                                icon: 'bx bx-info-circle',
                  
            
                            });
                        

});
            
            });
      
     })   
</script>
@endpush