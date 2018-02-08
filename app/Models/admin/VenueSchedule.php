<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueSchedule extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = ['venue_id','course_count','schedule_name','start_time','end_time','status','operator_id'];
    protected $table = 'admin_venue_schedule';
    
    public  function  operator()
    {
        return $this->belongsTo(Admin::class,'operator_id','id')
                    ->select(['id','name','logo','picture','email'.'phone']);
    }
}
