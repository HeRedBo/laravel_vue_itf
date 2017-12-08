<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class StudentNumberCard extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

}
