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
		'name','federation_id','logo','logo_thumb','parent_id','card_prefix',
		'province_code','city_code','district_code','address','remark',
        'operator_id','operator_name'
    ];
    
    public function users()
    {
        return $this->belongsToMany(Admin::class,'admin_venue','venue_id','admin_id');
    }
    
    public  function  classes()
    {
        return $this->hasMany(Classes::class,'id','venue_id');
    }


    public function operator()
    {
        return $this->hasOne(Admin::class, 'id', 'operator_id');
    }
    
    public function getLogoAttribute($logo)
    {
       
        $manager = app('uploader');
        if(\Request::method() == 'PUT' || \Request::method() == "DELETE")
        {
            return $logo;
        }
       
        if ($logo)
        {
            return $manager->fileWebPath($logo);
            
        } else
        {
            return $manager->fileWebPath('files/avatar/default.png');
        }
        
    }



}
