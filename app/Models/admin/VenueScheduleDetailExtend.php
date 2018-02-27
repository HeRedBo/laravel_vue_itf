<?php

namespace App\Models\admin;


use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class VenueScheduleDetailExtend extends Model implements Transformable
{
    use TransformableTrait;
    
    protected $table = 'admin_venue_schedule_detail_extend';
    /**
     * 该模型是否被自动维护时间戳
     * @var bool
     */
    public $timestamps = false;
    
    // 字段白名单
    protected $fillable = [
        'schedule_id','schedule_date','start_time','end_time','class_id','week','section',
        'remark','operator_id','created_at',
    ];
    
}
