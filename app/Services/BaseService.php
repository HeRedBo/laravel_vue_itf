<?php

/**
 * 服务基类 处理公共的服务
 */
namespace App\Services;
use  Illuminate\Support\Facades\Auth;

class BaseService
{
    protected  $admin_id = 0;
    protected  $admin_name;
    protected  $admin_mobile;
    const GUARD = 'admin';

    public  function  __construct()
    {
        if(Auth::guard(self::GUARD)->check())
        {
            $this->admin_id     = auth(self::GUARD)->user()->id;
            $this->admin_name   = auth(self::GUARD)->user()->name;
            $this->admin_mobile = auth(self::GUARD)->user()->phone;
        }
    }



}