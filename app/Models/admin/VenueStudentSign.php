<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueStudentSign extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'admin_venue_student_sign';
}
