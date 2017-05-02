<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
class AuthV2Command
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
        if(Sentinel::getUser())
            return $next($request);

        if($request->ajax())
            return response()->json([
                'status' => 'error',
                'need_login' =>true,
                'message' => 'Sesi Anda telah habis, silahkan login kembali terlebih dahulu!'
            ]);
        return redirect('login');
    }
}
