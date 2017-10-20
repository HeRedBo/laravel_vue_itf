<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Admin\Permission;
use Illuminate\Support\Facades\Auth;

class IndexController extends ApiController
{
	
	public function __construnct() 
	{
		parent::__construnct();

	}
	/**
	 * index
	 */
	public function Index()
	{
	    $user = Auth::guard('admin')->user();
		return view('admin.index',[
		    'user' => $user
        ]);
	}


	public function menu() 
	{
		$adminMenuData = Request::get('adminMenuData');
		return $this->response->withData($adminMenuData);
	}

	/**
	 * 校验请求路由权限
	 * @return bool
	 */
	public function checkAcl()
	{
		$path        = Request::get('path');
        $routeName   = implode('.', explode('/',$path));
        $permission  = Permission::where('name',$routeName)->first();
        $check = true;
        if($permission)
        {
            $check  = Gate::check($routeName);
        }
        $res['status']= $check;
        return $this->response->withData($res);
	}
}

