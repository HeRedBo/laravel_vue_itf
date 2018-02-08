<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Carbon\Carbon;

/**
 * 后台管理员操作日志
 * 操作日志只有超级管理员可以查看
 */
class AdminLogger extends Model
{
    use TransformableTrait;

    protected $table = 'admin_logger';

    public $timestamps = false;


    public function users()
    {
        return $this->belongsTo(Admin::class,'user_id','id')->select('id','username','name');
    }

    public  function  venues()
    {
        return $this->belongsTo(Venue::class,'venue_id','id')->select('id','name');
    }
    
    public function getCreatedAtAttribute($date)
    {
        if (Carbon::now() < Carbon::parse(date("Y-m-d H:i:s",$date))->addDays(5)) {
           return Carbon::parse(date("Y-m-d H:i:s",$date))->diffForHumans();
        }
        return Carbon::parse(date("Y-m-d H:i:s",$date))->toDateTimeString(); 
    }








}
