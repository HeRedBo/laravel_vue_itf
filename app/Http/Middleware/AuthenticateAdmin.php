<?php

namespace App\Http\Middleware;

use Closure;
use Route;
use App\Models\Admin\Permission;
use Illuminate\Support\Facades\Gate;
use App\Services\ApiServer\ApiResponse;

class AuthenticateAdmin
{
    protected $except = [
        'admin.login'
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routeName = starts_with(Route::currentRouteName(),'admin.') ? Route::currentRouteName() : 'admin.' . Route::currentRouteName();
        if(!Gate::check($routeName))
        {
            $response = new ApiResponse();
            return $response->withForbidden('你没有权限执行此操作');
        }
        return $next($request);
    }
}
