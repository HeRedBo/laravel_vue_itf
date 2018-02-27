<?php
/**
 * 道馆课程表服务类
 * Class VenueSchedule
 */
namespace App\Services\Admin;

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

    public  function  getSchedulesInUse($date = null)
    {
        if(empty($data))
            $date = date("Y-m-d");
        
        $venue_schedules = $course_times = [];
        $schedule = $this->findSchedule($date);
        if($schedule)
        {
            $schedule_start_time = $schedule['start_time'];
            $schedule_end_time   = $schedule['end_time'];
            $course_count        = $schedule['course_count'];
            
            $schedule['date_between'] = [
                $schedule['start_time'],
                $schedule['end_time'],
            ];
            
            
            // 方便比较 统一转换为时间戳
            $start_time          = strtotime($schedule_start_time);
            $end_time            = strtotime($schedule_end_time);
            $schedules_details = $this->getScheduleDetail($schedule);
            $venue_schedules_data   = $schedules_details['venue_schedules'];
            $course_times   = $schedules_details['course_times'];
            $week_between_arr  = $this->getWeekDateArr($date);
            // 从新组装数据 已当前周的开始时间与结束时间进行判断从足数据
            for($w = self::WEEK_START; $w <= self::WEEK_END; $w++ )
            {
                $venue_schedules[$w] = [];
                $week_day      = $week_between_arr[$w];
                $week_day_time = strtotime($week_day);
                // 时间比较  
                if(
                    isset($venue_schedules_data[$w]) && 
                    ($start_time <= $week_day_time && $week_day_time <= $end_time)
                )
                {
                    for ($i = 1; $i <= $course_count; $i++)
                    {
                        $venue_schedules[$w][$i] = [];
                        if(isset($venue_schedules_data[$w][$i]))
                        {
                            $venue_schedules[$w][$i] = $venue_schedules_data[$w][$i];
                        }
                    }
                }
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
     *
     * @param array $schedule
     * @return mixed
     */
    public  function  getScheduleDetail(array $schedule)
    {
        $result = [];
        if(!empty($schedule))
        {
            $course_count = $schedule['course_count'];
            $schedule_id  =  $schedule['id'];
            $venue_schedule_detail_model = ServiceFactory::getModel("Admin\\VenueScheduleDetail");
            $details     = $venue_schedule_detail_model->getVenueSchedules($schedule_id);

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
                            // 时间 处理待定
                            $course_times[$i] = [
                                $detail[$i]['start_time'],
                                $detail[$i]['end_time']
                            ];
                        }
                    }
                }
            }
            
            //$course_times = $this->reBuildCourseTimes($course_times);
            $result = compact('venue_schedules','course_times');
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

    public  function  findSchedule($date)
    {
        $schedule = $this->findScheduleByDate($date);
        if(empty($schedule))
        {
            $week_between = getWeekBE($date);
            $first_day = $week_between[0];
            if($first_day < $date)
            {
                $schedule = $this->findScheduleByDate($first_day);
            }
        }
        return $schedule;
    }
    
    protected  function  findScheduleByDate($date)
    {
        $venueSchedule_model = ServiceFactory::getModel("Admin\\VenueSchedule");
        $query = $venueSchedule_model->query();
        $where = [
            ['start_time','<=', $date],
            ['end_time','>=',  $date],
            ['status','=', self::VENUE_SCHEDULE_ON_STATUS],
        ];
        foreach ($where as $v)
        {
            $query->where($v[0], $v[1], $v[2]);
        }
        $query->orderBy('id','desc');
        return $query->first()->toArray();

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


    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
