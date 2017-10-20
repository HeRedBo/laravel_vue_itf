<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Admin\Permission;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
class AuthServiceProvider extends ServiceProvider
{
    
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];
    
    protected $except = [
        'admin',
        'admin.error'
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();

        //
         if(!empty($_SERVER['SCRIPT_NAME']) && strtolower($_SERVER['SCRIPT_NAME']) === 'artisan')
        {
            return false;
        }
        // acl 权限控制检查
        
        // 超级管理员 额外页面过滤权限检查
        $gate->before(function($user, $ability){
            if($user->id === 1 || in_array($ability, $this->except)) {
                return true;
            };
        });

        $permissions = Permission::with('roles')->get();
        foreach ($permissions as $permission)
        {
            $gate->define($permission->name, function($user) use ($permission)
            {
                return $user->hasPermission($permission);
            });
        }
    }
}
