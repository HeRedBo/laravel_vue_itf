<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Venue extends Model implements Transformable
{
    use TransformableTrait;

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
		'name','federation_id','logo','logo_thumb','parent_id',
		'province_code','city_code','district_code','address',
		'remark','operator_id',
    ];


    public function users()
    {
        return $this->belongsToMany(Admin::class,'admin_venue','venue_id','admin_id');
    }

}