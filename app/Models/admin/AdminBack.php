<?php

namespace App\Models\Admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


class Admin extends Authenticatable implements Transformable
{
    use TransformableTrait;

    use Notifiable;
    
    protected $table = 'admin';
    protected $admin_user_role = 'admin_user_role';
    protected $admin_venue     = 'admin_venue';
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','name','email','password','picture','phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


     /**
     * 属于该用户的身份。
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class,'admin_user_role','user_id','role_id');
    }


    /**
     * 查询该用户所属的道馆
     *
     */
    public function venues()
    {
        return $this->belongsToMany(Venue::class,'admin_venue','admin_id','venue_id');
    }


    /**
     *  判断用户是具有某权限
     *
     * @param mixed $permisson
     * @return boolean
     * @author RddBo
     */
    public function hasPermission($permisson) 
    {
        if(is_string($permisson)){
            $permisson = Permission::where('name', $permisson)->first();
            if(!$permisson) return false;
        }
        return $this->hasRole($permisson->roles);
    }

    /**
     * 判断的用户是否具有某个群权限
     * 
     * @param [type] $role
     * @return boolean
     * @author RddBo
     */
    public function hasRole($role) 
    {       
       
        if(is_string($role)) 
        {
            return $this->roles->contains('name',$ole);
        }

        return !!$role->intersect($this->roles)->count();
    }



    /**
     * 角色整体添加与修改
     *
     * @param array $roleId
     * @return bool
     */
    public function giveRoleTo(array $roleId)
    {
        $this->roles()->detach();
        $roles = Role::whereIn('id', $roleId)->get();
        $admin_roles = [];
        foreach($roles as $v)
        {
            $admin_roles[] = [
                'user_id' => $this->id,
                'role_id'  => $v->id
            ];
        }
        DB::table($this->admin_user_role)->insert($admin_roles);
        return true;
    }

    /**
     * 给用户分配权限
     * @param  mixed  $role 角色数据对象
     * @return viod
     */
    public function assignRole($role)
    {
        return $this->roles()->save($role);
    }

    public function giveVenueTo(array $venueId)
    {
        $this->venues()->detach();
        $venues = Venue::whereIn('id', $venueId)->get();
        $admin_venues = [];
        foreach($venues as $v)
        {
            $admin_venues[] = [
                'admin_id' => $this->id,
                'venue_id' => $v->id,
            ];
        }
        DB::table($this->admin_venue)->insert($admin_venues);
        return true;
    }

    public function assignVenue($venue)
    {
        $this->venues()->save($venue);
    }

    public function getPictureAttribute($pic)
    {
        $manager = app('uploader');
        if(\Request::method() == 'PUT' || \Request::method() == "DELETE")
        {
            return $pic;
        }
        if ($pic) 
        {
            return $manager->fileWebPath($pic);
        }
        else
        {
            return $manager->fileWebPath('files/avatar/default.png');
        }
    }

}
