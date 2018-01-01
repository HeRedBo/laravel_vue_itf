<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class StudentCard extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = [];
    protected  $table = 'student_card';



    public  function  card()
    {
        return $this->belongsTo(Card::class,'card_id','id');
    }

    public  function  student_number_card()
    {
        return $this->hasOne(StudentNumberCard::class, 'id', 'number_card_id');
    }



    
    
}
