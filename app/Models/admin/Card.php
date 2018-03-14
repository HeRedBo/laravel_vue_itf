<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Card extends Model implements Transformable
{
    use TransformableTrait;

    protected  $table = "admin_cards";
    protected $fillable = [
    	'venue_id','name','number','unit','card_price','explain','status','operator_id',
    ];
    
    public  function  venues()
    {
        return $this->belongsTo(Venue::class,'venue_id','id');
    }
    
    public  function  operator()
    {
        return $this->belongsTo(Admin::class,'venue_id','id');
    }



}
