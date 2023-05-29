<?php

namespace App\Helpers;

use App\Models\tbl_status_log;
use App\Models\apiLog;
use Illuminate\Support\Facades\Validator;


class LoggingHelper
{
    public function statusLog($LogParams){
        
    $validator=Validator::make($LogParams,
        [
        'recharge_id' => 'required|int|exists:tbl_recharges,id',
        'old_status'=> 'required|In:Pending,Success,Failed,Credit',
        'new_status'=> 'required|In:Pending,Success,Failed,Credit'
        ]               
    );

    if($validator->fails()){            
        return response()->json(['status'=>'ERR','errors'=>$validator->errors()]);
    }

    
    $logId = tbl_status_log::create($LogParams);

    return response()->json(['status'=>'TXN','id'=>$logId->id]);


    }

    public function apiLog($LogParams){

        $validator=Validator::make($LogParams,
        [
        'type' => 'required|string',
        'httpMethod'=> 'string',
        'httpCode'=> 'string',
        'url'=> 'string',
        'headers'=> 'required|string',
        'body'=> 'required|string',
        'requestId'=> 'integer',
        'ip_addr'=> 'required|string',
        ]               
        );
        
        if($validator->fails()){            
            return response()->json(['status'=>'ERR','errors'=>$validator->errors()]);
        }

        $logId = apiLog::create($LogParams);

        return response()->json(['status'=>'TXN','id'=>$logId->id]);


        
    }
}