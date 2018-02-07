<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Facades\DB;

class VenueScheduleDetail extends Model implements Transformable
{
    use TransformableTrait;

    // 字段白名单
    protected $fillable = [
        'schedule_id','start_time','end_time','class_id','week','section',
        'remark','operator_id','created_at',
    ];
    protected $table = 'admin_venue_schedule_detail';
    
    
    /**
     * 该模型是否被自动维护时间戳
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * 批量创建数据
     *
     * @param array $data 请求参数
     * @return bool
     * @author Red-Bo
     */
    public  function  BatchCreate(array $data)
    {
        $insert_data = [];
        foreach ($data as $k => $val) 
        {
            $insert_data[] = array_only($val, $this->fillable);
        }
        $DB = DB::table($this->table);
        return $DB->insert($insert_data);
    }
    
    /**
     * 通过课程表id获取课程表数据详情 | 数据需要从新组装一下在返回
     *
     * @param string $schedule_id
     * @return array
     * @author Red-Bo
     */
    public  function getVenueSchedules($schedule_id)
    {
        $result = [];
        $query = $this->query();
        $details   = $query->where('schedule_id', $schedule_id)
                     ->get()
                     ->toArray();
        if($details)
        {
            foreach ($details as $detail)
            {
                $result[$detail['week']][$detail['section']] = $detail;
            }
        }
        return $result;
    }
    

}
