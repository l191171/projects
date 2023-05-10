<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
use App;

class business extends Controller
{




 public static function vernoinfo()
    {
        
           $verno= DB::select('select vnumber from versions order by id desc  limit 1');
        
         if (count($verno) == 0) {

            $words = 101; 
      		  $words = (str_split($words, 1));

		 $words = explode(',', implode('.,', $words));
	return	 $words = $words[0].$words[1].$words[2];
      }	 else {
          
       

      		 $verno2 = $verno[0]->vnumber; 
      		  $verno2 = (str_split($verno2, 1));

		 $words = explode(',', implode('.,', $verno2));
		return $words = $words[0].$words[1].$words[2];
      }

    }


    public static function businessinfo()
    {
        
        return $data = DB::table('business')->select('currency','file','name')->get();

    }
    

    public  function business()
    {
         

         $business = DB::table('business')->where('id', '1')->get();
         $countries = DB::table('lists')->select('Text')->where('ListType', 'Countries')->where('InUse', 1)->orderBy('Text')->get();
         $counties = DB::table('lists')->select('Text')->where('ListType', 'Counties')->where('InUse', 1)->orderBy('Text')->get();
         $towns = DB::table('lists')->select('Text')->where('ListType', 'Towns')->where('InUse', 1)->orderBy('Text')->get();
         $verno= DB::select('select vnumber from versions order by id desc  limit 1');


          $data = [
                    'business' => $business,
                    'countries' => $countries,
                    'counties' => $counties,
                    'towns' => $towns
                   
          ]; 

   
        return view ('business')->with('data',$data);
    }

     public function updateBusinessInfo(Request $request)
    {

        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $fax = $request->input('fax');
        $vat = $request->input('vat');
        $website = $request->input('website');
        $destinationPath = public_path('images');
        $file = $request->file('file');

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
        ]);
     
        if ($validator->passes()) {
            
        DB::update("
            update business 
            set 
            name = '$name', email = '$email', phone = '$phone', fax = '$fax', vat = '$vat', website = '$website' $filename 
            ");



            return response()->json(['success'=>'Business info updated']);
            

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);
    }

     public function updateBusinessAddress(Request $request)
    {


        $address = $request->input('address');
        $city = $request->input('city');
        $state = $request->input('state');
        $country = $request->input('country');
        $zip = $request->input('zip');
        
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required'
        ]);
     
        if ($validator->passes()) {
            
        DB::update("
            update business 
            set 
            address = '$address', city = '$city', state  = '$state', country = '$country', zip = '$zip'
            ");


            return response()->json(['success'=>'Business address updated.']);
            

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);
    }







}
