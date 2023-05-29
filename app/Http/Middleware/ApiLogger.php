<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        $logData = [
            'type' => 'API-REQUEST',
            'http_method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => is_array($request->header()) ? json_encode($request->header()) : $request->header(),
            'body'=> is_array($request->input()) ? json_encode($request->input()) : $request->input(),
            'ip_addr'=> request()->ip()
        ];
        
        $logResponse=\DbLogs::apiLog($logData);

        if($logResponse->getData()->status!='TXN'){
            return $logResponse;
        }
       
        $response = $next($request);
        

        $logData = [
            'type' => 'API-RESPONSE',
            'http_code' => $response->getStatusCode(),
            'url' => $request->fullUrl(),
            'headers' => is_array($response->headers->all()) ? json_encode($response->headers->all()) : $response->headers->all(),
            'body'=>  json_encode($response->getContent()),
            'request_id'=> $logResponse->getData()->id,
            'ip_addr'=> request()->ip()
        ];

        $logResponse=\DbLogs::apiLog($logData);

        if($logResponse->getData()->status!='TXN'){
            return $logResponse;
        }
        
        return $response;
        
    }
}