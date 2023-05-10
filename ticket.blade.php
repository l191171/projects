@include('layouts.header')
  
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Ticket Info
               <a class="btn btn-info btn-sm" onclick=history.back()><i class="fas fa-arrow-left"></i> Go Back </a>
             </h1>
          </div><!-- /.col -->
          <div class="col-sm-6  d-none d-sm-none d-md-block ">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('/')}}">Home</a></li>
              <li class="breadcrumb-item active">Ticket Info</li>
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
                            <div class="card-body row">  



               <input type="hidden" name="id" id="id">
               <input type="hidden" name="tid" id="tid" value="<?=uniqid();?>">

                
                <div class="form-group col-md-6">
                    <label for="subject">Subject <span>*</span></label>
                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
           
                    <span class="text-danger error-text subject_error"></span>
                    
                </div>
             
   
                  <div class="col-md-3">
                    <label class="form-label">Department <span>*</span></label>
                    <select class="form-select form-control" name="department" id="department" >
                     
                    <option value="">Choose an Option</option>
                      
                      <option >Technical Department</option>
                    </select>
                    
                    <span class="text-danger error-text department_error"></span>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Priority <span>*</span></label>
                    <select class="form-select form-control" name="priority" id="priority">
                      <option value="">Choose an option</option>
                      <option>Low</option>
                      <option>Medium</option>
                      <option>High</option>
                      <option>Critical</option>
                    </select>
                    
                    <span class="text-danger error-text priority_error"></span>
                  </div>



                <div class="form-group col-md-6 patientInfo">
                <label class="form-label">Ticket MRN</label>

                    <input type="text" class="form-control"  id="patientname" name="patientname">
                </div>


                  <div class="form-group col-md-3 requestInfo">
                   <label for="requestid">Contact Number <span>*</span></label>
                    <input type="text" class="form-control f-one" id="contact" name="contact" placeholder="Contact No" required>
            
                </div>

                <!-- Request ID and Sample ID -->
            
         

                <!-- Message Area -->
                <div class="form-outline my-2 col-md-12">
                    <label class="form-label" for="textAreaExample2">Message <span>*</span></label>
                    <textarea class="form-control" rows="8" name="message" id="message" required></textarea>
                <span class="text-danger error-text message_error"></span>
                  </div>
                
                 <div class="form-outline my-2 col-md-12">
                <label  class="col-form-label">Attach Files </label>    
                  <input id="files" type="file" name="files[]" multiple="">
                </div>


             
         
              
                <div class="col-md-12">
                  <button type="submit" class="btn btn-primary float-right saveupdatebtn" value="Submit">Generate Ticket</button>
                  <div id="result"></div>
                </div>

              

                            </div>
                          </div>

                  </form>      
      

  

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>



@extends('layouts.footer')

@push('script')

<!-- fancyfileuploader -->
<script src="{{ asset('plugins/fancy-file-uploader/jquery.ui.widget.js') }}"></script>
<script src="{{ asset('plugins/fancy-file-uploader/jquery.fileupload.js') }}"></script>
<script src="{{ asset('plugins/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
<script src="{{ asset('plugins/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>

<script type="text/javascript">
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function(){
      

        var data = @json($data);

        console.log(data)

        if(data.ticketinfo != ''){


             $('#patientname').val(data.ticketinfo.patientname)
             $('#requestid').val(data.ticketinfo.requestid)
             $('#sampleid').val(data.ticketinfo.sampleid)
             $('#subject').val(data.ticketinfo.subject)
             $('#department').val(data.ticketinfo.department).trigger('change');
             $('#priority').val(data.ticketinfo.priority).trigger('change');
             $('#message').val(data.ticketinfo.message);
             $('#myid').val(data.ticketinfo.id);
             $('.saveupdatebtn').text('Update Ticket Info');

 $('.saveupdatebtn').click(function(){
     let formmy=document.getElementById("form");
     let formd=new FormData(formmy);
     $.ajax({
            
            url: "{{route('updateTicket')}}",
            data: formd,    
            cache: false,
            processData: false,
            contentType: false,
            type: 'post',
           
            }).done(function (response) {
                          
                          

                            $("#result").html('Ticket has been Updated successfully!')

                            window.location="http://localhost:8080/tickets/Tickets";

                            
                           
                    }).fail(function(response){

                      Lobibox.notify('warning', {
                                pauseDelayOnHover: true,
                                continueDelayOnInactiveTab: false,
                                position: 'top right',
                                msg: response.error,
                                icon: 'bx bx-info-circle'           
                      });
                    }) ;
            event.preventDefault();
          

    }); 


   


       } else {

              $("button").click(function(){
               var messages= $('#message').val();

               $("button").attr("disabled", true);
            let myform=document.getElementById("form");
              let data=new FormData(myform);
              data.append('messages',messages);
              $.ajax({
                              
                      url: "Ticket",
                      data: data,    
                      cache: false,
                      processData: false,
                      contentType: false,
                      type: 'POST',
                      }).done(function (response) {
                        console.log(response);
                                      if(response[1] == 'true') {
                                        $("#result").html('Ticket has been generated successfully!')
        
        //  window.location.reload();
          window.location="Tickets/Opened/"+response[2];

                                      
                                      }
                                    //  elseif(response[1] == 0)
                                    //   console.log("t");
                                     
                                      
                                      
                                     
                              })
                              .fail(function(response){
                                $("button").removeAttr("disabled");
console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          msg: Object.values(response.responseJSON.errors)[0],
          icon: 'bx bx-info-circle'           
});




}) ;



        
                              event.preventDefault();


                            
                });
              }



    // $('#patientname').select2({
    //     placeholder:'Choose a Patient'
    // });



           $('#files').FancyFileUpload({

                url : "uploadFiles",
                maxfilesize: 100000000,
                params: {
                    tid:$('#tid').val()
                },
                added : function(e, data) {
                this.find('.ff_fileupload_actions button.ff_fileupload_start_upload').click();
                },
                uploadcompleted : function(e, data) {
                    

                }
            });



    });
</script>
@endpush