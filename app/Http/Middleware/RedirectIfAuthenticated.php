<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::guard($guard)->check() && ($request->path() ==  'admin/login')) 
        {
           if(!$request->ajax())
           {
                $url = $guard ? '/admin' : 'home';
                return redirect($url);
           }
           else
           {
                $data  = [
                     "code" => 200,
                    "message" => "系统检测到你已登录，正在为你跳转中...",
                ];
                return response()->json($data);
           }   
        }
        return $next($request);
    }
}
