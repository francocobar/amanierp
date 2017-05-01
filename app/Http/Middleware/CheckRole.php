<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use UserService;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $role = UserService::getRoleByUser();
        // return $role;
        if($role != null) {
            if(strtolower($role->slug) == 'manager' ||  strtolower($role->slug) == 'superadmin') {
                return $next($request);
            }
        }
        abort(404);
    }
}
