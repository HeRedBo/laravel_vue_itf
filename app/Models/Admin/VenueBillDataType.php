<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


class VenueBillDataType extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    protected $table = 'admin_venue_bill_data_type';

}
