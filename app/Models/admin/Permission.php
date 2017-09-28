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
    
    protected  $attributes = [
        'name' => '',
        'display_name' => '',
        'parent_id' => 0,
        'icon' => '',
        'is_show' => 0,
        'order_num' => 0,
    ];
    

}
