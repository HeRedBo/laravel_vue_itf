<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class StudentNumberCard extends Model implements Transformable
{
    use TransformableTrait;

    protected  $table = 'admin_student_number_card';

    protected $fillable = [];

}
