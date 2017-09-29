<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Permission extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'admin_permissions';

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = ['name','display_name','parent_id','icon','is_show','order_num'];


    public function roles()
    {
        return $this->belongsToMany(roles::class,'role_permission','permission_id','role_id');
    }

}
