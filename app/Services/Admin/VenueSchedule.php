<?php
/**
 * 道馆课程表服务类
 * Class VenueSchedule
 */
namespace App\Services\Admin;

use Illuminate\Http\Request;
use App\Services\ServiceFactory;
use App\Services\Common\Dictionary;

class VenueSchedule
{
    const VENUE_SCHEDULE_ON_STATUS  = 1; //课程表启用状态
    const VENUE_SCHEDULE_OFF_STATUS = 0; //禁用状态

    const WEEK_START = 1; // 周开始
    const WEEK_END   = 7; // 周结束

    public  function __construct()
    {
        
    }

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
            $course_times = $venue_schedule_course_time_model->getScheduleCourseTime($schedule_id);
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
        return  compact('schedule','venue_course','venue_schedules','course_times');
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
            $params['date'] = $first_day;
            if($first_day < $date)
            {
                $schedule = $this->searchSchedule($params);
            }
        }
        return $schedule;
    }
    
    protected  function  searchSchedule(array $params)
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
