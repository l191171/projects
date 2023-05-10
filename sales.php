<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
use App\Models\User;
use DataTables;
use Validator;
use DB;
Use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;  
use PDF;

class sales extends Controller
{


    public function index(Request $request)
    {

        
         if ($request->ajax()) {
   
            $data = DB::table('sales')
                                    ->select(
                                    'sales.invoice_id',
                                    'sales.date',
                                    'sales.due_date',
                                    'sales.total',
                                    DB::raw('
                                    IFNULL((select sum(total) from sales a where a.invoice_id = sales.invoice_id and a.type = "Payment"),0)
                                     as status'),
                                    'sales.created_at',
                                    'sales.updated_at',
                                    'sales.updated_by',
                                    'customers.name as customer',
                                    'customer_accounts.name as customer_account',
                                    'A.name as created_by',
                                    'B.name as updated_by',
                                    )
                                    ->leftjoin('customers', 'sales.customer', '=', 'customers.uid')
                                    ->leftjoin('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                                    ->leftjoin('users AS A', 'A.id', '=', 'sales.created_by')
                                    ->leftjoin('users AS B', 'B.id', '=', 'sales.updated_by')
                                    ->where('sales.type','Invoice')
                                    ->get();


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                 <a href="invoiceView/'.$row->invoice_id.'" title="View Invoice" class="btn btn-info"><i class="fas fa-eye"></i>
                                </a>
                                 <a href="Payment/'.$row->invoice_id.'" title="Make Payment" class="btn btn-warning"><i class="fas fa-file-invoice-dollar"></i>
                                </a>
                                <a href="Invoice/'.$row->invoice_id.'" title="Edit Invoice" class="btn btn-primary">
                                 <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" title="Delete Invoice" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 

                    ->addColumn('status', function($row){ 


                            if($row->status >= $row->total) {

                             $status = '<span class="p-2 bg-success text-center d-block rounded">Paid</span>';

                            } elseif($row->status > 0) {

                            $status = '<span class="p-2 bg-info text-center d-block rounded">Partial</span>';

                            } else {

                            $status = '<span class="p-2 bg-danger text-center d-block rounded">unPaid</span>';
                                   
                            }

                           return $status; 
                     })

                    ->editColumn('total', function($row){ 

                           return business::businessinfo()[0]->currency.$row->total; 
                     })

                    ->editColumn('due', function($row){ 

                        $status = $row->total-$row->status;
                        return business::businessinfo()[0]->currency.$status;

                      })


                    ->editColumn('created_at', function($row){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d M y H:i a'); return $created_at; })
                    ->editColumn('updated_at', function($row){ $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $row->updated_at)->format('d M y H:i a'); return $updated_at; })

                    ->setRowId('invoice_id')
                    ->rawColumns(['status','action'])
                    ->make(true);

                    
                  
        }

        return view('invoices');
        
    }




     public function Invoice(Request $request)
    {

        $now = Carbon::now();
        $date =  $now->format('Y-m-d'); 
        $due_date = $now = Carbon::now()->addDays(7);
        $due_date =  $due_date->format('Y-m-d'); 
        $business = DB::table('business')->select('notes','footer')->get();
        $notes = $business[0]->notes;
        $footer = $business[0]->footer;


        $customer = '';
        $customername = '';
        $customer_account = '';
        $invoice_detail = '';
        $haspayments = 'no';

        $countries = DB::table('countries')->select('name','id')->get();

        if($request->uid != '') {

          // check payments

        $sales = DB::table('sales')
                          ->where('invoice_id', $request->uid)
                          ->where('type', 'Payment')
                          ->get();
        
        if(count($sales) > 0) {

            $haspayments = 'yes';
        }                  
                            

          $sales = DB::table('sales')
                          ->select('sales.date', 'sales.due_date', 'sales.customer', 'sales.customer_account', 'sales.notes', 'sales.footer', 'customers.name as customername')
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->where('invoice_id', $request->uid)
                          ->where('type', 'Invoice')
                          ->get();

          $date =  $sales[0]->date; 
          $due_date =  $sales[0]->due_date; 
          $customer =  $sales[0]->customer; 
          $customername =  $sales[0]->customername; 
          $customer_account =  $sales[0]->customer_account; 
          $notes =  $sales[0]->notes; 
          $footer =  $sales[0]->footer;              
          
          $invoice_detail = DB::table('invoice_detail')
                          ->join('products', 'invoice_detail.product', '=', 'products.uid')  
                          ->where('invoice_id', $request->uid)->get();
    
        } 
            
          $data = [
                    'date' => $date,
                    'due_date' => $due_date,
                    'customer' => $customer,
                    'customername' => $customername,
                    'customer_account' => $customer_account,
                    'invoice_detail' => $invoice_detail,
                    'haspayments' => $haspayments,
                    'notes' => $notes,
                    'footer' => $footer
          ];  

          return view ('invoice')->with('data',$data);
    }  
 



      public function getCustomersList(Request $request)
    {   
        $search = $request->input('search');
        $customers = DB::table('customers') 
                        ->where('name', 'like', '%'.$search.'%' )
                        ->orwhere('email', 'like', '%'.$search.'%' )
                        ->orderBy('id','desc')
                        ->limit(10)
                        ->get();
        
        $customersList = [];

        foreach ($customers as $customer) {
            $customersList[] = ['id' => $customer->uid, 'text' => $customer->name];
        }

        return \Response::json($customersList);
    }


      public function getCustomerAccounts(Request $request)
    {   
        $uid = $request->input('id');
        $accounts = DB::table('customer_accounts') 
                        ->where('customer', $uid )
                        ->orderBy('name','desc')
                        ->get();
        
        $accountsList = [];

        foreach ($accounts as $account) {
            $accountsList[] = ['id' => $account->uid, 'text' => $account->name];
        }

        return \Response::json($accountsList);
    }


      public function getProductsList(Request $request)
    {   
        $search = $request->input('search');
        $products = DB::table('products') 
                        ->where('name', 'like', '%'.$search.'%' )
                        ->orwhere('description', 'like', '%'.$search.'%' )
                        ->orderBy('id','desc')
                        ->limit(10)
                        ->get();
        
        $productsList = [];

        foreach ($products as $product) {
            $productsList[] = ['id' => $product->uid, 'text' => $product->name];
        }

        return \Response::json($productsList);
    }  

     public function getProductInfo(Request $request)
    {   
        $uid = $request->input('id');
        $productInfo = DB::table('products')
                        ->select('price','description') 
                        ->where('uid', $uid )
                        ->get();

        return \Response::json($productInfo);
    }


    public function saveInvoice(Request $request)
    {
        
        $user = auth()->user();
        $invoice_id = DB::table('sales')->max('invoice_id')+1;
        
        if($invoice_id == 1) {

            $invoice_id = DB::table('business')->select('invoice_s_f')->get();
            $invoice_id = $invoice_id[0]->invoice_s_f;
        }


        $date = $request->input('date');
        $due_date = $request->input('due_date');
        $customer = $request->input('customer');
        $customer_account = $request->input('customer_account');
        $notes = $request->input('notes');
        $footer = $request->input('footer');
        
         $products = $request->input('product');
         $description = $request->input('description');
         $quantity = $request->input('quantity');
         $price = $request->input('price');
         $tax = $request->input('tax');
         $discount = $request->input('discount');

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'due_date' => 'required',
            'customer' => 'required',
            'customer_account' => 'required',
            "product.*"  => "required",
            "quantity.*"  => "required",
            "price.*"  => "required",
            "tax.*"  => "required",
            "discount.*"  => "required"
        ]);

    


        if ($validator->passes()) {
            
            $amountTotal = 0;
            $costAmountTotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $gTotal = 0;

             foreach($products as $key => $product)
            {

            $cost = DB::table('products')->select('cost')->where('uid',$product)->get();
            $cost = $cost[0]->cost;

            $subtotal = $quantity[$key]*$price[$key]; 
            $costtotal = $quantity[$key]*$cost; 
            $discountvalue = $quantity[$key]*$price[$key]*$discount[$key]/100; 
            $taxvalue = $quantity[$key]*$price[$key]*$tax[$key]/100; 

            $amountTotal += $subtotal;
            $costAmountTotal += $costtotal;
            $discountTotal += $discountvalue;
            $taxTotal += $taxvalue;
           

            $iid = DB::table('invoice_detail')->max('id')+1;
            DB::insert('insert into invoice_detail (id, date, invoice_id, product, description, quantity, price, cost, discount, tax, subtotal, taxtotal, discounttotal, gtotal) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [
            $iid, $date, $invoice_id, $product, $description[$key], $quantity[$key], $price[$key], $cost, $discount[$key], $tax[$key], $quantity[$key]*$price[$key], $taxvalue, $discountvalue, $quantity[$key]*$price[$key]-$discountvalue+$taxvalue
            ]);

                         
            }

            $gTotal = $amountTotal-$discountTotal+$taxTotal;

            $sid = DB::table('sales')->max('id')+1;
            $uid = uniqid();
            DB::insert('insert into sales 
                (id, uid, invoice_id, customer, customer_account,  type, amount, cost_amount, discount, tax, total, status, date, due_date, notes, footer, created_at, updated_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$sid, $uid, $invoice_id, $customer, $customer_account, 'Invoice', $amountTotal, $costAmountTotal, $discountTotal, $taxTotal, $gTotal, 'unPaid', $date, $due_date, $notes, $footer, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user->id]);


                 return response()->json(['success'=>'Invoice Added.']);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     } 


     

         public function invoiceView(Request $request)
    {

        
          $business = DB::table('business')->get();
          $sales = DB::table('sales')
                          ->select(
                            'customer_accounts.*', 
                            'customers.name as customername', 
                            'customers.phone', 
                            'customers.email', 
                            'customers.alternative_phone',
                            'sales.invoice_id', 
                            'sales.amount', 
                            'sales.tax', 
                            'sales.discount', 
                            'sales.total', 
                            'sales.date', 
                            'sales.due_date', 
                            'sales.notes', 
                            'sales.footer',
                            'countries.name as country',
                            'states.name as state',
                              DB::raw('
                                    sales.total
                                    -
                                    IFNULL((select sum(total) from sales a where a.invoice_id = sales.invoice_id and a.type = "Payment"),0)
                                     as due')
                                      )
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                          ->join('countries', 'customer_accounts.country', '=', 'countries.id')
                          ->join('states', 'customer_accounts.state', '=', 'states.id')
                          ->where('sales.invoice_id', $request->uid)
                          ->where('sales.type', 'Invoice')
                          ->get();

                 
          
          $invoice_details = DB::table('invoice_detail')
                          ->join('products', 'invoice_detail.product', '=', 'products.uid')  
                          ->where('invoice_id', $request->uid)->get();
    
        
            
          $data = [
                    'business' => $business,
                    'sales' => $sales,
                    'invoice_details' => $invoice_details
          ];  

          return view ('invoiceview')->with('data',$data);
    } 


    public function invoiceDownload(Request $request)
    {
        
         $business = DB::table('business')->get();
          $sales = DB::table('sales')
                          ->select(
                            'customer_accounts.*', 
                            'customers.name as customername', 
                            'customers.phone', 
                            'customers.email', 
                            'customers.alternative_phone',
                            'sales.invoice_id', 
                            'sales.amount', 
                            'sales.tax', 
                            'sales.discount', 
                            'sales.total', 
                            'sales.date', 
                            'sales.due_date', 
                            'sales.notes', 
                            'sales.footer',
                            'countries.name as country',
                            'states.name as state',
                              DB::raw('
                                    sales.total
                                    -
                                    IFNULL((select sum(total) from sales a where a.invoice_id = sales.invoice_id and a.type = "Payment"),0)
                                     as due')
                                      )
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                          ->join('countries', 'customer_accounts.country', '=', 'countries.id')
                          ->join('states', 'customer_accounts.state', '=', 'states.id')
                          ->where('sales.invoice_id', $request->uid)
                          ->where('sales.type', 'Invoice')
                          ->get();

                 
          
          $invoice_details = DB::table('invoice_detail')
                          ->join('products', 'invoice_detail.product', '=', 'products.uid')  
                          ->where('invoice_id', $request->uid)->get();
    
        
            
          $data = [
                    'business' => $business,
                    'sales' => $sales,
                    'invoice_details' => $invoice_details
          ]; 

        
        $pdf = PDF::loadView('invoicedownload',array('data' =>$data)); 
        return $pdf->download('Invoice # '.$request->uid.'.pdf');
    }   


      public function updateInvoice(Request $request)
    {
  
        $user = auth()->user();
        $invoice_id = $request->input('invoice_id');
        $date = $request->input('date');
        $due_date = $request->input('due_date');
        $customer = $request->input('customer');
        $customer_account = $request->input('customer_account');
        $notes = $request->input('notes');
        $footer = $request->input('footer');
        

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'invoice_id' => 'required',
            'due_date' => 'required',
            'customer' => 'required',
            'customer_account' => 'required'
        ]);

        

         $products = $request->input('product');
         $description = $request->input('description');
         $quantity = $request->input('quantity');
         $price = $request->input('price');
         $tax = $request->input('tax');
         $discount = $request->input('discount');


        if ($validator->passes()) {
            
            $amountTotal = 0;
            $costAmountTotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $gTotal = 0;


            DB::table('invoice_detail')->where('invoice_id', $invoice_id)->delete();  


             foreach($products as $key => $product)
            {

            $cost = DB::table('products')->select('cost')->where('uid',$product)->get();
            $cost = $cost[0]->cost;

            $subtotal = $quantity[$key]*$price[$key]; 
            $costtotal = $quantity[$key]*$cost; 
            $discountvalue = $quantity[$key]*$price[$key]*$discount[$key]/100; 
            $taxvalue = $quantity[$key]*$price[$key]*$tax[$key]/100; 

            $amountTotal += $subtotal;
            $costAmountTotal += $costtotal;
            $discountTotal += $discountvalue;
            $taxTotal += $taxvalue;
           

            $iid = DB::table('invoice_detail')->max('id')+1;
            DB::insert('insert into invoice_detail (id, date, invoice_id, product, description, quantity, price, cost, discount, tax, subtotal, taxtotal, discounttotal, gtotal) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [
            $iid, $date, $invoice_id, $product, $description[$key], $quantity[$key], $price[$key], $cost, $discount[$key], $tax[$key], $quantity[$key]*$price[$key], $taxvalue, $discountvalue, $quantity[$key]*$price[$key]-$discountvalue+$taxvalue
            ]);

                         
            }

            $gTotal = $amountTotal-$discountTotal+$taxTotal;

            $sid = DB::table('sales')->max('id')+1;
            $uid = uniqid();

            $sales = DB::table('sales')
                          ->where('invoice_id', $invoice_id)
                          ->where('type', 'Payment')
                          ->get();
        
            if(count($sales) > 0) { 

            DB::update("
            update sales 
            set 
              
              amount = '".$amountTotal."', 
              cost_amount = '".$costAmountTotal."', 
              discount = '".$discountTotal."', 
              tax = '".$taxTotal."', 
              total = '".$gTotal."', 
              date = '".$date."',  
              due_date = '".$due_date."', 
              notes = '".$notes."', 
              footer = '".$footer."', 
              updated_at = '".date('Y-m-d H:i:s')."', 
              updated_by = '".$user->id."'

              where invoice_id =  '".$invoice_id."' and type = 'Invoice'
            ");
                
            } else {

             DB::update("
            update sales 
            set 
              
              customer = '".$customer."', 
              customer_account = '".$customer_account."', 
              amount = '".$amountTotal."', 
              cost_amount = '".$costAmountTotal."', 
              discount = '".$discountTotal."', 
              tax = '".$taxTotal."', 
              total = '".$gTotal."', 
              date = '".$date."',  
              due_date = '".$due_date."', 
              notes = '".$notes."', 
              footer = '".$footer."', 
              updated_at = '".date('Y-m-d H:i:s')."', 
              updated_by = '".$user->id."'

              where invoice_id =  '".$invoice_id."' and type = 'Invoice'
            ");
               

            }

            

                 return response()->json(['success'=>'Invoice Updated.']);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     } 

       public function deleteInvoice(Request $request)
    {   
        $id = $request->input('id');   
        DB::table('sales')->where('invoice_id', $id)->delete();  
        DB::table('invoice_detail')->where('invoice_id', $id)->delete();  
    } 




     public function indexPayments(Request $request)
    {

        
         if ($request->ajax()) {
   
            $data = DB::table('sales')
                                    ->select(
                                    'sales.uid as payment_id',
                                    'sales.invoice_id',
                                    'sales.date',
                                    'sales.total',
                                    'sales.status',
                                    'sales.created_at',
                                    'sales.updated_at',
                                    'sales.updated_by',
                                    'sales.payment_method',
                                    'customers.name as customer',
                                    'customer_accounts.name as customer_account',
                                    'accounts.name as payment_account',
                                    'A.name as created_by',
                                    'B.name as updated_by',
                                    )
                                    ->leftjoin('customers', 'sales.customer', '=', 'customers.uid')
                                    ->leftjoin('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                                    ->leftjoin('accounts', 'sales.payment_account', '=', 'accounts.id')
                                    ->leftjoin('users AS A', 'A.id', '=', 'sales.created_by')
                                    ->leftjoin('users AS B', 'B.id', '=', 'sales.updated_by')
                                    ->where('sales.type','Payment')
                                    ->get();


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($row){
     
                           $btn = '
                                <div class="btn-group" role="group">
                                 <a href="paymentView/'.$row->payment_id.'" title="View Payment" class="btn btn-info"><i class="fas fa-eye"></i>
                                </a>
                                <a href="Payment/'.$row->payment_id.'" title="Edit Payment" class="btn btn-primary">
                                 <i class="bx bx-edit"></i>
                                </a>
                                <button type="button" title="Delete Payment" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>
                                  ';
    
                            return $btn;
                    }) 

                    ->editColumn('invoice_id', function($row){ 

                            if($row->invoice_id != '') {

                        return $status = '<a title="View Invoice" href="invoiceView/'.$row->invoice_id.'"  class="btn btn-xs btn-info">#'.$row->invoice_id.'</a>';

                            } 
                     })

                    ->editColumn('total', function($row){ 

                           return business::businessinfo()[0]->currency.$row->total; 
                     })



                    ->editColumn('created_at', function($row){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d M y H:i a'); return $created_at; })
                    ->editColumn('updated_at', function($row){ $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $row->updated_at)->format('d M y H:i a'); return $updated_at; })

                    ->setRowId('invoice_id')
                    ->rawColumns(['invoice_id','status','action'])
                    ->make(true);

                    
                  
        }

        return view('payments');
        
    }



     public function Payment(Request $request)
    {

        $invoice_id = '';
        $payment_id = '';
        $type = '';
        $now = Carbon::now();
        $date =  $now->format('Y-m-d');
        $customer = '';
        $customername = '';
        $customer_account = '';
        $amount = '';
        $payment_method = '';
        $payment_account = '';
        $file = '';
        $notes = '';

        $payment_accounts  = DB::table('accounts')->select('name','id')->get();  


        if($request->uid != '') {

        $sales = DB::table('sales')
                          ->select('sales.uid as payment_id', 'sales.invoice_id', 'sales.date', 'sales.customer', 'sales.customer_account', 'sales.total', 'sales.type', 'sales.notes', 'sales.payment_method', 'sales.payment_account', 'sales.file','customers.name as customername')
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->where('sales.uid', $request->uid)
                          ->where('sales.type', 'Payment')
                          ->get();

          if(count($sales) == 0) {

          $sales = DB::table('sales')
                          ->select('sales.uid as payment_id', 'sales.invoice_id', 'sales.date', 'sales.customer', 'sales.customer_account', 
                        DB::raw('
                            IFNULL((select sum(total) from sales where invoice_id = "'.$request->uid.'" and type = "Invoice" ),0) -
                            IFNULL((select sum(total) from sales where invoice_id = "'.$request->uid.'" and type = "Payment" ),0)
                             as total'),
                            'sales.type', 'sales.notes', 'sales.payment_method', 'sales.payment_account', 'sales.file','customers.name as customername')
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->where('sales.invoice_id', $request->uid)
                          ->where('sales.type', 'Invoice')
                          ->get();
  
           $notes = '';              
                          
          }  else {

            $notes = $sales[0]->notes;  
            $date = $sales[0]->date;      
          }    

          $invoice_id =  $sales[0]->invoice_id; 
          $payment_id =  $sales[0]->payment_id; 
          $type =  $sales[0]->type; 
          $date =  $date; 
          $customer =  $sales[0]->customer; 
          $customername =  $sales[0]->customername; 
          $customer_account =  $sales[0]->customer_account; 
          $amount =  $sales[0]->total; 
          $payment_method =  $sales[0]->payment_method; 
          $payment_account =  $sales[0]->payment_account; 
          $file =  $sales[0]->file; 
          $notes =  $notes; 
         
          
    
        } 
  
            
          $data = [
                    'invoice_id' => $invoice_id,
                    'payment_id' => $payment_id,
                    'type' => $type,
                    'date' => $date,
                    'customer' => $customer,
                    'customername' => $customername,
                    'customer_account' => $customer_account,
                    'amount' => $amount,
                    'payment_method' => $payment_method,
                    'payment_account' => $payment_account,
                    'payment_accounts' => $payment_accounts,
                    'file' => $file,
                    'notes' => $notes

          ];  

          return view ('payment')->with('data',$data);
    } 



        public function savePayment(Request $request)
    {
        
        $user = auth()->user();
        $payment_id = DB::table('sales')->max('id')+1;
        
        $date = $request->input('date');
        $customer = $request->input('customer');
        $customer_account = $request->input('customer_account');
        $amount = $request->input('amount');
        $payment_method = $request->input('payment_method');
        $payment_account = $request->input('payment_account');
        $notes = $request->input('notes');

        

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'customer' => 'required',
            'customer_account' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'payment_account' => 'required'
        ]);

        


        if ($validator->passes()) {




            if ($request->hasFile('file')) {
                
                $file = $request->file('file');
                $extension = $request->file->getClientOriginalExtension();
         
                if($request->file('file')->getSize() > 40000000) {

                    return response()->json(['error'=> 'File size should be less than 40mb']);
             
                }

                $destinationPath = public_path('storage');
                $filename = uniqid().'.'.$extension;
                $file->move($destinationPath,$filename);
                $filename = "file = '$filename'";
               
                } else {
                    $filename = '';
                } 


            $uid = uniqid();
            DB::insert('insert into sales 
                (id, uid, customer, customer_account,  type, amount, total, status, payment_method, payment_account, date, notes, created_at, updated_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$payment_id, $uid, $customer, $customer_account, 'Payment', $amount, $amount, 'Paid', $payment_method, $payment_account, $date, $notes, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user->id]);


             DB::update("
                update sales 
                set 
                $filename
                where id = $payment_id
                ");


                 return response()->json(['success'=>'Payment Added.']);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     }  



     public function paymentView(Request $request)
    {

        
          $business = DB::table('business')->get();
          $sales = DB::table('sales')
                          ->select(
                            'customer_accounts.*', 
                            'customers.name as customername', 
                            'customers.phone', 
                            'customers.email', 
                            'customers.alternative_phone',
                            'sales.invoice_id', 
                            'sales.uid', 
                            'sales.amount', 
                            'sales.tax', 
                            'sales.discount', 
                            'sales.total', 
                            'sales.date', 
                            'sales.due_date', 
                            'sales.notes', 
                            'sales.footer',
                            'sales.file',
                            'countries.name as country',
                            'states.name as state',
                              DB::raw('
                                    sales.total
                                    -
                                    IFNULL((select sum(total) from sales a where a.invoice_id = sales.invoice_id and a.type = "Payment"),0)
                                     as due')
                                      )
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                          ->join('countries', 'customer_accounts.country', '=', 'countries.id')
                          ->join('states', 'customer_accounts.state', '=', 'states.id')
                          ->where('sales.uid', $request->uid)
                          ->where('sales.type', 'Payment')
                          ->get();

            
          $data = [
                    'business' => $business,
                    'sales' => $sales
          ];  

          return view ('paymentview')->with('data',$data);
    } 


    public function paymentDownload(Request $request)
    {
        
          $business = DB::table('business')->get();
          $sales = DB::table('sales')
                          ->select(
                            'customer_accounts.*', 
                            'customers.name as customername', 
                            'customers.phone', 
                            'customers.email', 
                            'customers.alternative_phone',
                            'sales.invoice_id',
                            'sales.uid',  
                            'sales.amount', 
                            'sales.tax', 
                            'sales.discount', 
                            'sales.total', 
                            'sales.date', 
                            'sales.due_date', 
                            'sales.notes', 
                            'sales.footer',
                            'countries.name as country',
                            'states.name as state',
                              DB::raw('
                                    sales.total
                                    -
                                    IFNULL((select sum(total) from sales a where a.invoice_id = sales.invoice_id and a.type = "Payment"),0)
                                     as due')
                                      )
                          ->join('customers', 'sales.customer', '=', 'customers.uid')
                          ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                          ->join('countries', 'customer_accounts.country', '=', 'countries.id')
                          ->join('states', 'customer_accounts.state', '=', 'states.id')
                          ->where('sales.uid', $request->uid)
                          ->where('sales.type', 'Payment')
                          ->get();

            
          $data = [
                    'business' => $business,
                    'sales' => $sales
          ]; 

        
        $pdf = PDF::loadView('paymentdownload',array('data' =>$data))->setPaper('a6', 'landscape'); 
        return $pdf->download('Payment ID '.$request->uid.'.pdf');
    }  




         public function updatePayment(Request $request)
    {
        
        $user = auth()->user();
         
        $payment_id = $request->input('payment_id');
        $date = $request->input('date');
        $customer = $request->input('customer');
        $customer_account = $request->input('customer_account');
        $amount = $request->input('amount');
        $payment_method = $request->input('payment_method');
        $payment_account = $request->input('payment_account');
        $notes = $request->input('notes');

        

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'customer' => 'required',
            'customer_account' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'payment_account' => 'required'
        ]);

        


        if ($validator->passes()) {

            
            if ($request->hasFile('file')) {
                
                $file = $request->file('file');
                $extension = $request->file->getClientOriginalExtension();
         
                if($request->file('file')->getSize() > 40000000) {

                    return response()->json(['error'=> 'File size should be less than 40mb']);
             
                }

                $destinationPath = public_path('storage');
                $filename = uniqid().'.'.$extension;
                $file->move($destinationPath,$filename);
                $filename = ", file = '$filename'";
               
                } else {
                    $filename = '';
                } 

            
            $customerInfo = DB::table('sales')
                                    ->select('customer','customer_account')
                                    ->where('uid',$payment_id)
                                    ->where('type','Payment')
                                    ->where('invoice_id','!=',null)
                                    ->get();

           
            if(count($customerInfo) > 0) {
            $customer = $customerInfo[0]->customer;
            $customer_account = $customerInfo[0]->customer_account;
            }


             DB::update("
            update sales 
            set 
              
              customer = '".$customer."', 
              customer_account = '".$customer_account."', 
              amount = '".$amount."', 
              total = '".$amount."', 
              date = '".$date."',  
              payment_method = '".$payment_method."',  
              payment_account = '".$payment_account."',   
              notes = '".$notes."', 
              updated_at = '".date('Y-m-d H:i:s')."', 
              updated_by = '".$user->id."'
              $filename

              where uid =  '".$payment_id."' 
            ");


                 return response()->json(['success'=>'Payment Updated.']);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     }  



          public function invoicePayment(Request $request)
    {
        
        $user = auth()->user();
         
        $payment_id = $request->input('payment_id');
        $date = $request->input('date');
        $amount = $request->input('amount');
        $payment_method = $request->input('payment_method');
        $payment_account = $request->input('payment_account');
        $notes = $request->input('notes');

        

        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'amount' => 'required',
            'payment_method' => 'required',
            'payment_account' => 'required'
        ]);

        


        if ($validator->passes()) {


            $customerInfo = DB::table('sales')
                                    ->select('customer','customer_account')
                                    ->where('invoice_id',$payment_id)
                                    ->where('type','Invoice')
                                    ->get();

            $customer = $customerInfo[0]->customer;
            $customer_account = $customerInfo[0]->customer_account;

            $id = DB::table('sales')->max('id')+1;
              $uid = uniqid();
            DB::insert('insert into sales 
                (id, uid, invoice_id, customer, customer_account,  type, amount, total, status, payment_method, payment_account, date, notes, created_at, updated_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $uid, $payment_id, $customer, $customer_account, 'Payment', $amount, $amount, 'Paid', $payment_method, $payment_account, $date, $notes, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user->id]);



                 return response()->json(['success'=>'Payment Updated.']);

                }

        return response()->json(['error'=>$validator->errors()->first()]);

     }  


         public function deletePayment(Request $request)
    {   
        $id = $request->input('id');   
        DB::table('sales')->where('uid', $id)->delete();  
    } 


}