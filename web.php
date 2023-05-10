<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\users;
use App\Http\Controllers\business;
use App\Http\Controllers\home;
use App\Http\Controllers\tickets;
use App\Http\Controllers\AppController;
use App\Http\Controllers\versions;
use App\Http\Controllers\patienthistorybt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('getUserTheme', [users::class, 'getUserTheme'])->name('getUserTheme');

Route::get('getSignupTheme', [users::class, 'getSignupTheme'])->name('getSignupTheme');
Auth::routes();

// Route::get('signUp',[users::class, 'signup']);



// Disable Registration 
Route::get('register', function() { return redirect('login'); });
Route::post('register', function() { return redirect('login'); });
// Route::get('login', 'Auth\LoginController@login');
Route::get('login', [users::class, 'login'])->name('login');
//forgot password
Route::get('forgotPassword', [forgotPassword::class, 'index'])->name('forgotPassword');
Route::post('forgotPassword', [forgotPassword::class, 'sendPassword']);

Route::get('Signup', [users::class, 'signup'])->name('Signup');
Route::post('register-user',[users::class,'registerUser'])->name('register-user');

// Authenticated Users Only
Route::group(['middleware' => 'auth'], function () {
	Route::get('Ticket/{id?}', [tickets::class, 'Ticket'])->name('Ticket');
	Route::get('TicketView/{id?}', [tickets::class, 'TicketView'])->name('TicketView');
	// Route::get('Tickets/{type}', [tickets::class, 'Tickettype'])->name('Tickets/{type}');
	Route::post('Tickets/{type?}/{tid?}', [tickets::class, 'tickets'])->name('Tickets');
	Route::get('Tickets/{type?}/{tid?}', [tickets::class, 'tickets'])->name('Tickets');
	Route::post('Ticket', [tickets::class, 'save'])->name('Ticket');
	Route::post('updateTicket',[tickets::class, 'update'])->name('updateTicket');
	Route::get('view/{id}', [tickets::class, 'view']);
	Route::get('deleteTicket/{id?}', [tickets::class, 'deleteTicket'])->name('deleteTicket');
	Route::get('Tickets', [tickets::class, 'tickets'])->name('Tickets');
	Route::post('uploadFiles', [tickets::class, 'uploadFiles'])->name('uploadFiles');
	Route::post('updateTicketInfo', [tickets::class, 'updateTicketInfo'])->name('updateTicketInfo');
	Route::post('CloseTicket', [tickets::class, 'CloseTicket'])->name('CloseTicket');
	Route::post('CompleteTicket', [tickets::class, 'CompleteTicket'])->name('CompleteTicket');
	Route::post('sendTicketToOCM', [tickets::class, 'sendTicketToOCM'])->name('sendTicketToOCM');
	Route::post('sendTicketToNET', [tickets::class, 'sendTicketToNET'])->name('sendTicketToNET');
	Route::post('assignTicketNow', [tickets::class, 'assignTicketNow'])->name('assignTicketNow');
	Route::get('Client', [tickets::class, 'client'])->name('Client');
	
	Route::get('ScanSample', [tickets::class, 'ScanSample'])->name('ScanSample');
	Route::post('ScanSample', [tickets::class, 'ScanSample'])->name('ScanSample');

	Route::get('Scan', [tickets::class, 'Scan'])->name('Scan');
	
	Route::post('Scanpost', [tickets::class, 'Scanpost'])->name('Scanpost');
	// Route::get('rep', [tickets::class, 'rep'])->name('rep');
	// Route::post('rep', [tickets::class, 'reporte'])->name('rep');
	// Route::post('reporte', [tickets::class, 'reporte'])->name('reporte');

	Route::get('reporte', [tickets::class, 'reporte'])->name('reporte');
	Route::post('reporte', [tickets::class, 'reporte'])->name('reporte');
Route::post('Client', [tickets::class, 'addc'])->name('Client');

Route::post('StartTicket', [tickets::class, 'StartTicket'])->name('StartTicket');

Route::post('PauseTicket', [tickets::class, 'PauseTicket'])->name('PauseTicket');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('Users', [tickets::class, 'usert'])->name('Users');

Route::get('Userdelete/{id}', [tickets::class, 'userd']);

Route::get('sendMail/{tid?}', [tickets::class, 'sendMail'])->name('sendMail');

Route::post('sendMail', [tickets::class, 'sendMail']);
Route::post('rateNow', [tickets::class, 'rateNow'])->name('rateNow');



Route::get('report',function(){
	return view('report');
});
// Route::get('/report',[AppController::class,'list']);
 Route::post('/search',[AppController::class,'search']);
 
Route::get('command', [tickets::class,'commands'])->name('command');

Route::get('Departments', [tickets::class, 'deptt'])->name('Departments');

Route::get('Departmentdel/{id?}', [tickets::class, 'deptdel'])->name('Departmentdel/{id?}');

Route::get('Departmentview/{id?}', [tickets::class, 'deptview'])->name('Departmentview/{id?}');
Route::post('Departmentview/{id?}', [tickets::class, 'deptsave'])->name('Departmentview/{id?}');

Route::get('Clientview/{id?}', [tickets::class, 'clientview']);
Route::post('Clientview/{id?}', [tickets::class, 'clientsave']);

Route::get('Userview/{id?}', [tickets::class, 'userview']);
Route::post('Userview/{id?}', [tickets::class, 'usersave']);

Route::get('Clients', [tickets::class, 'clientt'])->name('Clients');
Route::get('Cdelete/{id}', [tickets::class, 'cdel']);

Route::get('Department', [tickets::class, 'department'])->name('Department');
Route::post('Department', [tickets::class, 'adddepart'])->name('Department');

// Route::get('User', [tickets::class, 'user'])->name('User');
// Route::post('User', [tickets::class, 'adduser'])->name('User');
Route::get('Users', [users::class, 'index'])->name('Users');
Route::post('Users', [users::class, 'index'])->name('Users');
Route::get('User', [users::class, 'User'])->name('User');
Route::get('User/{id}', [users::class, 'User']);
Route::post('addUser', [users::class, 'add'])->name('addUser');
Route::post('deleteUser', [users::class, 'delete'])->name('deleteUser');
Route::post('updateUser', [users::class, 'update'])->name('updateUser');
 Route::get('Users/{ListType}', [lists::class, 'List'])->name('Roles');

 Route::get('addRole', [users::class, 'addRole'])->name('addRole');
 Route::post('addRole', [users::class, 'addRole'])->name('addRole');

 Route::post('userRate', [tickets::class, 'userRate'])->name('userRate');



// Profile Page
Route::get('MyProfile', [users::class, 'profile'])->name('MyProfile');
Route::post('updateMyProfile', [users::class, 'updateMyProfile'])->name('updateMyProfile');
Route::post('updateUserPassword', [users::class, 'updateUserPassword'])->name('updateUserPassword');

// Home Page
Route::get('', [home::class, 'index'])->name('/');
Route::get('home', [home::class, 'index'])->name('home');
Route::get('home/{id}', [patienthistorybt::class, 'index']);
Route::get('dashboardInfo', [home::class, 'dashboardInfo'])->name('dashboardInfo');
Route::get('getDepartmentStates', [home::class, 'getDepartmentStates'])->name('getDepartmentStates');
// Route::get('home', [home::class, 'index'])->name('home');
// //Route::get('home', [BTPController::class, 'list'])->name('home');
Route::get('Caution', [tickets::class, 'cautiontestIndex']);
Route::get('Transfusionlab', [tickets::class, 'transfusionlabIndex']);
Route::get('Unitspending', [tickets::class, 'unitspending']);
Route::get('Netacquire', [tickets::class, 'netacquireIndex']);

Route::get('Netacquire2', [tickets::class, 'netacquire2Index']);
Route::get('Microbiology', [tickets::class, 'microbiologyIndex']);
// Route::get('microbiology2', [tickets::class, 'microbiology2Index']);
// Route::get('microbiology3', [tickets::class, 'microbiology3Index']);

// Business Page 
	
Route::get('Business', [business::class, 'business'])->name('Business');
Route::post('updateBusinessInfo', [business::class, 'updateBusinessInfo'])->name('updateBusinessInfo');
Route::post('updateBusinessAddress', [business::class, 'updateBusinessAddress'])->name('updateBusinessAddress');
Route::post('updateInvoiceSetting', [business::class, 'updateInvoiceSetting'])->name('updateInvoiceSetting');
Route::get('cleanDatabase', [business::class, 'cleanDatabase'])->name('cleanDatabase');
Route::post('updateThemeInfo', [home::class, 'updateThemeInfo'])->name('updateThemeInfo');


Route::get('ocmdata', [users::class, 'ocmdata'])->name('ocmdata');
Route::post('ocmdata', [users::class, 'ocmdata'])->name('ocmdata');


Route::get('AHG', [tickets::class, 'ahg']);
Route::get('IssueBatch', [tickets::class, 'IssueBatch']);
Route::get('Forward', [tickets::class, 'forward']);
Route::get('PHistory', [tickets::class, 'phistory']);
Route::get('Stock', [tickets::class, 'stock']);
Route::get('Unlock', [tickets::class, 'unlock']);
Route::get('Sorder', [tickets::class, 'sorder']);
Route::get('Neteaquire', [tickets::class, 'neteaquire']);
Route::get('SemenAnalysis', [tickets::class, 'semenanalysis']);


Route::get('Antibody', [tickets::class, 'antibody']);
Route::get('Crossmatches', [tickets::class, 'crossmatches']);
Route::get('Crossmatchreport', [tickets::class, 'crossmatchreport']);
Route::get('Antidprophylaxis', [tickets::class, 'prophylaxis']);
Route::get('Patientdetails', [tickets::class, 'patientdetails']);
Route::get('Patientsearch', [tickets::class, 'patientsearch']);

Route::get('Reports', [reports::class, 'Reports'])->name('Reports');
Route::post('ShowReport', [reports::class, 'ShowReport'])->name('ShowReport');
Route::get('getTicketsReport', [home::class, 'getTicketsReport'])->name('getTicketsReport');
Route::get('Error', [tickets::class, 'Error'])->name('Error');

Route::get('versions/{id?}', [versions::class, 'versions'])->name('versions');
Route::post('versionsins', [versions::class, 'versionsins'])->name('versionsins');
Route::get('versionlist', [versions::class, 'vernodatatable'])->name('versionlist');
Route::post('updateversion', [versions::class, 'updateversion'])->name('updateversion');

});





