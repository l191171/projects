<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticketing System</title>

  <style>
    .user-panel img {
    height: auto;
    width: 3rem;
}
  </style>
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/' . \App\Http\Controllers\business::businessinfo()[0]->file) }}"/>

  <!-- Google Font: Source Sans Pro -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link id="currentLink" rel="stylesheet" type="text/css" href="<?php echo Auth::user()->font_link;?>">

  
  <!-- Font Awesome Icons -->
  <link href="{{ asset('plugins/fontawesome-free/css/all.min.css?1112') }}" rel="stylesheet" >
  
  <!-- IonIcons -->
  <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" >
  
  <!-- Theme style -->
  <link href="{{ asset('dist/css/adminlte.css?1114') }}" rel="stylesheet" >
  <link href="{{ asset('css/icons.css?1112') }}" rel="stylesheet">
  <link href="{{ asset('plugins/notifications/css/lobibox.min.css?1112') }}" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css?1112') }}">
  
  <!-- DataTables -->
  <link href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css?1112') }}" rel="stylesheet" >
  <link href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css?1112') }}" rel="stylesheet" >
  <link href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css?1112') }}" rel="stylesheet" >

  <!-- Select2 -->
  <link href="{{ asset('plugins/select2/css/select2.min.css?1112') }}" rel="stylesheet" >
  <link href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css?1112') }}" rel="stylesheet" >

  <!-- fancyfileuploader -->
  <link rel="stylesheet" href="{{ asset('plugins/fancy-file-uploader/fancy_fileupload.css?1112') }}" />

  <!-- daterange picker -->
  <link href="{{ asset('plugins/daterangepicker/daterangepicker.css?1112') }}" rel="stylesheet" >

  <!-- Tempusdominus Bootstrap 4 -->
  <link href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css?1112') }}" rel="stylesheet" >

  <!-- Bootstrap4 Duallistbox -->
  <link href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css?1112') }}" rel="stylesheet" >
  
  <!-- BS Stepper -->
  <link href="{{ asset('plugins/bs-stepper/css/bs-stepper.min.css?1112') }}" rel="stylesheet" >

  <link href="{{ asset('css/custom.css?1128') }}" rel="stylesheet"/>

<?php 
    $font = Auth::user()->font;
    $font_weight = Auth::user()->font_weight;
?>
<style type="text/css">
      
      :root {
       --main-color:#{{Auth::user()->colorscheme}}; 
       --main-font:{{$font}};
       --main-font_weight:{{$font_weight}}; 
      }

      body {

        font-family: var(--main-font);
        font-weight: var(--main-font_weight) !important;
  
      }

</style>
<script type="text/javascript">
  var main_color = '#'+'{{Auth::user()->colorscheme}}';
</script>

</head>
<!--
`body` tag options:

  Apply one or more of the following classes to to the body tag
  to get the desired effect

  * sidebar-collapse
  * sidebar-mini
-->
<body class="sidebar-mini layout-fixed text-sm">

<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link sideMenu" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link"><b id="pageTitle">Home</b></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <li class="nav-item" title="Full Screen">
        <a class="nav-link rightsideBtn" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item" title="Logout">
        <a class="nav-link rightsideBtn" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-light-primary elevation-4">
  

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">

          <a href="{{ route('MyProfile') }}"><img src="{{ asset('images/' . Auth::user()->file) }}"  class="img-circle" style=" 
    height: auto;
    width: 3rem;
" alt="User Image" onerror="this.onerror=null;this.src='{{ asset('images/'.'dp.webp') }}';"></a>
        </div>
        <div class="info">
          <div style="display:flex;">
          <a href="{{ route('MyProfile') }}" class="d-block mr-2">{{Auth::user()->name." "}}</a>
          
        @if(Auth::user()->role==2)  
          <a href="">{{" ".  App\Http\Controllers\tickets::rating()}}</a>
          @else
          <a href=""></a>
          
          @endif

        </div>
       <small>{{App\Http\Controllers\users::headerrole()}}</small>
        </div>
      </div>

      
      <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar p-1 px-2">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->



      <!-- Sidebar Menu -->
      <nav class="mt-2">
        
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
          <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link {{ (request()->is('home')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard 
              </p>
            </a>
          </li>
          
          <!-- <li class="nav-item admin">
            <a href="{{ route('Business') }}" class="nav-link {{ (request()->is('Business')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Business Profile 
              </p>
            </a>
          </li> -->

     <!--<li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link {{ (request()->is('home')) ?  : '' }}">
                <i class="nav-icon fas fa-envelope-open-text"></i>
              <p>
                Support
              </p>
            </a>
          </li>-->
     
          <li class="nav-item {{ ( request()->is('Tickets/*') ) ? 'menu-open' : '' }} admin">

             <a href="#" class="nav-link {{ ( request()->is('Tickets/*') ) ? 'active' : '' }}">
              <i class="nav-icon fas fa-envelope-open-text"></i>
              <p>
                Tickets
                <i class="fas fa-angle-left  right"></i>
              </p>
            </a>

            <ul class="nav nav-treeview">

            <li class="nav-item">
                <a href="{{ route('Tickets') }}/ALL" class="nav-link {{ ( request()->is('Tickets/ALL') ) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i><p class="ml-0">All</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('Tickets') }}/Opened" class="nav-link {{ ( request()->is('Tickets/Opened') ) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i><p class="ml-0">Opened</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="{{ route('Tickets') }}/Processing" class="nav-link {{ ( request()->is('Tickets/Processing') ) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i><p class="ml-0">Processing</p>
                </a>
              </li>  
              
              <li class="nav-item">
                <a href="{{ route('Tickets') }}/Completed" class="nav-link {{ ( request()->is('Tickets/Completed') ) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i><p class="ml-0">Completed</p>
                </a>
              </li>   
              
              <li class="nav-item">
                <a href="{{ route('Tickets') }}/Closed" class="nav-link {{ ( request()->is('Tickets/Closed') ) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i><p class="ml-0">Closed</p>
                </a>
              </li>
         
    
  
            </ul>
              
    </li>

               <li class="nav-item">
          @if(\App\Http\Controllers\users::roleCheck()=='yes')
       
               <li class="nav-item">
            <a href="{{ route('Users') }}" class="nav-link {{ (request()->is('Users')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-users"></i>
              <p>
                Users 
              </p>
            </a>
          </li>
          @endif
            
         
          
          
           <li class="nav-item">
            <a href="{{ route('versionlist') }}" class="nav-link {{ (request()->is('versionlist')) ? 'active' : '' }}">
            <i class="nav-icon fas fa-list"></i>
              <p>
                Version List
              </p>
            </a>
          </li>
          
          
@if(Auth::user()->role==4)          
          
          @endif
          
          <li class="nav-item">
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="nav-icon fas fa-sign-out-alt"></i>
              <p>Logout</p>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
          </li>
         
         
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <script>
//       window.localStorage.setItem('openOnePage', Date.now());
//  var onLocalStorageEvent = function(e){
    
//    if(e.key == "openOnePage"){
  
//         window.localStorage.setItem('pageAlreadyOpen', Date.now());
//         }
        
//         if(e.key == "pageAlreadyOpen"){
          
//             window.location.href="/Error";
//             return false;
         
//         }
    
//     };     
//         window.addEventListener('storage', onLocalStorageEvent, false);
  </script>



