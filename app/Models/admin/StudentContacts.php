<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
class StudentContacts extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = [];
    protected  $table = 'student_contacts';
    
    
}
