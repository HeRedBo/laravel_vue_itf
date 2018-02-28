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
    
    public  function  classes()
    {
        return $this->belongsTo(Classes::class,'class_id','id')
            ->select(['id','name']);
    }
    
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
    public  function getVenueSchedules($schedule_id,$params = [])
    {
        $result = [];
        $class_id = isset($params['class_id']) ? $params['class_id'] : 0;
        $where = [
            ['schedule_id','=', $schedule_id]
        ];
        
        if(!empty($class_id))
            $where[] = ['class_id','=', $class_id];
        $query = $this->query();
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        
        $details   = $query
                    ->with(['classes'])
                     ->get();
        if($details)
        {
            $details = $details->toArray();
            foreach ($details as $detail)
            {
                $detail['class_name'] = $detail['classes']['name'];
                unset($detail['classes']);
                $result[$detail['week']][$detail['section']] = $detail;
            }
        }
        return $result;
    }
}
