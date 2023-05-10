<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ticket;
use App\Models\ticketattachment;
use App\Models\contact;
use App\Models\client;
use App\Models\department;
use DB;
use App;
use Carbon\Carbon;
use DataTables;

class BTBS extends Controller
{

public function cautiontestIndex(){
    return view('BTBS.cautiontestsystem');
}
public function transfusionlabIndex(){
    return view('BTBS.transfusionlaboratory');
}
public function unitspending(){
    return view('BTBS.unitspending');
}
public function netacquireIndex(){
    return view('BTBS.netacquire');
}
public function netacquire2Index(){
    return view('BTBS.netacquire2');
}
public function microbiologyIndex(){
    return view('BTBS.microbiology');
}

public function dashboard (){
    return view ('BTBS.customsoftware');
}


public function ahg(){
    return view('BTBS.ahg');
}
public function IssueBatch(){
    return view('BTBS.issuebatch');
}
public function forward(){
    return view('BTBS.forward');
}
public function phistory(){
    return view('BTBS.patienthistory');
}
public function stock(){
    return view('BTBS.stock');
}
public function unlock(){
    return view('BTBS.unlock');
}
public function sorder(){
    return view('BTBS.sorder');
}
public function semenanalysis(){
    return view('BTBS.semenanalysis');
}

public function patientsearch (){
    return view('BTBS.patientsearch');
}


public function crossmatches (){
    return view('BTBS.crossmatches');
}



public function crossmatchreport (){
    return view('BTBS.crossmatchreport');
}




public function prophylaxis (){
    return view('BTBS.antidprophylaxis');
}





public function patientdetails (){
    return view('BTBS.patientdetails');
}





public function antibody (){
    return view('BTBS.antibody');
}


}
