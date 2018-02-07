<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueSchedule extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = ['venue_id','course_count','start_time','end_time','status','operator_id'];
    protected $table = 'admin_venue_schedule';



}
