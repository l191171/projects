@include('layouts.header')
  <style>
    *{
    margin: 0;
    padding: 0;
}
.rate {
    /* width: 37.9%; */
    height: 46px;
    padding: 0 10px;
}
.rate:not(:checked) > input {
    position:absolute;
    top:-9999px;
}
.rate:not(:checked) > label {
    float:right;
    width:1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:30px;
    color:#ccc;
}
.rate:not(:checked) > label:before {
    content: '★ ';
}
.rate > input:checked ~ label {
    color: #ffc700;    
}
.rate:not(:checked) > label:hover,
.rate:not(:checked) > label:hover ~ label {
    color: #deb217;  
}
.rate > input:checked + label:hover,
.rate > input:checked + label:hover ~ label,
.rate > input:checked ~ label:hover,
.rate > input:checked ~ label:hover ~ label,
.rate > label:hover ~ input:checked ~ label {
    color: #c59b08;
}

/* Modified from: https://github.com/mukulkant/Star-rating-using-pure-css */
  </style>
    <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

      <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-0">
          <div class="col-sm-6">
            <h1 class="m-0">Ticket View
               <a class="btn btn-info btn-sm" onclick=history.back()><i class="fas fa-arrow-left"></i> Go Back </a>
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
      <div class="container-fluid">



                         <div class="card card-primary card-outline pb-0">
                            <div class="card-body row">  
                              <div class="col-md-12">
                                <h3 class="mb-3" >
                                  Ticket #{{$data22['ticketinfo'][0]->ticketid}} by {{$data22['ticketinfo'][0]->username}}
                                  

                                  <?php

                                    if($data22['ticketinfo'][0]->priority == 'Low') {

                                        ?>
                                        <button class="btn btn-info float-right">{{$data22['ticketinfo'][0]->priority}}</button>
                                        <?php

                                    }
                                   elseif($data22['ticketinfo'][0]->priority == 'Medium') {

                                        ?>
                                        <button class="btn btn-primary float-right">{{$data22['ticketinfo'][0]->priority}}</button>
                                        <?php

                                    } 
                                    elseif($data22['ticketinfo'][0]->priority == 'High') {

                                        ?>
                                        <button class="btn btn-warning float-right">{{$data22['ticketinfo'][0]->priority}}</button>
                                        <?php

                                    } 
                                    elseif($data22['ticketinfo'][0]->priority == 'Critical') {

                                        ?>
                                        <button class="btn btn-danger float-right">{{$data22['ticketinfo'][0]->priority}}</button>
                                        <?php

                                    } 

                                   ?>





                               
                                <button class="btn btn-secondary float-right mr-1">{{$data22['ticketinfo'][0]->department}}</button>

                                <?php

                                    if($data22['ticketinfo'][0]->status == 'Opened') {

                                        ?>
                                        <button class="btn btn-primary float-right mr-1">{{$data22['ticketinfo'][0]->status}}</button>
                                        <?php

                                    }
                                   elseif($data22['ticketinfo'][0]->status == 'Processing') {

                                        ?>
                                        <button class="btn btn-warning float-right mr-1">{{$data22['ticketinfo'][0]->status}}</button>
                                        <?php

                                    } 
                                    elseif($data22['ticketinfo'][0]->status == 'Closed') {

                                        ?>
                                        <button class="btn btn-success float-right mr-1">{{$data22['ticketinfo'][0]->status}}</button>
                                        <?php

                                    } 
                                    elseif($data22['ticketinfo'][0]->status == 'Completed') {

                                      ?>
                                      <button class="btn btn-info float-right mr-1">{{$data22['ticketinfo'][0]->status}}</button>
                                      <?php

                                  } 
                                  
                                 

                                   ?>
                                  
                                         <button class="btn btn-info float-right mr-1">{{$data22['contact'][0]}}</button>                                 



                                
                              </h3>
                                <p>Subject : <b> 
                                  {{$data22['ticketinfo'][0]->subject}}
                                  @if($data22['ticketinfo'][0]->patientname != '')
                                  | Patient - <span id="Patient">{{$data22['ticketinfo'][0]->patientname}}</span>
                                  @endif
                                  @if($data22['ticketinfo'][0]->requestid != '')
                                  | RequestID {{$data22['ticketinfo'][0]->requestid}}
                                  @endif
                                  @if($data22['ticketinfo'][0]->sampleid != '')
                                  | SampleID {{$data22['ticketinfo'][0]->sampleid}}
                                  @endif
                                   </b></p>
                                
                                <p class="m-0">Message <button class="btn btn-dark float-right mr-1">{{$data22['ticketinfo'][0]->created_at}}</button> </p>  
                                <div class="jumbotron py-2 px-2 mb-0">
                                  {{$data22['ticketinfo'][0]->message}}
                                </div>


                                 <?php 
                                 
                                  if(count(json_decode($data22['ticketattachments'])) > 0) { ?>


                                  <div class="col-xl-12 mx-auto">
                                  <label  class="col-form-label">Attached Files</label> 
                                  <div class="row">
                                  @foreach($data22['ticketattachments'] as $ticketattachment)
                                  
                                    <div class="col-md-3">
                                    @if(

pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'webp' || 
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'jpg' || 
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'jpeg' || 
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'png' || 
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'gif' 

)
                                      <div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 100px;overflow: hidden;">
                                       <a target="_blank" href="../images/{{$ticketattachment->filename}}"><img class="w-100" src="../images/{{$ticketattachment->filename}}"></a>
                                     
                                       
                                       @elseif(

pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'mov'||
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'mp4'||
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'mpg'||
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'webj'||
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'webm'||
pathinfo($ticketattachment->filename, PATHINFO_EXTENSION) == 'flv'

)
<div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 15rem;overflow:hidden ;width:30rem">

<video style="height:15rem;width:30rem;" controls>
      <source src="../images/{{$ticketattachment->filename}}" type="video/mp4">
 
</video>

                                      @else
                                      <div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 100px;overflow: hidden;">
                                       <a target="_blank" href="../images/{{$ticketattachment->filename}}">{{$ticketattachment->filename}}</a>
                                       @endif

                                      </div>
                                      
                                    </div>

                                  @endforeach
                                  </div>
                                  </div>

                                <?php } ?>

                              </div>
                            </div>
                          </div>




                        @foreach($data22['ticketmessages'] as $ticketmessage)
                         <div class="card card-primary card-outline">
                            <div class="card-body row">  
                              <div class="col-md-12">
                                <h3 class="mb-3">Reply from {{$ticketmessage->username}}
                                </h3>
                             
                                  
                               <p class="m-0">Message <button class="btn btn-dark float-right mr-1">{{$ticketmessage->created_at}}</button> </p>  
                                <div class="jumbotron py-2 px-2 mb-0">
                                 {{$ticketmessage->message}}
                                </div>



                                    
                                  <?php 

                                  $attachments = \App\Http\Controllers\tickets::getTicketReplyAttachments($ticketmessage->mid);
                                 
                                  if(count(json_decode($attachments)) > 0) {

                                    $attachments = json_decode($attachments);

                                    ?>

                                  <div class="col-xl-12 mx-auto">
                                  <label  class="col-form-label">Attached Files</label> 
                                  <div class="row">
                                 
                                    <?php
                                      foreach($attachments as $attachment) {

                                          ?>
                                           <div class="col-md-3">
                                      
                                      @if(

                                      pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'webp' || 
                                      pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'jpg' || 
                                      pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'jpeg' || 
                                      pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'png' || 
                                      pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'gif' 

                                      )
                                      <div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 100px;overflow: hidden;">
                                       <a target="_blank" href="../images/{{$attachment->filename}}"><img class="w-100" src="../images/{{$attachment->filename}}"></a>
                                       @elseif(

pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'mov'||
pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'mp4'||
pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'mpg'||
pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'webm'||
pathinfo($attachment->filename, PATHINFO_EXTENSION) == 'flv'

)
<div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 15rem;overflow:hidden ;width:30rem">

<video style="height:15rem;width:30rem;" controls>
      <source src="../images/{{$attachment->filename}}" type="video/mp4">
 
</video>
                                       @else
                                       <div class="jumbotron p-0 mb-0" style="border:5px solid #dcdcdc;min-height:100px;max-height: 100px;overflow: hidden;">
                                       <a target="_blank" href="../images/{{$attachment->filename}}">{{$attachment->filename}}</a>
                                       @endif

                                      </div>
                                      
                                    </div>
                                          <?php
                                      }

                                      ?>

                                  </div>
                                  </div>
                                  <?php

                                  } 




                                  ?>  

                                

                


                              </div>
                            </div>
                          </div>
                          @endforeach


                  <form id="form">
                                       {{ csrf_field() }}
                                                  
                         <div class="card card-primary card-outline">
                            <div class="card-body row">  

                  <input type="hidden"  name="tid" id="tid">
                  <input type="hidden"  name="mid" id="mid" value="<?=uniqid();?>">

               
                <div class="form-group col-md-6">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" readonly name="subject" placeholder="Subject" required>
                </div>

   
                  <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select class="form-select form-control" name="department" readonly required id="department">
                      <option value="0">Choose an option</option>
                      <option>Technical Department</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Priority</label>
                    <select class="form-select form-control" name="priority" required id="priority">
                      <option value="">Choose an option</option>
                      <option>Low</option>
                      <option>Medium</option>
                      <option>High</option>
                      <option>Critical</option>
                    </select>
                  </div>


                   <div class="form-group col-md-6 patientInfo">
                    <label for="patientName">Patient Name</label>
                   <input type="" class="form-control f-one" name="patientname"  id="patientname_" readonly>
                     
                </div>


                  <div class="form-group col-md-3 requestInfo">
                   <label for="requestid">Request ID</label>
                    <input type="text" class="form-control f-one" readonly id="requestid" name="requestid" placeholder="Request ID" required>
            
                </div>


                <!-- Request ID and Sample ID -->
                <div class="form-group col-md-3 sampleInfo">
                   
                    <label for="sampleid">Sample ID</label>
                    <input type="text" class="form-control f-one" readonly name="sampleid" id="sampleid" placeholder="Sample ID" required>
                </div>


         

                <!-- Message Area -->
                <div class="form-outline my-2 col-md-12">
                    <label class="form-label" for="textAreaExample2">Message</label>
                    <textarea class="form-control" rows="9" name="message" id="message" placeholder="Reply" required></textarea>
                </div>


              
               <div class="col-xl-12 mx-auto">
                <label  class="col-form-label">Attach Files <span>*</span></label>    
                  <input id="files" type="file" name="files[]">
                </div>

             
         
              
                <div class="col-md-12 mt-2">


                 

                  @if(Auth::user()->role==4)
               
               <button type="button" class="btn btn-warning float-right ml-1 replyandclose mr-1" value="Submit">Reply & Close Ticket</button>
               <button type="button" class="btn btn-danger float-right sendtoocm" value="Submit">Send to IT Support</button>

                  
                  
                 
<button type="button" class="btn btn-dark float-right mr-1 ml-1 sendtonet" value="Submit">Send to Technical Support</button>
             @else
             <button type="button" class="btn btn-warning float-right ml-1 d-none replyandclose" value="Submit">Reply & Close Ticket</button>
             
               @endif

                  <button type="button" class="btn btn-success float-right ml-1 replyandcomplete" value="Submit">Reply & Complete Ticket</button>
              

                  <button type="button" class="btn btn-primary float-right saveupdatebtn" value="Submit">Generate Ticket</button>
               
                  <div id="result">
                  <img src="/images/Iphone-spinner-2.gif" alt="Loading..." id='loading-image' class='d-none'>
                  </div>
                </div>

        
                            </div>
                          </div>

                  </form>      
                  <div class="modal fade" id="selectUsers" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md  modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header bg-primary">
                          
                            
                                <h5 class="modal-title text-white">Rate The agent <span id="requestText2"></span></h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                               
                              </button>

                            </div>

                            <div class="modal-body" style="overflow: hidden"><div  style="display:flex;justify-content:center;align-item:center;flex-direction:column" >
                                     
                           
                                      <div class="rate mb-4" style="width:70%">
    <input type="radio" id="star5" name="rate" value="5"  />
    <label for="star5" title="text">5 stars</label>
    <input type="radio" id="star4" name="rate" value="4" />
    <label for="star4" title="text">4 stars</label>
    <input type="radio" id="star3" name="rate" value="3" />
    <label for="star3" title="text">3 stars</label>
    <input type="radio" id="star2" name="rate" value="2" />
    <label for="star2" title="text">2 stars</label>
    <input type="radio" id="star1" name="rate" value="1" />
    <label for="star1" title="text">1 star</label>
  </div>

  <div class="d-flex " style="justify-content:space-between; width:100% ">
    <div>
  <label for="html" class="mt-">Was the response timely?</label><br>
    </div>
  <div class='d-flex' style="justify-content:space-evenly">
  <div class="form-check">
  <input class="form-check-input" type="radio" value="1" id="flexCheckDefault" name="time">
  <label class="form-check-label mr-2" for="flexCheckDefault">
   Yes
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" value="0" id="flexCheckChecked" name="time">
  <label class="form-check-label" for="flexCheckChecked">
    No
  </label>
</div>

</div>
</div>

<div class="d-flex time" style="justify-content:space-between; width:100% ">
    <div>
  <label for="html" class="mt-">Was the response satisfactory?</label><br>
    </div>
  <div class='d-flex' style="justify-content:space-evenly">
  <div class="form-check">
  <input class="form-check-input" type="radio" value="1" id="flexCheckDefault" name="satisfy">
  <label class="form-check-label mr-2" for="flexCheckDefault">
   Yes
  </label>
</div>
<div class="form-check">
  <input class="form-check-input" type="radio" value="0" id="flexCheckChecked" name="satisfy">
  <label class="form-check-label" for="flexCheckChecked">
    No
  </label>
</div>
</div>


</div>
<div class="d-flex " style="justify-content:space-evenly; width:100% ">
   <div>
  <label for="html" class="mt-3">Additional Comments:</label><br>
    
 <textarea name="comments" id="comments" cols="25" rows="5"></textarea>
  </div>
</div>

                                </div>
                                <button type="button" class="mt-2 btn btn-primary ratenow float-right">Rate Now</button>
                                        
                                
                            </div>     

                           
                           
                        </div>
                    </div>
                </div> 
                  <!-- BASIC -->
            

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
<script></script>

<script type="text/javascript">
    
  $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $(document).ready(function(){
      

        var data = @json($data22);
        var d = @json($data22);
        // console.log(data22.internal[0])

        if(data.ticketinfo[0] != ''){


             if(data.ticketinfo[0].patientname == null) {

                  $('.patientInfo').remove();

             } else {

                $('#Patient').text(data.ticketinfo[0].Patientname);
                $('#patientname_').val(data.ticketinfo[0].patientname);
                $('#patientname').val(data.ticketinfo[0].patientname).trigger('change');
             }



            if(data.ticketinfo[0].requestid == null) {

                  $('.requestInfo').remove();

             } else {

                $('#requestid').val(data.ticketinfo[0].requestid);
             }



            
            if(data.ticketinfo[0].sampleid == null) {

                  $('.sampleInfo').remove();

             } else {

                $('#sampleid').val(data.ticketinfo[0].sampleid);
             }


  

    
            $('#tid').val(data.ticketinfo[0].ticketid)
            $('#subject').val(data.ticketinfo[0].subject)
            $('#department').val(data.ticketinfo[0].department).trigger('change');
            $('#priority').val(data.ticketinfo[0].priority).trigger('change');
            //$('#message').val(data.ticketinfo[0].message)
            $('.saveupdatebtn').text('Reply Now');



            if(data.ticketinfo[0].status == 'Closed') {

                $('#form').remove();
            }
      

 

        }



 
    $(".paused").click(function(){
      
      // $('.paused').addClass('d-none');
      
      // $('.paused').removeClass('d-block');
      // $('.started').addClass('d-block');
   
      let myform=document.getElementById("form");
let data=new FormData(myform);
$.ajax({
                
        url: "../PauseTicket",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        }).done(function (response) {

                        if(response > 0) {

                            $("#result").html('Ticket has been completed successfully!')

                         window.location="../TicketView/"+response;

                        }
                   
                       
                }).fail(function(response){

console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
pauseDelayOnHover: true,
continueDelayOnInactiveTab: false,
position: 'top right',
msg: Object.values(response.responseJSON.errors)[0],
icon: 'bx bx-info-circle'           
});
});;
                event.preventDefault();


  });
  $('#patientname').select2({
        placeholder:'Choose a Patient'
    });

    $(".started").click(function(){
      
        // $('.started').addClass('d-none');
        // $('.started').removeClass('d-block');
        // $('.paused').addClass('d-block');
        let myform=document.getElementById("form");
let data=new FormData(myform);
$.ajax({
                
        url: "../StartTicket",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        }).done(function (response) {
console.log(response);

                            $("#result").html('Ticket has been completed successfully!')

                         window.location="../TicketView/"+response;

                      
                   
                       
                }).fail(function(response){

console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
pauseDelayOnHover: true,
continueDelayOnInactiveTab: false,
position: 'top right',
msg: Object.values(response.responseJSON.errors)[0],
icon: 'bx bx-info-circle'           
});
});;
        event.preventDefault();
});
      
  

    $(".replyandcomplete").click(function(){
  //  var tid=$('#tid').val();S
  $(".replyandcomplete").attr("disabled", true);
// console.log(tid);
// return true;
      $('#loading-image').removeClass('d-none');
let myform=document.getElementById("form");
let data=new FormData(myform);
var messages= $('#message').val();
    data.append('messages',messages);
    // data.append('me',messages);
$.ajax({
                
        url: "../CompleteTicket",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        }).done(function (response) {

                        if(response > 0) {
                                 //  window.location="../TicketView/"+response;
                                 $.ajax({
                              
                              url: "../sendMail",
                             data:data,
                              cache: false,
                              processData: false,
                              contentType: false,
                              type: 'POST',
                              }).done(function (response) {
                                   
console.log(response);
$(".replyandcomplete").removeAttr("disabled");

                                              if(response == 1) {
        
                                          
        
                                              //  window.location="Tickets/Opened";
                                          
                                              
                            $("#result").html('Ticket has been completed successfully!')
                            $('#loading-image').addClass('d-none');
                            location.reload(true)
                                              }
                                            //  elseif(response[1] == 0)
                                            //   console.log("t");
                                             
                                              
                                              
                                             
                                      })


                        }
                   
                       
                }).fail(function(response){
                  $(".replyandcomplete").removeAttr("disabled");

                  console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
          pauseDelayOnHover: true,
          continueDelayOnInactiveTab: false,
          position: 'top right',
          msg: Object.values(response.responseJSON.errors)[0],
          icon: 'bx bx-info-circle'           
});
                });
        event.preventDefault();
});

 $(".replyandclose").click(function(){
  $(".replyandclose").attr("disabled", true);
  let myform=document.getElementById("form");
    let data1=new FormData(myform);
  $.ajax({
                    
                    url: "{{ route('CloseTicket') }}",
                    method: 'POST',
                    data:data1,
                    cache : false,
            processData: false,
            contentType: false
                  
                    }).done(function (response) {
                      $(".replyandclose").removeAttr("disabled");
                                    if(response > 0) {
        
                                        $("#result").html('Ticket has been Closed successfully!')
        
                                     window.location="../TicketView/"+response;
        
                                    }
                               
  $(".replyandclose").removeAttr("disabled", true);
                                   
                            }).fail(function(response){
  $(".replyandclose").removeAttr("disabled", true);
        
        console.log(Object.values(response.responseJSON.errors)[0]+"s");
        Lobibox.notify('warning', {
        pauseDelayOnHover: true,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: Object.values(response.responseJSON.errors)[0],
        icon: 'bx bx-info-circle'           
        });
        
        });
        event.preventDefault();
console.log(d);
      });    
// return true;
// var v=0;




    $(".saveupdatebtn").click(function(){
      $(".saveupdatebtn").attr("disabled", true);
      $('#loading-image').removeClass('d-none');
    let myform=document.getElementById("form");
    let data=new FormData(myform);
    var messages= $('#message').val();
    data.append('messages',messages);
    $.ajax({
                    
            url: "../updateTicketInfo",
            data: data,    
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            }).done(function (response) {

                            if(response > 0) {

                                $("#result").html('Ticket has been generated successfully!')

                             window.location="../TicketView/"+response;
                             // $("#result").html('Ticket has been generated successfully!')

                             $(".saveupdatebtn").removeAttr("disabled");

                          }

                            



                           
                    }).fail(function(response){

                      $(".saveupdatebtn").removeAttr("disabled");
console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
pauseDelayOnHover: true,
continueDelayOnInactiveTab: false,
position: 'top right',
msg: Object.values(response.responseJSON.errors)[0],
icon: 'bx bx-info-circle'           
});
});;
            event.preventDefault();
    });



    $(".sendtoocm").click(function(){

      $(".sendtoocm").attr("disabled", true);
    let myform=document.getElementById("form");
    let data=new FormData(myform);
    $.ajax({
                    
            url: "../sendTicketToOCM",
            data: data,    
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            }).done(function (response) {

                            if(response > 0) {

                                $("#result").html('Ticket has been sent to IT support!')

                             window.location="../TicketView/"+response;

                            }
                            $(".sendtoocm").removeAttr("disabled");
                           
                    }).fail(function(response){
                      $(".sendtoocm").removeAttr("disabled");
console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
pauseDelayOnHover: true,
continueDelayOnInactiveTab: false,
position: 'top right',
msg: Object.values(response.responseJSON.errors)[0],
icon: 'bx bx-info-circle'           
});
});;
            event.preventDefault();
    }); 


    $(".sendtonet").click(function(){

      $(".sendtonet").attr("disabled", true);

let myform=document.getElementById("form");
let data=new FormData(myform);
$.ajax({
                
        url: "../sendTicketToNET",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        }).done(function (response) {

                        if(response > 0) {

                            $("#result").html('Sent to IT Support!')

                         window.location="../TicketView/"+response;

                        }
                   
                       
      $(".sendtonet").removeAttr("disabled", true);
                }).fail(function(response){

                  $(".sendtonet").removeAttr("disabled", true);
console.log(Object.values(response.responseJSON.errors)[0]);
Lobibox.notify('warning', {
pauseDelayOnHover: true,
continueDelayOnInactiveTab: false,
position: 'top right',
msg: Object.values(response.responseJSON.errors)[0],
icon: 'bx bx-info-circle'           
});
});;
        event.preventDefault();
}); 


             $('#files').FancyFileUpload({

                url : "../uploadFiles",
                maxfilesize: 100000000000,
                params: {
                    tid:$('#tid').val(),
                    mid:$('#mid').val()
                },
                added : function(e, data) {
                this.find('.ff_fileupload_actions button.ff_fileupload_start_upload').click();
                },
                uploadcompleted : function(e, data) {
                    console.log(e);
                    
                }
            });



    });
</script>
@endpush