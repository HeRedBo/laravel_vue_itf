<?php
namespace App\Services\Admin;

use App\Services\BaseService;
/**
 * 学生信息服务类
 *
 * @package App\Services\Admin
 */

class StudentService extends BaseService
{
    
    
	public function signClassOptions(array $params)
	{
		$result   = [];
		$venue_id = isset($params['venue_id']) ? $params['venue_id'] : 0;
		$date     = $params['date'];
		if(!empty($venue_id))
		{
			$venueScheduleService = new VenueSchedule();
			$params =[
				'venue_id' => $venue_id,
				'date'     => $date
			];
			$venueSchedule  = $venueScheduleService->searchSchedule($params);
			if($venueSchedule)
			{
                $venue_schedules = $venueScheduleService->getVenueScheduleDetailData($venueSchedule, $params);
			}
		}
		return $result = [];
	}





}