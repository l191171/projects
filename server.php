<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use DB;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;  
    
class server extends Controller
{


     public function AddWards(Request $request)
    {   
      
        $id = DB::table('Wards')->max('id')+1;
        $ListOrder = DB::table('Wards')->max('ListOrder')+1; 
        $Code = $request->input('Code');
        $Text = $request->input('Text');
        $HospitalCode = $request->input('HospitalCode');
        $FAX = $request->input('FAX');
        $PrinterAddress = $request->input('PrinterAddress');
        $Location = $request->input('Location');


         $user = auth()->user();
        

        $validator = Validator::make($request->all(), [  
            'Code' => 'required',    
            'Text' => 'required|unique:Wards,Text'
        ]);
     

      if ($validator->passes()) {

         DB::insert('insert into Wards (id, Code, Text, InUse, HospitalCode, FAX, ListOrder, PrinterAddress, Location, created_at)
         values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $Code, $Text, 1, $HospitalCode, $FAX, $ListOrder, $PrinterAddress, $Location, date('Y-m-d H:i:s')] );      

            return response()->json(['success'=>'Ward added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }



    public function AddPatients(Request $request)
    {
        $id = DB::table('PatientIFs')->max('id')+1;
        $Chart = $request->input('Chart');
        $PatName = $request->input('PatName');
        $Sex = $request->input('Sex');
        $DoB = $request->input('DoB');
        $Ward = $request->input('Ward');
        $Clinician = $request->input('Clinician');
        $Address0 = $request->input('Address0');
        $Address1 = $request->input('Address1');
        $Entity = $request->input('Entity');
        $Episode = $request->input('Episode');
        $RegionalNumber = $request->input('RegionalNumber');
        $DateTimeAmended = $request->input('DateTimeAmended');
        $NewEntry = $request->input('NewEntry');
        $AandE = $request->input('AandE');
        $MRN = $request->input('MRN');
        $AdmitDate = $request->input('AdmitDate');
        $GP = $request->input('GP');
        $eMedRenalFlag = $request->input('eMedRenalFlag');
        $Address2 = $request->input('Address2');
        $Address3 = $request->input('Address3');
        $PatSurName = $request->input('PatSurName');
        $PatForeName = $request->input('PatForeName');
        $PrivatePatient = $request->input('PrivatePatient');
        $PatTitle = $request->input('PatTitle');
        $AreaCode = $request->input('AreaCode');
        $PatPhone = $request->input('PatPhone');
        $InsurancePolicyNumber = $request->input('InsurancePolicyNumber');
        $InsurancePolicyExpiry = $request->input('InsurancePolicyExpiry');
        $InsurancePlanType = $request->input('InsurancePlanType');
        $InsuranceCompanyName = $request->input('InsuranceCompanyName');
        $MedicalCardNumber = $request->input('MedicalCardNumber');
        $ADTmessage = $request->input('ADTmessage');
        $GP_Practice_Address = $request->input('GP_Practice_Address');
        $GP_Practice_identifier = $request->input('GP_Practice_identifier');
        $GP_Medical_Council_Number = $request->input('GP_Medical_Council_Number');
        $GP_Name = $request->input('GP_Name');

        

        $validator = Validator::make($request->all(), [  
            'PatName' => 'required'
        ]);
     

      if ($validator->passes()) {

         DB::insert('insert into PatientIFs (`id`, `Chart`, `PatName`, `Sex`, `DoB`, `Ward`, `Clinician`, `Address0`, `Address1`, `Entity`, `Episode`, `RegionalNumber`, `DateTimeAmended`, `NewEntry`, `AandE`, `MRN`, `AdmitDate`, `GP`, `eMedRenalFlag`, `Address2`, `Address3`, `PatSurName`, `PatForeName`, `PrivatePatient`, `PatTitle`, `AreaCode`, `PatPhone`, `InsurancePolicyNumber`, `InsurancePolicyExpiry`, `InsurancePlanType`, `InsuranceCompanyName`, `MedicalCardNumber`, `ADTmessage`, `GP_Practice_Address`, `GP_Practice_identifier`, `GP_Medical_Council_Number`, `GP_Name`, `created_at`)
        
         values (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, ?, ?, ?
        )',

        [$id, $Chart, $PatName, $Sex, $DoB, $Ward, $Clinician, $Address0, $Address1, $Entity, $Episode, $RegionalNumber, $DateTimeAmended, $NewEntry, $AandE, $MRN, $AdmitDate, $GP, $eMedRenalFlag, $Address2, $Address3, $PatSurName, $PatForeName, $PrivatePatient, $PatTitle, $AreaCode, $PatPhone, $InsurancePolicyNumber, $InsurancePolicyExpiry, $InsurancePlanType, $InsuranceCompanyName, $MedicalCardNumber, $ADTmessage, $GP_Practice_Address, $GP_Practice_identifier, $GP_Medical_Council_Number, $GP_Name, date('Y-m-d H:i:s')] );      

            return response()->json(['success'=>'Patient added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


     public function AddClinics(Request $request)
    {
        $id = DB::table('Clinics')->max('id')+1;
        $name = $request->input('name');
        $patient = $request->input('patient');


         $user = auth()->user();
        

        $validator = Validator::make($request->all(), [    
            'name' => 'required|unique:Clinics,name',
            'patient' => 'required'
        ]);
     

      if ($validator->passes()) {

         DB::insert('insert into Clinics (id, name, patient, status, created_at)
         values (?, ?, ?, ?, ?)', 
            [$id, $name, $patient, 1, date('Y-m-d H:i:s')] );      

            return response()->json(['success'=>'Clinic added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }



}