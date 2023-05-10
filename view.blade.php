@include('layouts.header')
<link rel="stylesheet" href="{{asset('css/style.css')}}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
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
 <h1 class="text-center">Patient Ticketing System</h1>

    <div class="tabs-one-whole d-flex align-items-start">

    
            <!-- Ticketing System Tab -->

            <form  id="form" >
   <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            	@csrf
                <div class="form-group">
                	<input type="hidden" name="id" value="{{$data['id']}}">
                    <label for="patientName">Patient Name</label>
                    <input type="text" class="form-control" id="patientName" aria-describedby="patientHelp" value="{{$data['patientname']}}" placeholder="Enter Patient Name" name="patientname" required>
                    <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                </div>
                <!-- Request ID and Sample ID -->
                <div class="form-group">
                    <label for="requestid">Request ID</label>
                    <input type="text" class="form-control f-one" value="{{$data['requestid']}}" id="requestid" name="requestid" placeholder="Request ID" required>
            
                    <label for="sampleid">Sample ID</label>
                    <input type="text" class="form-control f-one" name="sampleid" value="{{$data['sampleid']}}" id="sampleid" placeholder="Sample ID" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" value="{{$data['subject']}}" placeholder="Subject" required>
                </div>

                <!-- Department Dropdown -->
                <div class="row">
                	<div class="col-md-6">
                		<label class="form-label">Department</label>
                		<select class="form-select" name="department" required >
                			<option >{{$data['department']}}</option>
                			<option value="0">Choose an option</option>
                			<option>Technical department</option>
                		</select>
                	</div>
                	<div class="col-md-6">
                		<label class="form-label">Priority</label>
                		<select class="form-select" name="priority" required>
                			<option >{{$data['priority']}}</option>
                			<option value="0">Choose an option</option>
                			<option>Low</option>
                			<option>Medium</option>
                			<option>High</option>
                		</select>
                	</div>

                </div>
                <br>
      
                <!-- Message Area -->
                <div class="form-outline my-2">
                    <label class="form-label" for="textAreaExample2">Message</label>
                    <textarea class="form-control" id="textAreaExample2"  rows="8" name="message" required>{{$data['message']}}</textarea>
                </div>
                 <!-- Multiple File Uploads -->
                <label for="formFileMultiple" class="form-label">Select Multiple files</label>
                <!--<input class="form-control " type="file" name="filename" value="{{$file['filename']}}" id="images" multiple />-->
                <img src="{{asset($file['filename'])}}" width= '100' height='100' class="">
                <!--<input type="text" name="file" class="form-control" value="{{$file['filename']}}">-->
                <br>
                <!-- Submit Button -->
                  <a type="submit" class="btn btn-primary" value="Submit">Submit</a>
                  <button class="btn btn-warning btn1" id="{{$data['id']}}">Update</button>
                  <div id="result"></div>
            </form>
           
          </div>
         

        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  <script>
   $(document).ready(function(){          
$("button").click(function () {



    let myform = document.getElementById("form");
    let data = new FormData(myform);

        $.ajax({

        url: "view",
        data: data,    
        cache: false,
        processData: false,
        contentType: false,
        type:'POST'
        }).done(function (response) {
                                     
            $("#result").html('Your data has been updated sucessfully!');
            window.location="http://localhost:8080/Tickets/public/tickets";
        });

        event.preventDefault();

                });

});
  </script>
@include('layouts.footer')