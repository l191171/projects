<?php
  
namespace App\Http\Controllers;

use App;  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
use Auth;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class users extends Controller
{

    public function getUserTheme(Request $request)
    {
            $email = $request->email;
            $userInfo = DB::table('users')->select('colorscheme','font','font_link','font_weight')->where('email', $email)->get();
                
             if(count($userInfo) > 0) {

                 return response()->json(['success'=>'UserInfo.','data' => $userInfo]); 
             }   

           
        return response()->json(['error'=>'']);
    }

    public function getSignupTheme(Request $request)
    {
            $email = $request->email;
            $userInfo = DB::table('users')->select('colorscheme','font','font_link','font_weight')->where('role', 0)->get();
                
             if(count($userInfo) > 0) {

                 return response()->json(['success'=>'UserInfo.','data' => $userInfo]); 
             }   

           
        return response()->json(['error'=>'']);
    }


    public function login()
    {
         $users = DB::table('users')->select('email')->get(); 
         
          return view ('login')->with('data',$users);
          
    }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //      $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);
     
    //     if ($validator->passes()) {


    //         $users = DB::table('users')->select('new')->where('email',$request->email)->get();

    //         if (Auth::attempt($credentials)) {

    //                 $controller = App::make('\App\Http\Controllers\activitylogs');

    //                 $data = $controller->callAction('addLogs', [0,0,0,0,0,'Login','User logged into the System.']);
                    
    //                 return response()->json(['success'=>'Logging you in.', 'new' => $users[0]->new]);

    //             } else {
    //                 return response()->json(['error'=>'Email or Password is incorrect']);
    //             }

    //    } 
    //    else {

    //     return response()->json(['error'=>$validator->errors()->first()]);
       
    //    }
       
    // }

     public  static function roleCheck()
    {   
    

    $user = auth()->user();
    if($user->role==1||$user->role==4){
        return 'yes';
    }  else{
        return 'no';
    }
  

    }


     public function index(Request $request)
    {
         if(\App\Http\Controllers\users::roleCheck() == 'no')   
                    { return redirect('/home');} 

    if ($request->ajax()) {
    $r=auth()->user()->role;
     if($r==4){
     $data = User::select(
                            'users.id', 
                            'users.name', 
                            'users.email',
                            'users.zip',
                            'users.phone', 
                            'lists.Text as roles', 
                            'users.country',
                            'users.status',  
                            'users.created_at', 
                            'users.updated_at',
                            'users.created_by', 
                            'users.updated_by',
                            'A.name as created_by',
                            'B.name as updated_by'
                            )
                            ->leftjoin('lists', 'lists.id', '=', 'users.role')
                            ->leftjoin('users AS A', 'A.id', '=', 'users.created_by')
                            ->leftjoin('users AS B', 'B.id', '=', 'users.updated_by');
                         
     }
     else{
        $data = User::select(
            'users.id', 
            'users.name', 
            'users.email', 
            'users.zip',
            'users.phone', 
            'users.country',
            'users.status',  
            'users.created_at', 
            'users.updated_at',
            'users.created_by', 
            'users.updated_by',
            'A.name as created_by',
            'B.name as updated_by'
            )
            ->leftjoin('lists', 'lists.id', '=', 'users.role')
            ->leftjoin('users AS A', 'A.id', '=', 'users.created_by')
            ->leftjoin('users AS B', 'B.id', '=', 'users.updated_by')
            ->whereIn('users.role',[0,1,2,3]);

     }
        return Datatables::of($data)
    
                ->addIndexColumn()
                ->addColumn('action', function($row){
    
                       $btn = '
                            <div class="btn-group" role="group" aria-label="Basic example">
                            <a href="User/'.$row->id.'" title="Edit User" class="btn btn-primary update">
                             <i class="bx bx-edit"></i>
                            </a>
                            <button type="button" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                            </button>
                             </div>
                              ';
    
                        return $btn;
                })
                 ->editColumn('created_at', function ($request) {
                   $current = Carbon::now();
                    //return $request->created_at->format('Y-m-d H:i:s') ; // human readable format
                  })
    
                 ->editColumn('updated_at', function($data){ 
                    if($data->updated_at != '') {
    
                        $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); 
                        return $updated_at;
                        
                    }
                 })
    
                ->setRowId('id')
                ->rawColumns(['action'])
                ->make(true);
              
    }


        return view ('users');
        
    }


       public function User(Request $request)
    {

        
        $user = '';
        $editmode = 'off';


        $countries = DB::table('lists')->select('Text')->where('ListType', 'Countries')->where('InUse', 1)->groupBy('Text')->orderBy('Text')->get();
        $counties = DB::table('lists')->select('Text')->where('ListType', 'Counties')->where('InUse', 1)->orderBy('Text')->get();
        $towns = DB::table('lists')->select('Text')->where('ListType', 'Towns')->where('InUse', 1)->orderBy('Text')->get();
        $roles = DB::table('lists')->where('ListType', 'ROL')->where('InUse', 1)->whereNotIn('id',[4,5,6,7])
        ->orderBy('Text')->get();
        
        $roles2 = DB::table('lists')->where('ListType', 'ROL')->where('InUse', 1)
        ->whereIn('id',[4,5,6,7])
        ->orWhere('check',1)
        ->orderBy('Text')->get();
                                                     


        if($request->id != '') {

         $user = DB::table('users')->where('id', $request->id)->get();
         $editmode = 'on';
    

        } else {

            if((\App\Http\Controllers\users::roleCheck('Users','Add',0)) == 'No')   
                    { return redirect('/home');} 
        }
            
          $data = [
                    'editmode' => $editmode,
                    'user' => $user,
                    'roles' => $roles,
                    'roles2' => $roles2,
                    'countries' => $countries,
                    'counties' => $counties,
                    'towns' => $towns
          ];  

          return view ('user')->with('data',$data);
    }  
 


     public function add(Request $request)
    {   


        $uid = DB::table('users')->max('id')+1;
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $password = $request->input('password');
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $zip = $request->input('zip');
        $address = $request->input('address');
        $role = $request->input('role');
        $status = $request->input('InUse');

        $user = auth()->user();
        

        $validator = Validator::make($request->all(), [
            'name' => 'required',      
            'email' => 'required|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'InUse' => 'required'
        ]);
     

     if ($validator->passes()) {

     $dif = DB::table('users')->where('id',1)->get();
        DB::insert('insert into users (id, name, phone, email, password, role, status, address, city, state, country, zip, file, created_at, created_by, colorscheme, font, font_link,font_weight, resolution) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)', 
            
            [$uid, $name, $phone, $email, Hash::make($password), $role, $status, $address, $city, $state, $country, $zip, 'default.jpg', date('Y-m-d H:i:s'),  $user['id'],'1976D2', 'Kantumruy Pro', 'https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@600&display=swap', '600', '100'

                

            ]);      

        

            return response()->json(['success'=>'User added.']);




        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }



     public function update(Request $request)
    {
        $uid = $request->input('uid');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $password = $request->input('password');
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $zip = $request->input('zip');
        $address = $request->input('address');
        $role = $request->input('role');
        $status = $request->input('InUse');

        $user = auth()->user();
        

        $validator = Validator::make($request->all(), [
            'name' => 'required',      
            'email'=> 'required|unique:users,email,'.$uid,  
            'password' => 'nullable|min:6',
            'role' => 'required',
            'InUse' => 'required',
        ]);
     

     if ($validator->passes()) {

      
        DB::update("
            update users 
            set 
            name = '$name', email = '$email' , phone = '$phone' , role = '$role',  status = '$status', address = '$address', city = '$city', state = '$state', country = '$country', zip = '$zip', updated_at = '".date('Y-m-d H:i:s')."' , updated_by = '".$user['id']."'
            where id =  $uid 
            ");  

            if($password != '') {

              DB::update("update users set password = '".Hash::make($password)."'  where id =  $uid "); 
              
            }

    

            return response()->json(['success'=>'User updated.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }


    public function delete(Request $request)
    {
     $id = $request->input('id');   


     DB::table('users')->where('id', $id)->delete();

    }


    public static function headerrole(){
        
        $role = auth()->user()->role; 
        $role = DB::table('lists')->select('Text as name')->where('id', $role)->get();
        return $role[0]->name;
    }
      public function  profile()
    {   
         $user = auth()->user(); 
         $role = auth()->user()->role; 
         $role = DB::table('lists')->select('Text as name')->where('id', $role)->get();
         $profile = DB::table('users')->select('country','state','city','new')->where('id', $user->id)->get();
         $countries = DB::table('lists')->select('Text')->where('ListType', 'Countries')->where('InUse', 1)->orderBy('Text')->get();
         $counties = DB::table('lists')->select('Text')->where('ListType', 'Counties')->where('InUse', 1)->orderBy('Text')->get();
         $towns = DB::table('lists')->select('Text')->where('ListType', 'Towns')->where('InUse', 1)->orderBy('Text')->get();


          $data = [

                    'role' => $role,
                    'profile' => $profile,
                    'countries' => $countries,
                    'counties' => $counties,
                    'towns' => $towns
          ];



        return view ('profile')->with('data',$data);
    }

      
  public function updateMyProfile(Request $request)
    {  
        //  return $request;
        $id = auth()->user()->id;   
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $address = $request->input('address');
        $destinationPath = public_path('images');
        $file = $request->file('file');
        // $city = $request->input('city');
        $city = $request->input('town');

        $state = $request->input('state');
        $country = $request->input('country');
        $zip = $request->input('zip');

        if($file != '') {

         $extension = $request->file->getClientOriginalExtension();
         
             
        if($request->file('file')->getSize() > 4000000) {

            return response()->json(['error'=> 'Image size should be less than 4mb']);
     
        }
        

       
        $filename = uniqid().'.'.$extension;
        $file->move($destinationPath,$filename);
        $filename = ", file = '$filename'";
        } else {
            $filename = '';
        }  
        


        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address' => 'required',
           
        ]);
     
        if ($validator->passes()) {
            
        DB::update("
            update users 
            set 
            name = '$name', email = '$email' , phone = '$phone' , address = '$address', city = '$city', state  = '$state', country = '$country', zip = '$zip' $filename 
            where id =  $id 
            ");



            return response()->json(['success'=>'Info updated.']);
            

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);
    }

    public function addRole(Request $request)
    {if($request->ajax()){
        // return $request;

        $code=$request->code;
        $text=$request->name;
        DB::insert("insert into `lists`(`Code`,`Text`,`check`,`InUse`,`ListType`)values('$code','$text',1,1,'ROL')");
       
    }
    return view('addrole');
}

      public function updateUserPassword(Request $request)
    {
        $id = auth()->user()->id;   
        $current_password = $request->input('current_password');
        $password = $request->input('password');
        $confirm_password = $request->input('confirm_password');

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|password',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);
     
        if ($validator->passes()) {
        
        $password = Hash::make($password);    
        DB::update("update users 
            set 
            password = '$password',
            new = 0 where id =  $id ");

            // get current user
            $user = Auth::user();

            // logout user
            $userToLogout = User::find($id);
            Auth::setUser($userToLogout);
            Auth::logout();

            // set again current user
            Auth::setUser($user);



            return response()->json(['success'=>'Password updated.']);
            
        }
        
        return response()->json(['error'=>$validator->errors()->first()]);
    }

     public function UploadUsers(Request $request)
    {

        return view ('uploadusers');
    }

      public function syncUsersData(Request $request)
    {

        $user = auth()->user();
        $tempusers = DB::table('tempusers')->get();

        foreach($tempusers as $tempuser) {


           $users = DB::table('users')->where('email',$tempuser->email)->get();

           if(count($users) > 0) {


            DB::update("
            update users 
            set 
            name = '$tempuser->name' , phone = '$tempuser->phone' , address = '$tempuser->address', city = '$tempuser->city', state  = '$tempuser->state', country = '$tempuser->country', zip = '$tempuser->zip' , updated_at = '".date('Y-m-d H:i:s')."' , updated_by = '".$user['id']."' 
            where email =  '".$tempuser->email."' 
            ");

           } else {

             DB::insert('insert into users (name, email, password, role, department, subdepartment, phone, address, city, state, country, zip, file, created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            
            [$tempuser->name, $tempuser->email, $tempuser->password, $tempuser->role, $tempuser->department, $tempuser->subdepartment, $tempuser->phone, $tempuser->address, $tempuser->city, $tempuser->state, $tempuser->country, $tempuser->zip, 'default.jpg', date('Y-m-d H:i:s'),  $user['id'] ]);   

           }

        }

         return response()->json(['success'=>'Users info updated.' ]);

    }

    public function UploadUsersDataFile(Request $request)
    {

        DB::table('tempusers')->delete();
    

     $path = $request->file('file')->getRealPath();
    $data = array_map('str_getcsv', file($path));
    $csv_data = array_slice($data, 1);
        
        $counter = 0;
        $new = 0;

        foreach($csv_data as $data) {


             $users = DB::table('users')->select('email')->where('email',$data[1])->get();

            if(count($users) > 0) {

                $counter ++;


            } else {

                 $new  ++;
            }  

            $role = DB::table('lists')->select('id')->where('Text',$data[3])->where('lists.ListType','ROL')->get();
            $department = DB::table('userdepartments')->select('id')->where('name',$data[4])->get();
            $subdepartment = DB::table('usersubdepartments')->select('id')->where('name',$data[5])->get();

             DB::insert('insert into tempusers (name, email, password, role, department, subdepartment, phone, address, city, state, country, zip) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            
            [$data[0], $data[1], $data[2], $role[0]->id, $department[0]->id, $subdepartment[0]->id, $data[6], $data[7], $data[8], $data[9], $data[10], $data[11]]);    
        }
    
        return response()->json(['success'=>'File uploaded.','products'=> $counter, 'tproducts'=> $counter+$new ]);
    }



     
public function signup(){


    $countries = DB::table('lists')->select('Text')->where('ListType', 'Countries')->where('InUse', 1)->groupBy('Text')->orderBy('Text')->get();
    $counties = DB::table('lists')->select('Text')->where('ListType', 'Counties')->where('InUse', 1)->orderBy('Text')->get();
    $towns = DB::table('lists')->select('Text')->where('ListType', 'Towns')->where('InUse', 1)->orderBy('Text')->get();

    $data = [


        'countries' => $countries,
        'counties' => $counties,
        'towns' => $towns
];  

return view ('signup')->with('data',$data);
}
 
public function registerUser(Request $request){
    echo'value Posted';
$role=1;
$status="Pending";


    $request->validate([
'name'=>'required',


'address'=>'required',
'city'=>'required',
'email'=>'required|email|unique:users',
'phone'=>'required',
'state'=>'required',
'country'=>'required',
'zip'=>'required',

'password'=>'required|min:8|max:30',
''
    ]);
    $user=new User();
    $user->name=$request->name;
    $user->address=$request->address;
    $user->city=$request->city;
    $user->email=$request->email;
    $user->phone=$request->phone;
    $user->state=$request->state;
    $user->country=$request->country;
    $user->zip=$request->zip;
    
    $user->password=bcrypt($request->password);
    $user->role=$role;
    $user->status=$status;
    $res = $user->save();
if($res){
    return back()->with('success','Registered');
}
else{
    
    return back()->with('failed','Not Registered');
}

        }



 
    

}