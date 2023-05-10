<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;  
use Mail;
use PDF;

class products extends Controller
{


    public function index(Request $request)
    {

        
         if ($request->ajax()) {
   
            $data = DB::table('products')
                            ->select('products.id','products.uid','products.name','categories.name as category','products.price','products.created_at','products.updated_at')
                            ->join('categories', 'categories.uid', '=', 'products.category')
                                ->where('products.service', 'no')
                                ->get();


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                <a href="Product/'.$row->uid.'" title="Edit Product" class="btn btn-primary">
                                 <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" title="Delete Product" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 
                    ->editColumn('created_at', function($data){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d M Y H:i a'); return $created_at; })
                    ->editColumn('updated_at', function($data){ $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at; })

                    ->setRowId('id')
                    ->rawColumns(['action'])
                    ->make(true);

                    
                  
        }

        return view('products');
        
    }


     public function Product(Request $request)
    {

        $now = Carbon::now();
        $today =  $now->format('Y-m-01'); 
        
        $product = '';
        $productlocations = '';

        $categories = DB::table('categories')->select('name','uid')->get();

        if($request->uid != '') {

          $product = DB::table('products')->where('uid', $request->uid)->get();
          $productlocations = DB::table('product_locations')
                                ->select('customer_accounts.uid','customer_accounts.name','customer_accounts.country','customer_accounts.state','customer_accounts.city','customer_accounts.zip','customer_accounts.address','sales.total','sales.date')
                                ->join('sales', 'sales.customer_account', '=', 'customer_accounts.uid')
                                ->where('sales.type', 'Opening Balance')
                                ->where('customer_accounts.customer', $request->uid)
                                ->get();
    
        } 
            
          $data = [
                    'date' => $today,
                    'product' => $product,
                    'productlocations' => $productlocations,
                    'categories' => $categories
          ];  

          return view ('product')->with('data',$data);
    } 




     public function add(Request $request)
    {
        $id = DB::table('products')->max('id')+1;
        $uid = uniqid();
        $name = $request->input('name');
        $category = $request->input('category');
        $price = $request->input('price');
        $buy = $request->input('buy');
        $cost = $request->input('cost');

        
         $quantity = $request->input('quantity');
         $rack = $request->input('rack');

         $user = auth()->user();
        

        $validator = Validator::make($request->all(), [      
            'name' => 'required|unique:products,name',
            'category' => 'required',
            'price' => 'required',
            "quantity.*"  => "required"
        ]);
     

             if ($validator->passes()) {

             foreach($opening_balances as $key => $opening_balance)
            {

            $plid = DB::table('product_locations')->max('id')+1; 
            $auid = uniqid();

                  DB::insert('insert into product_locations (id, name,  uid, customer, country, state, city, zip, address, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$aid, $accountname[$key],  $auid, $uid, $country[$key], $state[$key],  $city[$key],  $zip[$key],  $address[$key],  date('Y-m-d H:i:s'), date('Y-m-d H:i:s')] );

            $sid = DB::table('sales')->max('id')+1;       
                  DB::insert('insert into sales (id, uid, customer, customer_account, type, amount, total, status, date, created_at, updated_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$sid, uniqid(), $uid, $auid, 'Opening Balance', $opening_balances[$key], $opening_balances[$key], 'unPaid', date('Y-m-d'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'),  $user['id'] ] );      
            

             } 


        DB::insert('insert into customers (id, uid, name, phone, alternative_phone, email, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $uid, $name, $phone, $alternative_phone, $email,  date('Y-m-d H:i:s'), date('Y-m-d H:i:s')] );      

            return response()->json(['success'=>'Data added.']);


        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }





}