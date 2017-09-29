<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Role extends Model implements Transformable
{

    protected  $table = "admin_roles";
    use TransformableTrait;

    protected $dates = ['created_at', 'updated_at'];

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'display_name',
    ];


    /**
     * 属于该身份的用户。
     */
    public function users()
    {
        return $this->belongsToMany(Admin::class,'admin_user_role','role_id','user_id');
    }


    /**
     * 属于该角色的权限
     *
     * @return void
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission','role_id','permission_id');
    }

    


}
