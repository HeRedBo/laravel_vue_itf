<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Classes extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'admin_classes';

    //protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [
        'venue_id','name','remark','operator_id'
    ];
    
    
    public  function  venues()
    {
        return $this->belongsTo(Venue::class,'venue_id','id');
    }
    
    public  function  operator()
    {
        return $this->belongsTo(Admin::class,'operator_id','id');
    }

}
