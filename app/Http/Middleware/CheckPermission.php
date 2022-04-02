<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission_code)
    {
        if (!userHasPermission($permission_code)) {
            return abort(403, __('custom.permission_required', ['permission' => ucwords(str_ireplace('_', ' ', $permission_code))]));
        }

        if ($request->isMethod('PUT')) {
            // removing '_method' from request will avoid SQL exception that is raised by Laravel
            // when the model is updated via Eloquent in PUT request of any controller
            $request->request->remove('_method');
        }

        return $next($request);
    }
}
