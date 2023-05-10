<?php
  
namespace App\Http\Controllers;
  
use DataTables;
use Validator;
use DB;
use Auth;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ActivityLogs extends Controller
{


     public function index(Request $request)
    {

        if((\App\Http\Controllers\users::roleCheck('Activity Logs','View',0)) == 'No')   
                    { return redirect('/home');} 

        if ($request->ajax()) {
            $data = DB::table('ActivityLog')->select(
                                'ActivityLog.ActivityID', 
                                'ActivityLog.RequestID', 
                                'ActivityLog.EpisodeID', 
                                'ActivityLog.TestID', 
                                'ActivityLog.SampleID',
                                'PatientIFs.PatName as PatientID',  
                                'ActivityLog.ActionType', 
                                'ActivityLog.Action',
                                'ActivityLog.Reason', 
                                'ActivityLog.Notes',
                                'ActivityLog.DateTimeOfRecord',
                                'ActivityLog.IP',
                                'ActivityLog.BrowserInfo',
                                'ActivityLog.InfoDateTime',
                                'ActivityLog.InfoModifiedDateTime',
                                'A.name as UserName',
                                'B.name as InfoAddedBy',
                                'C.name as InfoUpdatedBy',
                                'D.name as TestID'
                                )
                                ->leftjoin('PatientIFs', 'PatientIFs.id', '=', 'ActivityLog.PatientID')
                                ->leftjoin('users AS A', 'A.id', '=', 'ActivityLog.UserName')
                                ->leftjoin('users AS B', 'B.id', '=', 'ActivityLog.InfoAddedBy')
                                ->leftjoin('users AS C', 'C.id', '=', 'ActivityLog.InfoUpdatedBy')
                                ->leftjoin('testprofiles AS D', 'D.id', '=', 'ActivityLog.TestID');
                               

            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group" aria-label="Basic example">
                                <!-- <a href="User/'.$row->ActivityID.'" title="Edit User" class="btn btn-primary">
                                 <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" class="view btn btn-secondary"><i class="fas fa-eye"></i>
                                </button>-->
                                 <button type="button" class="delete btn btn-dark"><i class="fas fa-times"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    })
                     // ->editColumn('created_at', function ($request) {
                     //    return $request->created_at->format('d M Y H:i a'); // human readable format
                     //  }) 
                     //  ->editColumn('updated_at', function ($request) {
                     //    return $request->updated_at->format('d M Y H:i a'); // human readable format
                     //  })    
                    ->setRowId('ActivityID')
                    ->rawColumns(['action'])
                    ->make(true);
                  
        }

        return view ('activitylogs');
        
    }



      public function delete(Request $request)
    {
     $id = $request->input('id');   

     return DB::table('ActivityLog')->where('ActivityID', $id)->delete(); 

    }



     public function addLogs($RequestID,$EpisodeID,$TestID,$SampleID,$PatientID,$ActionType,$Action)
    {
            $user = auth()->user();
            $ActivityID = DB::table('ActivityLog')->max('ActivityID')+1;     
            DB::insert('insert into ActivityLog 
                (
                    ActivityID,
                    RequestID,
                    EpisodeID,
                    TestID,
                    SampleID,
                    PatientID,
                    ActionType,
                    Action,
                    UserName,
                    DateTimeOfRecord,
                    IP,
                    BrowserInfo
                ) 
                values (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                    )', 
                [
                    $ActivityID,
                    0,
                    0,
                    0,
                    0,
                    0,
                    $ActionType,
                    $Action,
                    $user->id,
                    date('Y-m-d H:i:s'),
                    request()->ip(),
                    //$request->header('User-Agent')
                    app('request')->header('User-Agent')
                ]);
    }

     

}