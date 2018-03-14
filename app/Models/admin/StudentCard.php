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
        return $this->belongsTo(Card::class,'card_id','id')
                    ->select(['id','name','venue_id','type','number',
                        'unit','card_price','explain','status','start_time','end_time'
                        ])
            ;
    }
    
    public function card_snap()
    {
        return $this->belongsTo(CardSnap::class,'card_snap_id','id')
            ->select(['id','name','venue_id','type','number',
                'unit','card_price','explain','status',
            ])
            ;
    }
    
    
    

    public  function  student_number_card()
    {
        return $this->hasOne(StudentNumberCard::class, 'id', 'number_card_id');
    }



    
    
}
