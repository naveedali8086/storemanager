<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;

class DecodeRequestParams
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$request_params)
    {
        $hash_decoder = getHashidsObj();

        foreach ($request_params as $request_param) {

            $encrypted_val = $request->input($request_param);
            // As per the ' hashids/hashids' library's documentation, when decoding, output is always an array of numbers
            // (even if you encoded only one number)
            if ($encrypted_val){
                $request->request->set($request_param, $hash_decoder->decode($encrypted_val)[0]);
            }

        }

        return $next($request);
    }
}
