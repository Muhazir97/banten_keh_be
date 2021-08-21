<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
        ];

        // Config X-Frame-Options
        // $headers = [
        //     'X-Frame-Options' => 'SAMEORIGIN',
        //     'X-Frame-Options' => 'DENY',
        // ];
        
        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method": "OPTIONS"}', 200, $headers);
        }
        $response = $next($request);
        foreach ($headers as $key => $row) {
            $response->headers->set($key, $row);
        }
        return $response;
    }

}