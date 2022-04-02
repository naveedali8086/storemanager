<?php

namespace App\Http\Middleware;

use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;

class DecodeRouteParams
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$route_param_names)
    {
        $hash_decoder = getHashidsObj();

        foreach ($route_param_names as $route_param_name) {

            $encrypted_val = $request->route($route_param_name);
            // As per the ' hashids/hashids' library's documentation, when decoding, output is always an array of numbers
            // (even if you encoded only one number)
            if ($encrypted_val){
                $request->route()->setParameter($route_param_name, $hash_decoder->decode($encrypted_val)[0]);
            }

        }

        return $next($request);
    }
}
