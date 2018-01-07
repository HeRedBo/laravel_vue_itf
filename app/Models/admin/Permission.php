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

    /**
     * @return array
     */
    public function transform()
    {
        return [
             'id'           => (int) $this->id,
            'name'         => $this->name,
            'display_name' => $this->display_name,
            'description'  => $this->description,
            'level'        => $this->level,
            'icon'         => $this->icon,
            'parent_id'    => (int) $this->parent_id,
            'order_num'    => (int) $this->order_num,
            'icon'         => $this->icon,
            'is_show'      => (bool)  $this->is_show,
            'created_at'   => $this->created_at->toDateTimeString(),
            'updated_at'   => $this->updated_at->toDateTimeString()
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class,'role_permission','permission_id','role_id');
    }



}
