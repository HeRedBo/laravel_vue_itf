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
            ['head_name' => '时间'],
            ['head_name' => '节次'],
        ];
        $calendar = $this->getDateInfo($date);
        $head = array_merge($head, $calendar);
        $head = array_values($head);
        return $head;
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
            ];
            $tempDate = date("Y-m-d", strtotime($tempDate  . " + 1 day"));
        }
        return $calendar;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}
