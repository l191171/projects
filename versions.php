<?php
  
namespace App\Http\Controllers;
use App;  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
use Auth;
use Session;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class versions extends Controller
{
public function versions($id=NULL){
    
    $fetch = [];

  if($id != ''){
   $fetch = DB::select("select * from versions where id = '$id' ");

  }
	

          $modules = DB::select('select distinct(modulename),ID from modules');
          $verno= DB::select('select vnumber from versions order by id desc  limit 1');
          if (count($verno) == 0) {

           $words = 101;
           
      		  $words = (str_split($words, 1));

		 $words = explode(',', implode('.,', $words));
		 $words = $words[0].$words[1].$words[2];
      }	 else {

      		 $verno2 = $verno[0]->vnumber+1; 
      		  $verno2 = (str_split($verno2, 1));

		 $words = explode(',', implode('.,', $verno2));
		 $words = $words[0].$words[1].$words[2];
      }
        return view('versions')->with('modules',$modules)->with('verno',$words)->with('fetch', $fetch);

}


public function versionsins(Request $request){

    $module=$request->module;
    $vnumber=$request->vnumber;

    $description=$request->description;

             $validator = Validator::make($request->all(), [      
            'module' => 'required',
            'vnumber' => 'required',
            'description' => 'required',
        ]);

          if ($validator->passes()) {

          	$vnumber = str_replace('.', '', $vnumber);

          DB::insert('insert into versions (modules,vnumber,description) values(?,?,?) ', [$module,$vnumber,$description]);
           return response()->json(['success' => 'Data Saved']);

      }

      else{
      	return response()->json(['error'=>$validator->errors()->first()]);
      }


}

 public function vernodatatable(Request $request){



            if ($request->ajax()) {

   
      $data = DB::table('versions');


            return Datatables::of($data)

                    ->addIndexColumn()
                     ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group" aria-label="Basic example">

                                 <a href="" title="Description" class="btn btn-warning des">
                                 Show Description
                                </a>
                                <a href="./versions/'.$row->id.'" title="Edit User" class="btn btn-primary update">
                                 <i class="fas fa-edit"></i>
                                </a>
                                
                                 </div>
                                  ';
    
                            return $btn;
                    })
  

                    ->setRowId('id')
                    ->rawColumns(['action'])
                    ->make(true);
                  
        }



    return view('vernodatatable');
 }
 
 public function updateversion(Request $request){
  // return $request;
  $uid = $request->input('id');
   $modules = $request->input('module');
  $description = $request->input('description');
  $vnumber = $request->input('vnumber');
   $validator = Validator::make($request->all(), [      
            'module' => 'required',
            'vnumber' => 'required',
            'description' => 'required',
        ]);


      if ($validator->passes()) {

            $vnumber = str_replace('.', '', $vnumber);

          
  DB::update("update versions set modules = '$modules', description = '$description',vnumber = '$vnumber' where id = '$uid'");
           return response()->json(['success' => 'Data update']);

      }

      else{
        return response()->json(['error'=>$validator->errors()->first()]);
      }


}



}
?>