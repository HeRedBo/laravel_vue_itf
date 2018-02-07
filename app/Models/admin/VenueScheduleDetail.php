<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueScheduleDetail extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = [];
    protected $table = 'admin_venue_schedule_detail';

}
