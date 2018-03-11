<?php
namespace App\Services\Admin;

use App\Services\BaseService;
use App\Services\ServiceFactory;
use App\Services\Common\Dictionary;

/**
 * 学生信息服务类
 *
 * @package App\Services\Admin
 */

class StudentService extends BaseService
{

    protected  $sign_before_start_time = 60 * 30; // 需要在课程的前 半哥小时才可以签到

    // table
    protected  $tb_students = 'students';
    protected  $tb_student_class = 'student_class';

	public function signClassOptions(array $params)
	{
		$result   = [];
		$venue_id = isset($params['venue_id']) ? $params['venue_id'] : 0;
		$date     = $params['date'];
        $venue_schedules = [];
		if(!empty($venue_id))
		{
			$venueScheduleService = new VenueSchedule();
			$params =[
				'venue_id' => $venue_id,
				 'date'    => $date,
			];
			$venueSchedule  = $venueScheduleService->searchSchedule($params);
			if($venueSchedule)
			{
                $week = getDateWeek($date);
                $params['week'] = $week;
                $venue_schedules = $venueScheduleService->getVenueScheduleDetailData($venueSchedule, $params);
                if($venue_schedules && isset($venue_schedules[$week]))
                {
                    $schedule_details = $venue_schedules[$week];
                    if($schedule_details)
                    {
                        $schedule_id = $venueSchedule['id'];
                        $venue_schedule_course_time_model = ServiceFactory::getModel("Admin\\VenueScheduleCourseTime");
                        $course_times      = $venue_schedule_course_time_model->getScheduleCourseTime($schedule_id);
                        // 时间比较
                        $now_time = time();

                       // $schedule_details = array_column($schedule_details,NULL,'section');

                        $result = [];

                        foreach ($course_times as $k =>  $course_time)
                        {
                            $start_time = strtotime($course_time[0]);
                            $end_time   = strtotime($course_time[1]);
                            $temp_time  = $now_time + $this->sign_before_start_time;

//                            if($now_time > $start_time && $temp_time >= $end_time )
//                            {
//                                $result[$schedule_details[$k]['section']] = $schedule_details[$k];
//                            }

                            $result[$k] = $schedule_details[$k];
                        }
                    }
                }
			}
		}

		$options = [];
		if($result)
        {
            foreach ($result as $v)
            {
                if(empty($v))
                    continue;
                $options[] = [
                    'value' => $v['section']. '_'. $v['class_id'],
                    'label' => "【{$v['section']}】". $v['class_name']
                ];
            }
        }
        return $options;
	}


	public  function  getStudentSignData(array $student_ids, array $params)
    {
        $venue_id = isset($params['venue_id']) ? $params['venue_id'] : 0;
        $section  = isset($params['section']) ? $params['section'] : 0;
        $class_id = isset($params['class_id']) ? $params['class_id'] : 0;
        $date     = isset($params['date']) ? $params['date'] : 0;
        $data  =  $result  = [];
        if($venue_id)
        {
            $where = [];
            $where[] = ['venue_id','=', $venue_id];
            if(!empty($date))
                $where[] = ['sign_date','=', $date];
            if(!empty($section))
                $where[] = ['section','=', $section];
            if(!empty($class_id))
                $where[] = ['class_id','=', $class_id];
            $model = ServiceFactory::getModel("Admin\\VenueStudentSign");
            $query = $model->query();
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
            $query->whereIn('student_id', $student_ids);
            $query->with(['classes']);
            $data = $query->get();
            if($data)
            {
                $data = $data->toArray();
                $signStatusMap = Dictionary::studentSignStatusMap();
                $signTypeMap = Dictionary::signTypeMap();
                foreach ($data as &$v)
                {
                    $classes = isset($v['classes']) ? $v['classes'] : [];
                    $v['class_name'] =isset( $classes['name']) ? "【{$v['section']}】". $classes['name'] : '';
                    $v['status_name'] = isset($signStatusMap[$v['status']]) ? $signStatusMap[$v['status']] : '';
                    $v['type_name'] = isset($signTypeMap[$v['status']]) ? $signTypeMap[$v['status']] : '';
                    unset($v['classes']);
                    $result[$v['student_id']][] = $v;
                }
            }
        }
        return $result;
    }





}