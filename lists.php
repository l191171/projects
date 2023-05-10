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

class lists extends Controller
{

    public function List(Request $request)
    {
      
    


           $ListType = $request->ListType;

           if($ListType == 'Doc Type') { 

                $ListType = 'Doc Type'; 
                $ListType0 = 'Doc Types'; 
            } 
            elseif($ListType == 'Roles') { 

                $ListType = 'Roles'; 
                $ListType0 = 'User Roles'; 
            } 
            else {

                $ListType0 = $ListType; 
            }

          
            
           if((\App\Http\Controllers\users::roleCheck($ListType0,'View',0)) == 'No')  {

                 return redirect('/home');
             }   


        if($ListType == 'Roles') { $ListType = 'User Roles'; }
           $code = DB::table('Lists')->select('Code')->where('Text',$ListType)->get(); 

        if(count($code) == 0) {
             return redirect('/home');
        }

         if ($request->ajax()) {
            
            $data = DB::table('Lists') 
                         ->select('Lists.id',
                            DB::raw('(select count(ListOrder) from Lists where ListType = "'.$code[0]->Code.'") as MaxListOrder'),
                            'Lists.Text',
                            'Lists.InUse',
                            'Lists.Code',
                            'Lists.ListType',
                            'Lists.Default',
                            'Lists.ListOrder',
                            'Lists.created_at',
                            'Lists.updated_at',
                            'Lists.created_by',
                            'Lists.updated_by',
                            'A.name as created_by',
                            'B.name as updated_by')
                         ->leftjoin('users AS A', 'A.id', '=', 'Lists.created_by')
                         ->leftjoin('users AS B', 'B.id', '=', 'Lists.updated_by')
                         ->where('Lists.ListType',$code[0]->Code);


            return Datatables::of($data)

                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                            
                             $btn = '<div class="btn-group" role="group">';

                            if($data->ListOrder == 1) {
                            
                           
                            $btn .= '<button  title="Edit" class="btn btn-info movedown" index="'.$data->ListOrder.'">
                                 <i class="fas fa-arrow-down"></i>
                                </button>';
                            }
                            elseif($data->ListOrder == $data->MaxListOrder) {
                            
                           
                            $btn .= '<button  title="Edit" class="btn btn-info moveup" index="'.$data->ListOrder.'">
                                 <i class="fas fa-arrow-up"></i>
                                </button>';
                            }
                             else {

                            $btn .= '
                                <button  title="Edit" class="btn btn-info moveup" index="'.$data->ListOrder.'">
                                 <i class="fas fa-arrow-up"></i>
                                </button>

                                <button  title="Edit" class="btn btn-info movedown" index="'.$data->ListOrder.'">
                                 <i class="fas fa-arrow-down"></i>
                                </button>
                                ';
                            }      
    
                            $btn .= '<button id="'.$data->id.'" title="Edit" class="btn btn-primary update ">
                                 <i class="bx bx-edit"></i><button type="button" title="Delete" class="delete btn btn-dark"><i class="bx bx-x-circle"></i>
                                </button>
                                 </div>';

                            return $btn;
                    }) 
                    ->editColumn('InUse', function($data){ 

                        if($data->InUse == 1) {
                            return '<div class="input-group-prepend">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                      Yes
                                    </button>
                                    <ul class="dropdown-menu">
                                      <li id="0" class="dropdown-item status"><span type="button" class="status">No</span></li>
                                    </ul>
                                  </div>';
                        } else {
                            return '<div class="input-group-prepend">
                                    <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                      No
                                    </button>
                                    <ul class="dropdown-menu">
                                      <li id="1" class="dropdown-item status"><span type="button" class="status">Yes</span></li>
                                    </ul>
                                  </div>';
                        }
                    })
                    ->editColumn('created_at', function($data){ $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d M Y H:i a'); return $created_at; })

                    ->editColumn('updated_at', function($data){ 
                        if($data->updated_at != '') {

                            $updated_at = Carbon::createFromFormat('Y-m-d H:i:s', $data->updated_at)->format('d M Y H:i a'); return $updated_at;
                            
                        }
                     })

                    ->setRowId('id')
                    ->rawColumns(['action','InUse'])
                    ->make(true);

                    
     
            }           
                    
                    $data = [
                    'name' => $request->ListType,
                    'code' => $code[0]->Code
                    ];


        return view ('lists')->with('data',$data);


                   
    }




     public function add(Request $request)
    {
        $id = DB::table('Lists')->max('id')+1;
        $name = $request->input('name');
        $Code = $request->input('Code');
        $type = $request->input('type');
        $InUse = $request->input('InUse');
        $Default = $request->input('Default');

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:Lists,Text,ListType',  
            'name' => 'required|unique:Lists,Text,NULL,id,ListType,'.$type,    
            'Code' => 'required',
            'type' => 'required',
            'InUse' => 'required'
        ]);
     

        if ($validator->passes()) {


        $ListOrder = DB::table('Lists')->where('ListType', $type)->max('ListOrder')+1;    
        DB::insert('insert into Lists (id, Text, Code, ListOrder, ListType, InUse, Lists.Default,  created_at, created_by) values (?, ?, ?, ?, ?, ?, ?, ?, ?)', 
            [$id, $name, $Code, $ListOrder, $type, $InUse, $Default, date('Y-m-d H:i:s'), $user['id'] ] );  



              if($Default == 'Yes') {
                DB::update("
                update Lists 
                set 
                Lists.Default = '' where id !=  '$id'  and ListType = '$type'
                ");
            }


            return response()->json(['success'=>'Data added.']);

        }
        
        return response()->json(['error'=>$validator->errors()->first()]);

    }





     public function ListRowShift(Request $request)
    {

         $currentRow = $request->input('currentRow');
         $moveToRow = $request->input('moveToRow');
         $currentRowId = $request->input('currentRowId');
         $nextRowId = $request->input('nextRowId');

         DB::update("update Lists set ListOrder = $moveToRow where id = $currentRowId");
         DB::update("update Lists set ListOrder = $currentRow where id = $nextRowId");


    }
    


     public static function ListTypes()
    {

    return $data = DB::table('Lists')->where('ListType', 'List')->where('InUse', 1)->orderBy('ListOrder')->get();
          
    } 


    public function ListInfo(Request $request)
    {


        if($request->id != '') {

          $data = DB::table('Lists')->where('id', $request->id)->get();
          return \Response::json($data);  
        } 
             

          
    }  
 


    public function delete(Request $request)
    {
     
     $id = $request->input('id');   

        $data = DB::table('Lists')->select('ListType')->where('id', $id)->get();  

        if($data[0]->ListType == 'STP') {

            $data2 = DB::table('OCMPhlebotomy')->where('sampletype', $id)->get();   
           
           if(count($data2) > 0) {

                return response()->json(['error'=>'Data exist.']);                  
           }    
        }


        if($data[0]->ListType == 'SHL') {

            $data2 = DB::table('OCMRequestTestsDetails')->where('specialhandling', $id)->get();   
           
           if(count($data2) > 0) {

                return response()->json(['error'=>'Data exist.']);                  
           }    
        }

         if($data[0]->ListType == 'DPT') {

            $data2 = DB::table('OCMRequestTestsDetails')->where('department', $id)->get();   
           
           if(count($data2) > 0) {

                return response()->json(['error'=>'Data Exist.']);                  
           }    
        }



      DB::table('Lists')->where('id', $id)->delete(); 
      //DB::table('rolesPermissions')->where('role', $id)->delete(); 
      return response()->json(['success'=>'Data Deleted.']);


       
       
    }





      public function update(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $Code = $request->input('Code');
        $type = $request->input('type');
        $InUse = $request->input('InUse');
        $Default = $request->input('Default');

         $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:Lists,Text,'.$id,      
            'Code' => 'required',
            'type' => 'required',
            'InUse' => 'required'
        ]);

        if ($validator->passes()) {

            
            DB::update("
            update Lists 
            set 
            Text = '$name', Code = '$Code', ListType = '$type', InUse = '$InUse', Lists.Default = '$Default', updated_at = '".date('Y-m-d H:i:s')."',  
            updated_by = '".$user['id']."'  where id =  '$id' 
            ");

            if($Default == 'Yes') {
                DB::update("
                update Lists 
                set 
                Lists.Default = '' where id !=  '$id'  and ListType = '$type'
                ");
              
            }
            
            return response()->json(['success'=>'Info updated.']);
            
        }

            return response()->json(['error'=>$validator->errors()->first()]);
                
    } 


     public function indexCustomerBalances(Request $request)
    {
       
        $now = Carbon::now();
        $date =  $now->format('Y-m');
        $month =  $now->format('M/Y');
        $customers = DB::table('customers')->select('name','uid')->get(); 
        $customer_account = '';

       if ($request->ajax()) {
        
              if(!empty($request->month))
                  {
                    $date = $request->month;
                    $month = Carbon::parse($date)->format('M/Y');
                   } 

              if(!empty($request->customer_account))
                  {
                    $customer_account = " and customer_account = '".$request->customer_account."'";
                    
                   } 

            $data = DB::table('customers')
                        ->select(
                            'customers.uid',
                            'customers.name',
                            DB::raw('
                                SUBSTRING("'.$month.'", 1, 10) as month'),
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Opening Balance" and SUBSTRING(date, 1, 7) <= "'.$date.'" ),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                -
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                as total
                                '),
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) = "'.$date.'"), 0) 
                                as invoices
                                '), 
                            DB::raw('
                                
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Opening Balance" and SUBSTRING(date, 1, 7) <= "'.$date.'" ),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                -
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) = "'.$date.'"), 0)
                                as receivable
                                '), 
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 7) = "'.$date.'"), 0) 
                                as received
                                '), 
                            DB::raw('
                                  IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Opening Balance" and SUBSTRING(date, 1, 7) <= "'.$date.'" ),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                -
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 7) < "'.$date.'"),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 7) = "'.$date.'"), 0)
                                - 
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 7) = "'.$date.'"), 0) 
                                as closing
                                '), 
                                       
                            )
                        ->join('sales', 'sales.customer', '=', 'customers.uid')
                        ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                         ->when(!empty($request->customer) , function ($query) use($request){
                                return $query->where('sales.customer',$request->customer);
                                })
                        ->groupBy('customers.uid');


            return Datatables::of($data)

                    ->addIndexColumn()

                     ->editColumn('total', function($row){ 

                        return business::businessinfo()[0]->currency.$row->total;

                      })

                     ->editColumn('invoices', function($row){ 

                        return business::businessinfo()[0]->currency.$row->invoices;

                      })

                     ->editColumn('receivable', function($row){ 

                        return business::businessinfo()[0]->currency.$row->receivable;

                      })

                     ->editColumn('received', function($row){ 

                        return business::businessinfo()[0]->currency.$row->received;

                      })

                     ->editColumn('closing', function($row){ 

                        return business::businessinfo()[0]->currency.$row->closing;

                      })

                    ->setRowId('uid')
                    ->rawColumns(['action'])
                    ->make(true);

                    
                  
        }

                 $data = [
                    'month' => $date,
                    'customers' => $customers
          ];


        return view ('customerbalances')->with('data',$data);
                
    } 



        public function indexCustomerStatement()
    {
        $now = Carbon::now();
        $date =  $now->format('Y-m');
        $customers = DB::table('customers')->select('name','uid')->get(); 
        $business = DB::table('business')->select('file')->get();

                 $data = [
                    'date' => $date,
                    'customers' => $customers,
                    'business' => $business
          ];


        return view ('customerstatement')->with('data',$data);
                
    }



        public function getCustomerStatement(Request $request)
    {
        

        $daterange = $request->input('daterange');
        $customer = $request->input('customer');
        $customer_account = $request->input('customer_account');


        $validator = Validator::make($request->all(), [
            'daterange' => 'required',      
            'customer' => 'required'
        ]);


        if ($validator->passes()) {

        $daterange = str_replace(' ','',$daterange);
        $daterange = explode('-',$daterange);
        $from = $daterange[0];
        $from = Carbon::createFromFormat('Y/m/d', $from)->format('Y-m-d');
        $to = $daterange[1];
        $to = Carbon::createFromFormat('Y/m/d', $to)->format('Y-m-d');

        $datefrom = Carbon::createFromFormat('Y-m-d', $from)->format('d M Y');
        $dateto = Carbon::createFromFormat('Y-m-d', $to)->format('d M Y');

        $business = DB::table('business')->get();
        $customer_info = DB::table('customers')->where('uid',$customer)->get(); 
        
            if($customer_account != '') {

                 $customer_account_info = DB::table('customer_accounts')
                                            ->select('customer_accounts.*','countries.name as country','states.name as state',)
                                            ->join('countries', 'countries.id', '=', 'customer_accounts.country')
                                            ->join('states', 'states.id', '=', 'customer_accounts.state')
                                            ->where('uid',$customer_account)
                                            ->get(); 

            } else {

                 $name = 'All';   
                 $customer_account_info = DB::table('customer_accounts')
                                            ->select('customer_accounts.*',
                                                DB::raw('SUBSTRING("'.$name.'", 1, 10) as name'),
                                                'countries.name as country','states.name as state',)
                                            ->join('countries', 'countries.id', '=', 'customer_accounts.country')
                                            ->join('states', 'states.id', '=', 'customer_accounts.state')
                                            ->where('customer_accounts.customer',$customer)
                                            ->where('customer_accounts.name','Primary Account')
                                            ->get(); 
            }


           if(!empty($request->customer_account))
                  {
                    $customer_account = " and customer_account = '".$request->customer_account."'";
                    
                   } 
                   
            $customerbalance = DB::table('customers')
                        ->select(
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Opening Balance" ),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 10) < "'.$from.'"),0) 
                                -
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 10) < "'.$from.'"),0) 
                                as total
                                '),
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 10) between "'.$from.'" and  "'.$to.'"), 0) 
                                as invoices
                                '), 
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 10) between "'.$from.'" and  "'.$to.'"), 0) 
                                as received
                                ')
                                       
                            )
                        ->join('sales', 'sales.customer', '=', 'customers.uid')
                        ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                        ->where('customers.uid',$customer)
                        ->groupBy('customers.uid')->get();


                 $activities = DB::table('sales')
                        ->select('sales.date','sales.type','customer_accounts.name','sales.total','sales.uid','sales.invoice_id')
                        ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                        ->where('sales.customer',$customer)
                        ->where('sales.type','!=','Opening Balance')
                        ->where('sales.date','>=',$from)
                        ->where('sales.date','<=',$to)
                         ->when(!empty($request->customer_account) , function ($query) use($request){
                                return $query->where('sales.customer_account',$request->customer_account);
                                })
                        ->get();
                               

                 $closingbalance = $customerbalance[0]->total+$customerbalance[0]->invoices-$customerbalance[0]->received;      

                 $data = [
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'customer' => $customer_info,
                    'customer_account' => $customer_account_info,
                    'customerbalance' => $customerbalance,
                    'closingbalance' => $closingbalance,
                    'business' => $business,
                    'activities' => $activities
          ];

          return \Response::json($data);
        }

          return response()->json(['error'=>$validator->errors()->first()]);  
        
                
        } 


        public function downloadCustomerStatement(Request $request) {

                $from =  $request->datefrom;
                $to =  $request->dateto;
                $datefrom = Carbon::createFromFormat('Y-m-d', $from)->format('d M Y');
                $dateto = Carbon::createFromFormat('Y-m-d', $to)->format('d M Y');

                $customer =  $request->customer;
                $customer_account =  $request->customer_account;
                $business = DB::table('business')->get();

            
                $business = DB::table('business')->get();
        $customer_info = DB::table('customers')->where('uid',$customer)->get(); 
        
            if($customer_account != 'All') {

                 $customer_account_info = DB::table('customer_accounts')
                                            ->select('customer_accounts.*','countries.name as country','states.name as state',)
                                            ->join('countries', 'countries.id', '=', 'customer_accounts.country')
                                            ->join('states', 'states.id', '=', 'customer_accounts.state')
                                            ->where('uid',$customer_account)
                                            ->get(); 

            } else {

                 $name = 'All';   
                 $customer_account_info = DB::table('customer_accounts')
                                            ->select('customer_accounts.*',
                                                DB::raw('SUBSTRING("'.$name.'", 1, 10) as name'),
                                                'countries.name as country','states.name as state',)
                                            ->join('countries', 'countries.id', '=', 'customer_accounts.country')
                                            ->join('states', 'states.id', '=', 'customer_accounts.state')
                                            ->where('customer_accounts.customer',$customer)
                                            ->where('customer_accounts.name','Primary Account')
                                            ->get(); 
            }


           if($customer_account != 'All')
                  {
                    $customer_account = " and customer_account = '".$request->customer_account."'";
                    
                   } else {

                    $customer_account = '';

                   }
                   
            $customerbalance = DB::table('customers')
                        ->select(
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Opening Balance" ),0) 
                                +
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 10) < "'.$from.'"),0) 
                                -
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 10) < "'.$from.'"),0) 
                                as total
                                '),
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Invoice" and SUBSTRING(date, 1, 10) between "'.$from.'" and  "'.$to.'"), 0) 
                                as invoices
                                '), 
                            DB::raw('
                                IFNULL((select sum(total) from sales where customer = customers.uid '.$customer_account.' and type = "Payment" and SUBSTRING(date, 1, 10) between "'.$from.'" and  "'.$to.'"), 0) 
                                as received
                                ')
                                       
                            )
                        ->join('sales', 'sales.customer', '=', 'customers.uid')
                        ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                        ->where('customers.uid',$customer)
                        ->groupBy('customers.uid')->get();


                    $activities = DB::table('sales')
                        ->select('sales.date','sales.type','customer_accounts.name','sales.total','sales.uid','sales.invoice_id')
                        ->join('customer_accounts', 'sales.customer_account', '=', 'customer_accounts.uid')
                        ->where('sales.customer',$customer)
                        ->where('sales.type','!=','Opening Balance')
                        ->where('sales.date','>=',$from)
                        ->where('sales.date','<=',$to)
                         ->when(!empty($customer_account) , function ($query) use($request){
                                return $query->where('sales.customer_account',$request->customer_account);
                                })
                        ->get();
                               

                 $closingbalance = $customerbalance[0]->total+$customerbalance[0]->invoices-$customerbalance[0]->received;      

                 $data = [
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                    'customer' => $customer_info,
                    'customer_account' => $customer_account_info,
                    'customerbalance' => $customerbalance,
                    'closingbalance' => $closingbalance,
                    'business' => $business,
                    'activities' => $activities,
                    ];



                view ('customerstatementdownload')->with('data',$data);
                $pdf = PDF::loadView('customerstatementdownload',array('data' =>$data)); 
        return  $pdf->download('Customer Statement - '.$customer_info[0]->name.' - '.$customer_account_info[0]->name.'.pdf');
        

        }



}