<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class RelationName extends Model implements Transformable
{
    use TransformableTrait;
    protected  $table = "admin_relation_name";
    //
}
