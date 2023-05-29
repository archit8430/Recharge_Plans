<?php

namespace App\Http\Controllers;

use PHPExcel;
use App\Models\User;
use App\Models\tbl_company;
use App\Exports\TableExport;
use App\Models\tbl_recharge;
use Illuminate\Http\Request;
use App\Models\tbl_commission;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Event;
use App\Events\ApiHit;

class apiController extends Controller
{    
    //Registration Function
    public function RegisterUser(Request $req){

            $req->validate([
                'username'=>'required',
                'email'=>'required|unique:users',
                'password'=>'required|confirmed',
                'password_confirmation'=>'required',
            ]);

            $usrObj=User::create([
                'name'=>$req->username,
                'email'=>$req->email,
                'password'=>Hash::make($req->password),
            ]);

            $token=$usrObj->createToken('myToken')->plainTextToken;

            return response([
                'user'=>$usrObj,
                'token'=>$token,
            ],200);
    }

    public function loginUser(Request $req){
            $req->validate([
                'email'=>'required',
                'password'=>'required',
            ]);

            $userObj=user::where('email',$req->email)->first();


            if(!$userObj || Hash::check($req->password,$userObj->password)){
                return response([
                    'message'=>'The provided credential are incorrect',
                ],401);            
            }

            $token=$userObj->createToken('myToken')->plainTextToken;

            return response([
                'user'=>$userObj,
                'token'=>$token,
            ],200);

    }

    public function logout(user $user){
            $user->tokens()->delete();
            return response()->json(['message'=>'Successfully Logged Out']);
    }
    
    // Recharge Functions
    public function insertRechargeData(Request $request){

        $validator=Validator::make($request->all(),
            [
            'file'=>'file|required|mimes:txt,csv' 
            ]               
        );

        if($validator->fails()){            
            return response()->json(['errors'=>$validator->errors()]);
        }

        $getFileData = file($request['file']->getRealPath());
        $getFileData = array_map('str_getcsv', $getFileData);
        $fileHeaders = array_flip(array_change_key_case(array_flip(array_slice($getFileData[0],0,5)),CASE_LOWER));
        $fileErrorLog = [];
        $validatedImportData =[];

        foreach($getFileData as $key=>$rows){
            if($key!=0){
                $rows = array_combine($fileHeaders,$rows);
                $validator=Validator::make($rows,
                [
                    'mobile'=> 'required|digits:10|',
                    'company'=> 'required|exists:tbl_companies,company_name',
                    'amount'=> 'required|numeric',
                    'status'=> 'required|In:Pending,Success,Failed,Credit',
                    'recharge_date'=> 'required|date|date_format:Y-m-d',
                ]
                );
                if($validator->fails()){  
                    $fileErrorLog['Row '.$key] = $validator->messages()->all();
                }else{
                    $rows['created_at'] = Carbon::now();
                    $rows['updated_at'] = Carbon::now();
                    $validatedImportData[$key]= $rows;
                }
            }
        }

        if(!empty($fileErrorLog)){
            return response([
                'status'=>'ERR',
                'message'=>'The Imported Records Have Error',
                'errors'=>$fileErrorLog,
            ],401);            
        }

        tbl_recharge::insert($validatedImportData);

        $eventParams=[
            'data'=>'',
            'statusCode'=>'TXN',
            'statusMessage'=>'Recharge Data Updated'
        ];

        event (new \App\Events\ApiHit($eventParams));

        return response()->json(['status'=>'TXN','message'=>'File Imported Successfully']);

    }

    public function updateRechargeStatus(Request $request){

        $validator=Validator::make($request->all(),
            [
            'rechargeId' => 'required|int|exists:tbl_recharges,id',
            'status'=> 'required|In:Pending,Success,Failed,Credit',
            ]               
        );

        if($validator->fails()){            
            return response()->json(['errors'=>$validator->errors()]);
        }

        $existingRechargeData=tbl_recharge::select('status','recharge_date')->find($request->rechargeId);

        if($existingRechargeData['status']=='Failed' && in_array($request['status'],['Pending','Success'])){
            return response()->json(['status'=>'ERR','message'=>'Status Can Not Be changed from Failed to Success/Pending']);            
        }else if($existingRechargeData['status']=='Success' && in_array($request['status'],['Pending','Failed'])){
            return response()->json(['status'=>'ERR','message'=>'Status Can Not Be changed from Failed to Pending/Failed']);            
        }else if($existingRechargeData['status']=='Pending' && $existingRechargeData['recharge_date'] < date('Y-m-d')){
            return response()->json(['status'=>'ERR','message'=>'Status Can Not Be changed as older Transaction']);                        
        }
        
        $logResponse=\DbLogs::statusLog([
            'recharge_id'=>$request->rechargeId,
            'old_status'=>$existingRechargeData['status'],
            'new_status'=>$request->status
        ]);


        if($logResponse->getData()->status!='TXN'){
            return $logResponse;
        }

        tbl_recharge::where('id',$request->rechargeId)->update([
            'status'=>$request->status
        ]);

        $eventParams=[
            'data'=>'',
            'statusCode'=>'TXN',
            'statusMessage'=>'Recharge Status Updated'
        ];

        event (new \App\Events\ApiHit($eventParams));
        
        return response()->json(['status'=>'TXN','message'=>'Recharge Status Updated Successfully']);

    }

    //handle Company Crud Operation
    public function updateCompany(Request $request){

        $validator=Validator::make($request->all(),
        [
        'companyId'=>'nullable|exists:tbl_companies,id',
        'companyName' => [
            'required',
            Rule::unique('tbl_companies', 'company_name')
                  ->ignore(
                        (!empty($request->companyId) && !empty(tbl_company::where('id', $request->companyId)->first())) ? tbl_company::where('id', $request->companyId)->first()->company_name : "",
                            'company_name'
                ),
            ],
        ]               
        );

        if($validator->fails()){      
            return response()->json(['statusCode'=>'ERR','status'=>'Request Invalid','errors'=>$validator->errors()]);
        }

        $params=[
            'company_name'=> $request->companyName,
        ];
        
        
        if(!empty($request['companyId'])){
            tbl_company::where('id', $request->companyId)->update($params);
        }else{
            tbl_company::create($params);       
        }

        $eventParams=[
            'data'=>'',
            'statusCode'=>'TXN',
            'statusMessage'=>'Company Record Updated'
        ];

        event (new \App\Events\ApiHit($eventParams));

        return response()->json([
            'statusCode'=>'TXN',
            'status' => 'Company Details '.((!empty($request->companyId))?'Updated':'Added').' Successfully',
       ]);
            
    }

    public function listCompany(){

        $data= tbl_company::all();
            
        
        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    


    }

    //handle Commission Crud Operation
    public function updateCommission(Request $request){

        $validator=Validator::make($request->all(),
        [
        'commissionId'=>'nullable|exists:tbl_commissions,id',
        'companyId'=> ['required','integer','exists:tbl_companies,id',
                        Rule::unique('tbl_commissions','company_id')
                        ->ignore(( !empty($request->commissionId) && !empty(tbl_commission::where('id',$request->commissionId)->first()) ) ? tbl_commission::where('id',$request->commissionId)->first()->company_id :"",'company_id')
                      ],
        'commissionPercentage' => 'required|numeric'               
        ],[
            'companyId.unique' => 'Commission Already Generated for this Company',
        ]);

        if($validator->fails()){      
            return response()->json(['statusCode'=>'ERR','status'=>'Request Invalid','errors'=>$validator->errors()]);
        }

        $company = tbl_company::find($request->companyId);
        $params=[
            'commission_percentage' => $request->commissionPercentage
        ];
                
        if(!empty($request['commissionId'])){
            $company->commission()->update($params);            
        }else{   
            $company->commission()->create($params);
        }

        $eventParams=[
            'data'=>'',
            'statusCode'=>'TXN',
            'statusMessage'=>'Commission Record Updated'
        ];

        event (new \App\Events\ApiHit($eventParams));

        return response()->json([
            'statusCode'=>'TXN',
            'status' => 'Commission Details '.((!empty($request->commissionId))?'Updated':'Added').' Successfully',
       ]);
            
    }

    public function listCommission(Request $request){
        
        $data=tbl_commission::
        join('tbl_companies as cs','cs.id','=','tbl_commissions.company_id')
        ->select('tbl_commissions.*','cs.company_name')
        ->get();
                
        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    

    }    

    //Handle Report Generation Operation
    public function getSummaryData(Request $request){
    
        $validator=Validator::make($request->all(),
        [
        'fromDate' => 'date|date_format:Y-m-d',
        'toDate'=> 'date|date_format:Y-m-d',
        'status'=> 'In:Pending,Success,Failed,Credit,""'
        ]               
        );

        if($validator->fails()){      
            return response()->json(['statusCode'=>'ERR','status'=>'Request Invalid','errors'=>$validator->errors()]);
        }
        
        $fromDate = empty($request->fromDate) ?  date('Y-m-01') : date('Y-m-d', strtotime($request->fromDate));
        $toDate = empty($request->toDate) ?  date('Y-m-t') : date('Y-m-d', strtotime($request->toDate));


        $data=tbl_recharge::
        leftjoin('tbl_status_logs as tsl','tsl.recharge_id','=','tbl_recharges.id')
        ->whereRaw( " DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) >= '".$fromDate. "' and DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) <= '" .$toDate. "'")
        ->when(!empty($request->status), function ($query) use($request){
            return $query->whereRaw("IFNULL(tsl.new_status,tbl_recharges.status)='".$request->status."'");
        })
        ->selectRaw('tbl_recharges.company,tbl_recharges.mobile,tbl_recharges.amount,IFNULL(tsl.new_status,tbl_recharges.status) as status,DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) as date')
        ->get();

        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    
                    
    }

    public function getCompanySalesData(Request $request){

        $validator=Validator::make($request->all(),
        [
        'fromDate' => 'date|date_format:Y-m-d',
        'toDate'=> 'date|date_format:Y-m-d',
        'status'=> 'In:Pending,Success,Failed,Credit,""'
        ]               
        );

        if($validator->fails()){      
            return response()->json(['statusCode'=>'ERR','status'=>'Request Invalid','errors'=>$validator->errors()]);
        }
        
        $fromDate = empty($request->fromDate) ?  date('Y-m-01') : date('Y-m-d', strtotime($request->fromDate));
        $toDate = empty($request->toDate) ?  date('Y-m-t') : date('Y-m-d', strtotime($request->toDate));


        $data=tbl_recharge::
            leftjoin('tbl_status_logs as tsl','tsl.recharge_id','=','tbl_recharges.id')
            ->whereRaw( " DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) >= '".$fromDate. "' and DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) <= '" .$toDate. "'")
            ->when(!empty($request->status), function ($query) use($request){
                return $query->whereRaw("IFNULL(tsl.new_status,tbl_recharges.status)='".$request->status."'");
            })    
            ->selectRaw('
                    tbl_recharges.company,
                    DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) as date,
                    SUM(CASE when IFNULL(tsl.new_status,tbl_recharges.status) = "Pending" THEN tbl_recharges.amount ELSE 0 END) AS "Pending_Amount",
                    SUM(CASE when IFNULL(tsl.new_status,tbl_recharges.status) = "Success" THEN tbl_recharges.amount ELSE 0 END) AS "Success_Amount",
                    SUM(CASE when IFNULL(tsl.new_status,tbl_recharges.status) = "Credit" THEN tbl_recharges.amount ELSE 0 END) AS "Credit_Amount",
                    SUM(CASE when IFNULL(tsl.new_status,tbl_recharges.status) = "Failed" THEN tbl_recharges.amount ELSE 0 END) AS "Failed_Amount",
                    SUM(tbl_recharges.amount) as "Total_Amount",
                    (SUM(CASE WHEN IFNULL(tsl.new_status, tbl_recharges.status) = "Pending" THEN tbl_recharges.amount ELSE 0 END) / SUM(tbl_recharges.amount)) * 100 AS "Pending_Percentage",
                    (SUM(CASE WHEN IFNULL(tsl.new_status, tbl_recharges.status) = "Failed" THEN tbl_recharges.amount ELSE 0 END) / SUM(tbl_recharges.amount)) * 100 AS "Failed_Percentage",
                    (SUM(CASE WHEN IFNULL(tsl.new_status, tbl_recharges.status) = "Success" THEN tbl_recharges.amount ELSE 0 END) / SUM(tbl_recharges.amount)) * 100 AS "Success_Percentage",
                    (SUM(CASE WHEN IFNULL(tsl.new_status, tbl_recharges.status) = "Credit" THEN tbl_recharges.amount ELSE 0 END) / SUM(tbl_recharges.amount)) * 100 AS "Credit_Percentage"
            ')
            ->groupByRaw('tbl_recharges.company,DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date))')
            ->get();  

        foreach($data as $key=>$records){
            if($key==0){
                $data[$key]['Trend'] = '-';
            }else{
                $data[$key]['Trend'] = ((  $records->Success_Percentage - $data[$key-1]->Success_Percentage  ) > 0 ) ? 'UP' : 'DOWN';
            }
        }

        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    
                    
    }

    public function getSalesReport(Request $request){

        $validator=Validator::make($request->all(),
        [
        'fromDate' => 'date|date_format:Y-m-d',
        'toDate'=> 'date|date_format:Y-m-d',
        'status'=> 'In:Pending,Success,Failed,Credit,""'
        ]               
        );

        if($validator->fails()){      
            return response()->json(['statusCode'=>'ERR','status'=>'Request Invalid','errors'=>$validator->errors()]);
        }
        
        $fromDate = empty($request->fromDate) ?  date('Y-m-01') : date('Y-m-d', strtotime($request->fromDate));
        $toDate = empty($request->toDate) ?  date('Y-m-t') : date('Y-m-d', strtotime($request->toDate));
  
        $data=tbl_recharge::
        leftjoin('tbl_companies as tc','tc.company_name','=','tbl_recharges.company')
        ->leftjoin('tbl_commissions as tcs','tcs.company_id','=','tc.id')
        ->leftjoin('tbl_status_logs as tsl','tsl.recharge_id','=','tbl_recharges.id')
        ->whereRaw( " DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) >= '".$fromDate. "' and DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) <= '" .$toDate. "'")
        ->when(!empty($request->status), function ($query) use($request){
            return $query->whereRaw("IFNULL(tsl.new_status,tbl_recharges.status)='".$request->status."'");
        })
        ->selectRaw('
                tbl_recharges.company,
                DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date)) as date,
                (SUM(CASE WHEN IFNULL(tsl.new_status, tbl_recharges.status) = "Pending" THEN tbl_recharges.amount ELSE 0 END) /MAX(tcs.commission_percentage)) * 100 AS "profit"
        ')
        ->groupByRaw('tbl_recharges.company,DATE(IFNULL(tsl.created_at, tbl_recharges.recharge_date))')
        ->get();  


        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    
                    
    }

    public function getCommReport(Request $request){
        $data=tbl_commission::
        leftjoin('tbl_companies as tc','tc.id','=','tbl_commissions.company_id')
        ->select('tc.company_name','tbl_commissions.commission_percentage')
        ->get();

        return response()->json([
            'statusCode'=>'TXN',
            'status'=>'Data Fetched Successfully',
            'data' => $data,
        ]);    
                    
    }

}