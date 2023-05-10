<?php
     
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Validator;
use DB;
     
class forgotPassword extends Controller
{
 

    public function index()
    {
        
        return view ('forgot');  


    }

     public function sendPassword(Request $request)
    {
        
         $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->passes()) {

            $status = Password::sendResetLink(
                    $request->only('email')
            );

         }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    }



}