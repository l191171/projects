<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<style>
.gradient-custom {
/* fallback for old browsers */

/* Chrome 10-25, Safari 5.1-6 */}

.card-registration .select-input.form-control[readonly]:not([disabled]) {
font-size: 1rem;
line-height: 2.15;
padding-left: .75em;
padding-right: .75em;
}
.card-registration .select-arrow {

}

.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 23px;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
</style>
<body>
<section class="vh-100 gradient-custom" style="">
  <div class="container py-5 h-100">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-12 col-lg-9 col-xl-7">
        <div class="card shadow-2-strong card-registration" style="border-radius: 15px;">
          <div class="card-body p-4 p-md-5 ">
            <div class="text-center mb-2">
          <img id="logo" style="width:35%" src="{{ asset('images/' . \App\Http\Controllers\business::businessinfo()[0]->file) }}" alt="{{ config('app.name') }}" class="mx-auto d-block brand-image" style="opacity:1;  ">
            <b class="h3 ">{{ \App\Http\Controllers\business::businessinfo()[0]->name }}</b>
</div>
            <form action ="{{route('register-user')}}" method="post">
              @if (Session :: has ('success'))
              <div>
                {{Session::get('success')}}
              </div>
              @endif
              @if (Session :: has ('failed'))
              <div>
                {{Session::get('failed')}}
              </div>
              @endif
@csrf
              <div class="row">
                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                  <label class="form-label" for="firstName">Full Name</label>
                    <input type="text" id="firstName" class="form-control form-control-lg"  name="name"/>
                   
                    <span class="text-danger">@error('name'){{$message}}@enderror</span>
                  </div>    
              
                </div>
                <div class="col-md-6 mb-4">
                <div class="form-outline ">
                <label for="birthdayDate"  class="form-label">Password</label>
                    <input type="password" class="form-control form-control-lg" name="password" id="birthdayDate" />
                  
                    
                    <span class="text-danger">@error('password'){{$message}}@enderror</span>
                  </div>
              </div>
</div>

              <div class="row">
                <div class="col-md-6 mb-4 d-flex align-items-center">

                  <div class="form-outline datepicker w-100">
                  <label for="birthdayDate"  class="form-label">Address</label>
                    <input type="text" class="form-control form-control-lg" name="address" id="birthdayDate" />
                
                    <span class="text-danger">@error('address'){{$message}}@enderror</span>
                  </div>


                </div>
              <div class="col-md-6 mb-4">
              
                
              <div class="form-outline datepicker w-100">
              <label for="birthdayDate"  class="form-label">City</label>
              <select class="form-control"  id="city" name="city">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['towns'] as $town)
                                                    <option>{{$town->Text}}</option>
                                                    @endforeach
                                                </select>
                    
                    <span class="text-danger">@error('city'){{$message}}@enderror</span>
                  </div>
                  <!-- <h6 class="mb-2 pb-1">Gender: </h6>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="femaleGender"
                      value="option1" checked />
                    <label class="form-check-label" for="femaleGender">Female</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="maleGender"
                      value="option2" />
                    <label class="form-check-label" for="maleGender">Male</label>
                  </div>

                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="otherGender"
                      value="option3" />
                    <label class="form-check-label" for="otherGender">Other</label>
                  </div>

                </div> -->
              </div> 

              <!-- <div class="row"> -->
                <div class="col-md-6 mb-4 ">

                  <div class="form-outline">
                  <label class="form-label" for="emailAddress">Email</label>
                    <input type="email" id="emailAddress" class="form-control form-control-lg"  name="email"/>
                
                    
                    <span class="text-danger">@error('email'){{$message}}@enderror</span>
                  </div>  <div class="form-outline datepicker w-100">
                  <label for="birthdayDate"  class="form-label mt-4">State</label>
                  <select class="form-control"  id="state" name="state">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['counties'] as $county)
                                                    <option>{{$county->Text}}</option>
                                                    @endforeach
</select>
                    
                    
                    <span class="text-danger">@error('state'){{$message}}@enderror</span>
                    <div class="form-outline datepicker w-100 mt-4">
                    <label for="birthdayDate"  class="form-label">Zip Code</label>
                    <input type="text" class="form-control  form-control-lg" name="zip" id="birthdayDate" />
                    
                    
                    <span class="text-danger">@error('zip'){{$message}}@enderror</span>
                  </div>
                  <p class="mt-2">
        <a class="text-secondary" href="{{ route('login') }}">Already a User?</a>
      </p>
                  </div>

                </div>

                <div class="col-md-6 mb-4">

                  <div class="form-outline">
                  <label class="form-label" for="phoneNumber">Phone Number</label>
                    <input type="tel" id="phoneNumber" class="form-control form-control-lg" name="phone"/>
                  
                  
                    <span class="text-danger">@error('phone'){{$message}}@enderror</span>
                  </div>
                  <div class="form-outline datepicker w-100 mb-5">
                  <label for="birthdayDate"  class="form-label mt-4">Country</label>
                  <select class="form-control"  id="country" name="country">
                                                    <option disabled selected value=""></option>
                                                  @foreach ($data['countries'] as $country)
                                                    <option>{{$country->Text}}</option>
                                                    @endforeach
                                                </select>
                   
                    
                    <span class="text-danger">@error('country'){{$message}}@enderror</span>
                  </div>  
                  <div class="mt-4 pt-2">
                <input class="btn btn-primary btn-lg " id="submit" type="submit" value="Submit" />
              </div>
                </div>
              </div>

              <div class="row">
                <div class="col-12">

                
                </div>
              </div>

            

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script>
 $(document).ready(function () {




// let position = email.search("@");

// if(position <= 0) {

//   return false;
// }

$.ajax({
  type: 'get',
  url:"{{ route( 'getSignupTheme') }}",
  // data: {'email' : email},
  dataType: 'json', 

  success: function(response){
console.log(response.data[0].colorscheme)
      if ($.isEmptyObject(response.error)){
               
               //console.log(response);
               
              //  $("section").get(0).style.setProperty("bac",'#'+response.data[0].colorscheme);
               $("body").css('background','#'+response.data[0].colorscheme);
               $("#submit").css('background','#'+response.data[0].colorscheme);
              
              //  $(".forgotPassword").attr('style','color:#'+response.data[0].colorscheme+' !important;');
              //  $('.card-outline').attr('style','border-top:3px solid #'+response.data[0].colorscheme+' !important;');
              //  $('body').css('font-weight',response.data[0].font_weight);
              //  $('body').css('font-family',response.data[0].font);
              //  $("body").get(0).style.setProperty("--main-color", '#'+response.data[0].colorscheme+' !important;');
              //  $('#currentLink').attr('href',response.data[0].font_link);
              // $('#logo').attr('style', '');    
                 
            }

   }
 })  


});
</script>

</section>
</body>
