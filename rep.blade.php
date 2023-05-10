@include('layouts.header')

    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Tickets
               <a class="btn btn-info btn-sm" href="{{(request()->segment(2))}}"><i class="fas fa-sync"></i></a>
               
               <a class="btn btn-info btn-sm" href="{{route('Ticket')}}"><i class="fas fa-plus"></i> Ticket </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">Tickets</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


     <!-- Main content -->
    <div class="content">
    <form action="" method="post" id='form'>
    <input type="date" name='todate'>
    <input type="date" name="tildate">
    <input type="text" name="status">
  
    <input type="submit" id ="submit">
  </form>
      <div class="container-fluid">

      
                         <div class="card card-primary card-outline">
                            <div class="card-body table-responsive"> 
             <table id="table"  class="table mb-0 table-striped table">
                                 
             <thead>
             
             <tr>
             
              <th>ID</th>
              <th>Client</th>
              <th>Ticket#</th>
              <th>Subject</th>
              <th>closed</th>


              
              </tr>


                 </thead>

              
               </table>
            




                 <!-- Modal -->
                <div class="modal fade" id="selectUsers" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md  modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header bg-primary">
                                <h5 class="modal-title text-white">Assign Ticket to a User <span id="requestText2"></span></h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                                      
                                 

                              <div class="col-md-12">
                                
                                    <input type="hidden" id="tid"> 


                                  </div>

                                <button type="button" class="mt-2 btn btn-primary assignTicketNowBtn float-right">Assign Now</button>
                                        

                            </div>     

                           
                           
                        </div>
                    </div>
                </div> 




        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
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
   
$(document).ready(function() {
 
   
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
   
  
 
    $("#submit").click(function(){
      
      // $('.paused').addClass('d-none');
      
      // $('.paused').removeClass('d-block');
      // $('.started').addClass('d-block');
   
      let myform=document.getElementById("form");
let data=new FormData(myform);
$.ajax({
                
        url: "../reporte",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        }).done(function (response) {

                        if(response > 0) {

                            $("#result").html('Ticket has been completed successfully!')

                        //  window.location="../TicketView/"+response;

                        }
                   
                       
                });
                event.preventDefault();


  });


 
   });


   
 
 



                  

    </script>
@endpush