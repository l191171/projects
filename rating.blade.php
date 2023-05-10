
   
        <div class="modal-body">
        <div class="text-center">
                  <img class="profile-user-img img-fluid img-circle" src="images/{{ Auth::user()->file }}" alt="User profile picture" onerror="this.onerror=null;this.src='{{ asset('images/'.'dp.webp') }}';">
                </div>
  


                  <h3 class="profile-username text-center"><?php echo $data['name'][0]; ?></h3>

<p class="text-muted text-center"><?php echo $data['text'][0]; ?></p>

<ul class="list-group list-group-unbordered mb-3">
  <li class="list-group-item">
    <b>Rating</b> <a class="float-right"><?php echo $data['sum']?></a>
  </li>
  <li class="list-group-item">
    <b>E-Mail</b> <a class="float-right"><?php echo $data['user']?></a>
  </li>


</ul>




             

         

                 

        </div>     
       




       