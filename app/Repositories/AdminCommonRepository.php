<?php

namespace App\Repositories;

use App\Models\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * 后台公共仓库类
 * Class AdminCommonRepository
 * @package App\Repositories
 */
class AdminCommonRepository extends  BaseRepository
{
    const GUARD = 'admin';
    protected  $admin_id = 0;
    protected  $admin_name;
    protected  $admin_mobile;
    protected  $users = [];
    
    const DEFAULT_PAGE_SIZE = 15; // 默认的分页数
    public  function  __construct()
    {
        
        $app = new Application();
        parent::__construct($app);
        
        if(Auth::guard(self::GUARD)->check())
        {
            $this->admin_id     = auth(self::GUARD)->user()->id;
            $this->admin_name   = auth(self::GUARD)->user()->name;
            $this->admin_mobile = auth(self::GUARD)->user()->phone;
        }
    }
    
    //所有子类要实现这个方法
    public function model(){}
    
    /**
     * 获取用户的隶属道馆ID
     * @param string $user_id
     * @return array
     * @author Red-Bo
     */
    public  function  getUserVenueIds($user_id = '')
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
    

    /**
     * 获取道馆的各个模块的统计统计数
     *
     * @param int $is_self
     * @param array $where
     * @return int
     */
    public function getNumber($is_self = 1, $where = [])
    {
        $whereIn = [];
        if($is_self)
        {
            $venue_ids = $this->getUserVenueIds();
            if($venue_ids)
            {
                   $whereIn[] = ['venue_id', $venue_ids];
            }
        }
        $query = $this->model->query();
        if($whereIn)
        {
            foreach ($whereIn as $in)
            {
                $query->whereIn($in[0], $in[1]);
            }
        }
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }
        return $query->count();
    }
}
