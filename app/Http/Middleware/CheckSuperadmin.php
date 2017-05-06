<?php

namespace App\Http\Middleware;

use Closure;
use UserService;

class CheckSuperadmin
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
        if(UserService::isSuperadmin())
            return $next($request);

        if($request->ajax())
            return response()->json([
                'status' => 'error',
                'redirect_to' =>'/dashboard',
                'message' => 'Anda tidak memiliki akses untuk ini!'
            ]);
        abort(404);
    }
}
