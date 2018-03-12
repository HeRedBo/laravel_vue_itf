<?php
namespace App\Services\Common;

/**
 * 后台管理系统公共服务类 这里的服务是一些全局的服务 方便调用
 *
 * Created by PhpStorm.
 * User: hehongbo
 * Date: 2018/3/12
 * Time: 下午10:14
 */

use  App\Models\Admin\Admin;
use  App\Services\BaseService;


class  Common extends  BaseService
{

    protected  $users = [];

    public function getUserVenueIds($user_id = 0)
    {
        $venueIds = [];
        if(empty($user_id))
            $user_id = $this->admin_id;
        $user = $this->getUser($user_id);
        if($user)
        {
            $user_venues = $user['venues'];
            $venueIds = array_column($user_venues,'id');
        }
        return $venueIds;
    }

    /**
     * 获取指定用户的隶属道馆信息
     *
     * @param $user_id
     * @return array|mixed
     * @author Red-Bo
     */
    public  function  getUserVenues($user_id = '')
    {
        $user_venues = [];
        if(empty($user_id))
            $user_id = $this->admin_id;
        $user = $this->getUser($user_id);
        if($user)
        {
            $user_venues = $user['venues'];
        }
        return $user_venues;
    }



    protected function  getUser($user_id)
    {
        if(isset($this->users[$user_id]))
            $user = $this->users[$user_id];
        else
        {
            $query = Admin::query();
            $user = $query->with('venues')->find($user_id);
            if($user)
            {
                $user = $user->toArray();
                $this->users[$user_id] = $user;
            }
        }
        return $user;
    }

}