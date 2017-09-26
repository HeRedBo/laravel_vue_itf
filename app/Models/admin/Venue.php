<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    //
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
		'name','federation_id','logo','logo_thumb','parent_id',
		'province_code','city_code','district_code','address',
		'remark','operator_id',
    ];



}
