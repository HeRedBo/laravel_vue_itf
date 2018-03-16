<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


class VenueScheduleCourseTime extends Model implements Transformable
{
    use TransformableTrait;
    protected  $table = 'admin_venue_schedule_course_times';

    
    /**
     * 该模型是否被自动维护时间戳
     * @var bool
     */
    public $timestamps = false;
    

    // 字段白名单
    protected $fillable = [
        'schedule_id','start_time','end_time','section'
        ,'operator_id','created_at',
    ];

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

    public function getScheduleCourseTime($schedule_id)
    {
        $where = [
            ['schedule_id','=', $schedule_id]
        ];
        $query = $this->query();
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        $details   = $query->get();
        $result = [];
        if($details)
        {
            $details = $details->toArray();
            foreach ($details as $detail)
            {
                $result[$detail['section']] = [
                    $detail['start_time'],
                    $detail['end_time']
                ];
            }
        }
        return $result;
    }






}
