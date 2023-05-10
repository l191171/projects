<?php
  
namespace App\Http\Controllers;
  
use DataTables;
use Validator;
use DB;
use Auth;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class patientsaudit extends Controller
{


     public function index(Request $request)
    {


        if ($request->ajax()) {
            $data = DB::table('patientifs')->select(
                                'patientifs.Chart', 
                                 'patientifs.PatName', 
                                 
                                 'patientifs.PatName',
                                 
                                 'patientifs.Sex',
                                 'patientifs.DoB',
                                 'patientifs.Ward',
                                 'patientifs.Clinician',
                                 
                                 'patientifs.Address1',
                                 'patientifs.PatPhone',
                                // 'ActivityLog.EpisodeID', 
                                // 'ActivityLog.TestID', 
                                // 'ActivityLog.SampleID',
                                // 'PatientIFs.PatName as PatientID',  
                                // 'ActivityLog.ActionType', 
                                // 'ActivityLog.Action',
                                // 'ActivityLog.Reason', 
                                // 'ActivityLog.Notes',
                                // 'ActivityLog.DateTimeOfRecord',
                                // 'ActivityLog.IP',
                                // 'ActivityLog.BrowserInfo',
                                // 'ActivityLog.InfoDateTime',
                                // 'ActivityLog.InfoModifiedDateTime',
                                // 'A.name as UserName',
                                // 'B.name as InfoAddedBy',
                                // 'C.name as InfoUpdatedBy',
                                // 'D.name as TestID'
            );
                               

            return Datatables::of($data)

                    ->addIndexColumn()
                  
                    ->setRowId('Chart')
                    ->rawColumns(['action'])
                    ->make(true);
                  
        }

        return view ('patientsaudit');
        
    }



   

     

}