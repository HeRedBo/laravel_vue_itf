<?php
/**
 * 道馆课程表服务类
 * Class VenueSchedule
 */
namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Services\ServiceFactory;
use App\Services\Common\Dictionary;
use  App\Services\BaseService;


class VenueSchedule extends  BaseService
{
    const VENUE_SCHEDULE_ON_STATUS  = 1; //课程表启用状态
    const VENUE_SCHEDULE_OFF_STATUS = 0; //禁用状态
    
    const WEEK_START = 1; // 周开始
    const WEEK_END   = 7; // 周结束
    
    /**
     * 获取课程表表头信息
     * @param $date
     * @return array
     * @author Red-Bo
     */
    public  function getScheduleHead($date)
    {
        $head = [
            ['date'=>'', 'head_name' => '时间','ori_date' => ''],
            ['date'=>'', 'head_name' => '节次','ori_date' => ''],
        ];
        $calendar = $this->getDateInfo($date);
        $head = array_merge($head, $calendar);
        $head = array_values($head);
        return $head;
    }

    public  function  getSchedulesInUse(Request $request)
    {
        $date = $request->get('date');
        if(empty($date))
            $date = date("Y-m-d");
        $venue_schedules = $course_times = [];
        $schedule = $this->findSchedule($request);
        if($schedule)
        {
            $schedule_start_time = $schedule['start_time'];
            $schedule_end_time   = $schedule['end_time'];
            $course_count        = $schedule['course_count'];
            $schedule_id         = $schedule['id'];
            $schedule['date_between'] = [
                $schedule['start_time'],
                $schedule['end_time'],
            ];
            
            // 方便比较 统一转换为时间戳
            $start_time          = strtotime($schedule_start_time);
            $end_time            = strtotime($schedule_end_time);
            $schedules_details   = $this->getScheduleDetail($schedule, $request);
            $venue_schedules_data   = $schedules_details['venue_schedules'];
            $venue_schedule_course_time_model = ServiceFactory::getModel("Admin\\VenueScheduleCourseTime");
            $course_times      = $venue_schedule_course_time_model->getScheduleCourseTime($schedule_id);
            $week_between_arr  = $this->getWeekDateArr($date);
            $venue_schedules_extend_data = $this->getVenueScheduleExtend($schedule_id, $request);
            // 从新组装数据 已当前周的开始时间与结束时间进行判断从足数据
            for($w = self::WEEK_START; $w <= self::WEEK_END; $w++ )
            {
                $venue_schedules[$w] = [];
                $week_day      = $week_between_arr[$w];
                $week_day_time = strtotime($week_day);
                for ($i = 1; $i <= $course_count; $i++)
                {
                    $venue_schedules[$w][$i] = [];
                    if(
                    (isset($venue_schedules_data[$w]) &&
                        ($start_time <= $week_day_time && $week_day_time <= $end_time))
                    ||
                    isset($venue_schedules_extend_data[$w])
                    )
                    {
                        if(isset($venue_schedules_data[$w][$i]))
                        {
                            $venue_schedules[$w][$i] = $venue_schedules_data[$w][$i];
                        }
                        // 如果存在补充数据则以补充数据表数据为准
                        if(isset($venue_schedules_extend_data[$w][$i]) && $venue_schedules_extend_data[$w][$i]['class_id'] > 0)
                        {
                            $venue_schedules[$w][$i] = $venue_schedules_extend_data[$w][$i];
                        }
                    }
                }
                // 时间比较
            }
        }
        return  compact('schedule','venue_schedules','course_times');
    }
    
    public function venueScheduleData()
    {
        
    }
    
    /**
     * 获取周日区间数组
     * @param  string $date 日期
     * @return array
     */
    private function  getWeekDateArr($date)
    {
        $week_between = getWeekBE($date);
        $first_day    = $week_between[0];
        $last_day     = $week_between[1];
        $w_first      = self::WEEK_START; // 周开始时间 以 1 开始
        $tempDate     = $first_day;
        $tempW        = $w_first;
        $result       = [];
        
        while ($tempDate <= $last_day) 
        {
            $result[$tempW] = $tempDate;
            $tempW ++;
            $tempDate = date("Y-m-d", strtotime($tempDate ."+ 1 day"));
        }
        return $result;
    }
    
    
    /**
     * 获取一个课程表的课程详情
     * @param array   $schedule
     * @param Request $request
     * @return array
     * @author Red-Bo
     */
    public  function  getScheduleDetail(array $schedule, Request $request)
    {
        $result = [];
        if(!empty($schedule))
        {
            
            $course_count = $schedule['course_count'];
            $schedule_id  =  $schedule['id'];
            $venue_schedule_detail_model = ServiceFactory::getModel("Admin\\VenueScheduleDetail");
            $params = $request->all();
            $details     = $venue_schedule_detail_model->getVenueSchedules($schedule_id,$params);
            $venue_schedules = $course_times = [];
            // 组装课程列表数据与课程时间数据
            for($w = self::WEEK_START; $w <= self::WEEK_END; $w++ )
            {
                $venue_schedules[$w] = [];
                if(isset($details[$w]))
                {
                    $detail = $details[$w];
                    for ($i = 1; $i <= $course_count; $i++)
                    {
                        $venue_schedules[$w][$i] = [];
                        if(isset($detail[$i]))
                        {
                            $venue_schedules[$w][$i] = $detail[$i];
                        }
                    }
                }
            }
            $result = compact('venue_schedules');
        }
        return $result;
    }


    /**
     * 获取日期时间
     * @param string $date
     * @return array
     * @author Red-Bo
     */
    public  function  getDateInfo($date)
    {
        $service = ServiceFactory::getService("Common\\Lunar");
        $date_week_between = getWeekBE($date);
        $first_day = $date_week_between[0];
        $last_day  = $date_week_between[1];
        $tempDate  = $first_day;
        $calendar  = [];
        $week_map = Dictionary::WeekMap();
        while ($tempDate <= $last_day)
        {
            $calendarDate = $service->getFestival($tempDate);
            $tempTime = strtotime($tempDate);
            $week_mame    = $calendarDate ? $calendarDate : $week_map[date("w", $tempTime)];
            $calendar[$tempDate] = [
                'date'      => date("m-d", $tempTime),
                'head_name' => $week_mame,
                'ori_date'  => $tempDate
            ];
            $tempDate = date("Y-m-d", strtotime($tempDate  . " + 1 day"));
        }
        return $calendar;
    }

    public  function  findSchedule($request)
    {
        $date = $request->get('date');
        if(empty($data))
            $date  = date("Y-m-d");
        $params = $request->all();
        $schedule = $this->searchSchedule($params);
        if(empty($schedule))
        {
            $week_between = getWeekBE($date);
            $first_day = $week_between[0];
            $last_day  = $week_between[1];
            $params['date'] = $first_day;
            if($first_day < $date)
            {
                $schedule = $this->searchSchedule($params);
            }
            if(empty($schedule) && $last_day > $date)
            {
                $params['date'] = $last_day;
                $schedule = $this->searchSchedule($params);
            }
        }
        return $schedule;
    }


    // ========================  签到日历表相关程序(start) ==================================;
    public  function getSignCalendar(Request $request)
    {
        // 获取课程表数据
        $date = $request->get('date');
        if(empty($date))
        {
            $date = date("Y-m-d");
        }

        $mouth_between = getMonthBE($date);
        $venue_id = $request->get('venue_id');
        $class_id = $request->get('class_id');
        $params = [
            'venue_id' => $venue_id,
            'start_time' => $mouth_between[0],
            'end_time' => $mouth_between[1],
        ];
        $venueScheduleModel = ServiceFactory::getModel("Admin\\VenueSchedule");
        $schedules = $venueScheduleModel->getDateBetweenSchedule($params);
        $params['class_id'] = $class_id;
        $venue_schedules = [];
        if($schedules)
        {
            $venue_schedules = $this->buildScheduleData($schedules,$params);
        }
        return compact('venue_schedules');
    }

    public  function  buildScheduleData(array $schedules, $params = [])
    {
        $venue_schedules  = [];
        if($schedules)
        {
            $course_count_arr = array_column($schedules,'course_count');
            $max_course_count = max($course_count_arr);
            $calendarMaps     = $this->calendarScheduleMaps($schedules, $params);
            $schedule_ids     = array_unique(array_column($schedules,'id'));
            $params['course_count'] = $max_course_count;
            $schedule_details = $this->getScheduleDetails($schedule_ids, $params);
            $venue_schedules_extend_data = $this->getVenueSchedulesExtend($schedule_ids, $params);

            $start_date_time = $params['start_time'];
            $end_date_time   = $params['end_time'];
            $temp_date_time  = $start_date_time;

            // 从新组装数据 已当前周的开始时间与结束时间进行判断从足数据
            while ($temp_date_time <= $end_date_time)
            {
                $mouth_temp_time = strtotime($temp_date_time);
                $mouth_temp_date = date("Y-m-d", $mouth_temp_time);
                $w = date('w', $mouth_temp_time);
                if($w == 0)
                    $w = 7;
                $schedule_id = isset($calendarMaps[$mouth_temp_date]) ?$calendarMaps[$mouth_temp_date] : 0;
                $start_time = $end_time = 0;
                if($schedule_id)
                {
                    $schedules = array_column($schedules,NULL,'id');
                    $schedule  = $schedules[$schedule_id];
                    $schedule_start_time = $schedule['start_time'];
                    $schedule_end_time   = $schedule['end_time'];
                    // 方便比较 统一转换为时间戳
                    $start_time  = strtotime($schedule_start_time);
                    $end_time    = strtotime($schedule_end_time);

                    $venue_schedule_extend_data = isset($venue_schedules_extend_data[$schedule_id]) ? $venue_schedules_extend_data[$schedule_id] : [];
                    $schedule_detail            = isset($schedule_details[$schedule_id]) ? $schedule_details[$schedule_id] : [];
                }
                $i = getDateWeekOrder($temp_date_time);
                $temp_data = [
                    'date' => date('j',$mouth_temp_time)
                ];
                $venue_schedules[$w][$i] = $temp_data;
                if(
                    $schedule_id &&
                    (isset($schedule_detail[$w]) &&
                        ($start_time <= $mouth_temp_time && $mouth_temp_time <= $end_time))
                )
                {
                    if(isset($schedule_detail[$w][$i]))
                    {
                        $temp_data = array_merge($venue_schedules[$w][$i], $schedule_detail[$w][$i]);
                        $venue_schedules[$w][$i] = $temp_data;
                    }
                }

                if($schedule_id && isset($venue_schedule_extend_data[$w]) && ($start_time <= $mouth_temp_time && $mouth_temp_time <= $end_time))
                {
                    // 如果存在补充数据则以补充数据表数据为准
                    if(isset($venue_schedule_extend_data[$w][$i]) && $venue_schedule_extend_data[$w][$i]['class_id'] > 0)
                    {
                        $temp_data = array_merge($temp_data,$venue_schedule_extend_data[$w][$i]);
                        $venue_schedules[$w][$i] = $temp_data;
                    }
                }
                $temp_date_time = date("Y-m-d", strtotime("{$temp_date_time} + 1 day"));
            }
        }
        return $venue_schedules;
    }

    private function calendarScheduleMaps($schedules, $params)
    {
        $first_day = $params['start_time'];
        $last_day  = $params['end_time'];
        $maps      = [];
        foreach ($schedules as $k => $v)
        {
            $start_time = $v['start_time'];
            $end_time   = $v['end_time'];
            $maps[$v['id']] = [
                $start_time,
                $end_time
            ];
        }

        $tempDate     = $first_day;
        $calendarMaps        = [];

        while ($tempDate <= $last_day)
        {
            $key = 0;
            foreach ($maps as $k => $v)
            {
                if($v[0] <= $tempDate && $tempDate <= $v[1])
                {
                    $key = $k;
                    break;
                }
            }
            if($key)
            {
                $temp_key = date("Y-m-d",strtotime($tempDate));
                $calendarMaps[$temp_key] = $key;
            }
            $tempDate = date("Y-m-d 00:00:00", strtotime($tempDate ."+ 1 day"));
        }
        return $calendarMaps;

    }

    protected function getScheduleDetails(array $schedule_ids, array $params)
    {
        $venue_schedule_detail_model = ServiceFactory::getModel("Admin\\VenueScheduleDetail");
        $details     = $venue_schedule_detail_model->getVenueSchedulesByIds($schedule_ids,$params);
        $venue_schedules  = [];
        // 组装课程列表数据与课程时间数据
        if($details)
        {
            $course_count = $params['course_count'];
            foreach ($details as $k => $detail)
            {
                if(!isset($venue_schedules[$k]))
                {
                    $venue_schedules[$k] = [];
                }
                for($w = self::WEEK_START; $w <= self::WEEK_END; $w++ )
                {
                    $venue_schedules[$k][$w] = [];
                    if(isset($detail[$w]))
                    {
                        $data = $detail[$w];
                        for ($i = 1; $i <= $course_count; $i++)
                        {
                            $venue_schedules[$k][$w][$i] = [];
                            if(isset($data[$i]))
                            {
                                $venue_schedules[$k][$w][$i] = $data[$i];
                            }
                        }
                    }
                }
            }
        }
        return  $venue_schedules;
    }

    protected  function  getDateBetweenArr(array $params)
    {
        $first_day = $params['start_time'];
        $last_day  =  $params['end_time'];
        $tempDate  = $first_day;
        $result    = [];

        while ($tempDate <= $last_day)
        {
            $week = date("w", strtotime($tempDate));
            if($week == 0)
                $week = 7;
            $tempDate =  date("Y-m-d", strtotime($tempDate));
            $result[$week] = $tempDate;
            $tempDate = date("Y-m-d 00:00:00", strtotime($tempDate ."+ 1 day"));
        }
        return $result;
    }

    public  function  getVenueSchedulesExtend($schedule_ids ,$params)
    {
        $class_id   = isset($params['class_id']) ? $params['class_id'] : 0;
        $start_time = $params['start_time'];
        $end_time   = $params['end_time'];

        $date_between = [
            $start_time, $end_time
        ];
        $where = $whereIn =  [];
        $whereIn[] = ['schedule_id', $schedule_ids];
        if(!empty($class_id))
            $where[] = ['class_id','=', $class_id];
        $model  = ServiceFactory::getModel("Admin\\VenueScheduleDetailExtend");
        $query = $model->query()
                       ->with(['classes']);
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        if($whereIn)
        {
            foreach ($whereIn as $v)
            {
                $query->whereIn($v[0], $v[1]);
            }
        }

        $query->whereBetween("schedule_date",$date_between);
        $data = $query->get()->toArray();
        $result = [];
        if($data)
        {
            foreach ($data as $k => $v)
            {
                $v['class_name'] = $v['classes']['name'];
                unset($v['classes']);
                $result[$v['schedule_id']['week']][$v['section']] = $v;
            }
        }
        return $result;
    }

    // ========================  签到日历表相关程序( end ) ==================================;

    public function  searchSchedule(array $params)
    {
        $date     = isset($params['date']) ? $params['date'] : date("Y-m-d");
        $venue_id = isset($params['venue_id']) ? (int) $params['venue_id'] : 0;
        $venueSchedule_model = ServiceFactory::getModel("Admin\\VenueSchedule");
        $query = $venueSchedule_model->query();
        $where = [
            ['start_time','<=', $date],
            ['end_time','>=',  $date],
            ['status','=', self::VENUE_SCHEDULE_ON_STATUS],
        ];
        if(!empty($venue_id))
            $where[] = ['venue_id','=', $venue_id];
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        
        $query->orderBy('id','desc');
        $result = [];
        $data = $query->first();
        if($data)
            $result = $data->toArray();
        return $result;
    }

    /**
     * 校验签到参数是否课表参数是否有效
     *
     * @param $params
     * @return mixed
     */
    public  function  getSignDataValidate($params)
    {
        $week = getDateWeek($params['date']);
        $schedule_id = $params['schedule_id'];
        $where = [
            ['schedule_id','=', $schedule_id],
            ['class_id','=', $params['class_id']],
            ['week','=', $week],
            ['section','=',  $params['section']],
        ];
        $venue_schedule_detail_model = ServiceFactory::getModel("Admin\\VenueScheduleDetail");
        $query = $venue_schedule_detail_model->query();
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        $result = $query->get();
        if($result)
        {
            $result = $result->toArray();
        }

        if(empty($result))
        {
            $where[] = ['schedule_date','=', date("Y-m-d",strtotime($params['date']))];
            $venueSchedule_model = ServiceFactory::getModel("Admin\\VenueScheduleDetailExtend");
            $query2 = $venueSchedule_model->query();
            foreach ($where as $v)
            {
                $query2->where($v[0], $v[1], $v[2]);
            }

            $result = $query2->get();
            if($result)
            {
                $result = $result->toArray();
            }
        }
        return $result;











    }


    protected  function  reBuildCourseTimes($course_times)
    {
        $result = [];
        foreach ($course_times as $k => $v)
        {
            foreach ($v as $kk => $vv) {
                $date_time = date("Y-m-d H:i:s", strtotime($v[0]));
                $result[$k][$kk] = DateTimeToGmt($date_time);
            }
        }
        return $result;
    }
    
    /**
     * 获取课程表的补充数据
     * @param int $schedule_id  课程表ID
     * @param string $request
     * @return array
     * @author Red-Bo
     */
    protected  function getVenueScheduleExtend($schedule_id , $request)
    {
        $class_id = $request->get('class_id');
        $date = $request->get('date');
        $where = [
            ['schedule_id', '=', $schedule_id],
        ];
        if(!empty($class_id))
            $where[] = ['class_id','=', $class_id];
        if(empty($date))
            $date =date("Y-m-d");
        
        $date_between = getWeekBE($date);
        $model =   $venueSchedule_model = ServiceFactory::getModel("Admin\\VenueScheduleDetailExtend");
        $query = $model->query()
                ->with(['classes']);
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        $query->whereBetween("schedule_date",$date_between);
        $data = $query->get()->toArray();
        $result = [];
        if($data)
        {
            foreach ($data as $k => $v)
            {
                $v['class_name'] = $v['classes']['name'];
                unset($v['classes']);
                $result[$v['week']][$v['section']] = $v;
            }
        }
        return $result;
    }
}
