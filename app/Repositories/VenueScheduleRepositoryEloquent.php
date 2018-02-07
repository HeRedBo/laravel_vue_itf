<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VenueScheduleRepository;
use App\Models\Admin\VenueSchedule;

/**
 * Class VenueScheduleRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueScheduleRepositoryEloquent extends BaseRepository implements VenueScheduleRepository
{
    protected  $fields = [
        'venue_id'     => 0,
        'course_count' => 0,
        'start_time'   => 1,
        'end_time'     => 1,
        'status'       => 1,
        'operator_id'  => 0,
    ];

    const WEEK_START = 1; // 周开始
    const WEEK_END   = 7; // 周结束


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return VenueSchedule::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public  function  create(array $data)
    {
        $venue_course      = $data['venue_course_form'];
        $course_times      = $data['course_times'];
        $venue_schedules   = $data['venue_schedules'];
        $course_count      = $venue_course['course_count'];

        $operator_id      =  auth('admin')->user()->id;

        $venue_schedule   = [
            'venue_id'     => $venue_course['venue_id'],
            'course_count' => $venue_course['course_count'],
            'start_time'   => date("Y-m-h 00:00:00",strtotime($venue_course['date_between'][0])),
            'end_time'     => date("Y-m-h 23:59:59",strtotime($venue_course['date_between'][1])),
            'status'       => $venue_course['status'],
            'operator_id'  => $operator_id,
        ];
        $venue_schedule_detail = [];
        // 组装
        $model = $this->model;
        // 设置字段默认值
        $model = $model->create($venue_schedule);
        $schedule_id = $model->id;
        // 组装新的数据
        $now = date("Y-m-d H:i:s");
        // 从新组装数据
        for ($w = self::WEEK_START; $w <= self::WEEK_END;$w ++ )
        {
            $venue_schedule = $venue_schedules[$w];
            if($venue_schedule)
            {
                for ($i=1; $i<= $course_count; $i++)
                {
                    if(isset($venue_schedule[$i]) && !empty($venue_schedule[$i]))
                    {
                        $course_time    = $course_times[$i];
                        $schedule = $venue_schedule[$i];
                        $remark = !empty($schedule['remark']) ? $schedule['remark'] : '';
                        $venue_schedule_detail[] = [
                            'schedule_id' => $schedule_id,
                            'class_id' => $schedule['class_id'],
                            'week' => $schedule['week'],
                            'section' => $schedule['section'],
                            'remark' => $remark,
                            'operator_id' => $operator_id,
                            'created_at' => $now,
                        ];
                    }
                }
            }
        }
        dd($venue_schedule_detail);














    }
}
