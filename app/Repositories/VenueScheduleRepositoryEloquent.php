<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Repositories\AdminCommonRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\VenueScheduleRepository;
use App\Models\Admin\VenueSchedule;
use App\Models\Admin\VenueScheduleDetail;
use App\Models\Admin\VenueScheduleCourseTime;
use Illuminate\Support\Facades\Event;
use App\Events\AdminLogger;
use App\Services\ServiceFactory;


/**
 * Class VenueScheduleRepositoryEloquent
 * @package namespace App\Repositories;
 */
class VenueScheduleRepositoryEloquent extends AdminCommonRepository implements VenueScheduleRepository
{
    const WEEK_START = 1; // 周开始
    const WEEK_END   = 7; // 周结束
    
    const VENUE_SCHEDULE_ON_STATUS  = 1;// 课程表启用状态
    const VENUE_SCHEDULE_OFF_STATUS = 0;// 禁用状态
    
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
    
    /**
     * 创建课程表数据
     * @param array $data 请求数据
     * @return array|void
     * @author Red-Bo
     */
    public  function  create(array $data)
    {
        try
        {
            DB::beginTransaction();
            $venue_course      = $data['venue_course_form'];
            $course_times      = $data['course_times'];
            $venue_schedules   = $data['venue_schedules'];
            $course_count      = $venue_course['course_count'];
            $operator_id       = $this->admin_id;
            //保存之前要校验一下课程表时间是否合法
            $start_time = date("Y-m-d 00:00:00",strtotime($venue_course['date_between'][0]));
            $end_time   = date("Y-m-d 23:59:59",strtotime($venue_course['date_between'][1]));
            $params = [
                'venue_id'     => $venue_course['venue_id'],
                'start_time'   => $start_time,
                'end_time'     => $end_time,
                'id'           => isset($venue_course['id']) ? $venue_course['id'] : 0,
            ];

            //保存之前要校验一下课程表时间是否合法
            $result = $this->model->getDateBetweenSchedule($params);
            if($result)
            {
                return error('课程有效期出现重叠，请检查');
            }
            $venue_schedule   = [
                'venue_id'      => $venue_course['venue_id'],
                'course_count'  => $venue_course['course_count'],
                'schedule_name' => $venue_course['schedule_name'],
                'start_time'    => date("Y-m-d 00:00:00",strtotime($venue_course['date_between'][0])),
                'end_time'      => date("Y-m-d 23:59:59",strtotime($venue_course['date_between'][1])),
                'status'        => $venue_course['status'],
                'operator_id'   => $operator_id,
            ];

            $model = $this->model;
            // 设置字段默认值
            $model = $model->create($venue_schedule);
            $schedule_id = $model->id;
            // 组装新的数据
            $now = getNow();
            $venue_schedule_detail = $venue_schedule_course_times = [];
            // 从新组装数据
            for ($w = self::WEEK_START; $w <= self::WEEK_END;$w ++ )
            {
                $venue_schedule = isset($venue_schedules[$w]) ? $venue_schedules[$w] : [];
                if($venue_schedule)
                {
                    for ($i=1; $i<= $course_count; $i++)
                    {
                        if(isset($venue_schedule[$i]) && !empty($venue_schedule[$i]))
                        {
                            $course_time = $course_times[$i];
                            $schedule    = $venue_schedule[$i];
                            $remark      = !empty($schedule['remark']) ? $schedule['remark'] : '';
                            $venue_schedule_detail[] = [
                                'schedule_id' => $schedule_id,
                                'class_id'    => $schedule['class_id'],
                                'start_time'  =>  date("H:i:s",strtotime($course_time[0])),
                                'end_time'    => date("H:i:s",strtotime($course_time[1])),
                                'week'        => $schedule['week'],
                                'section'     => $schedule['section'],
                                'remark'      => $remark,
                                'operator_id' => $operator_id,
                                'created_at'  => $now,
                            ];

                            $venue_schedule_course_times[$schedule['section']] = [
                                'schedule_id' => $schedule_id,
                                'section'     => $schedule['section'],
                                'start_time'  =>  date("H:i:s",strtotime($course_time[0])),
                                'end_time'    => date("H:i:s",strtotime($course_time[1])),
                                'operator_id' => $operator_id,
                                'created_at'  => $now,
                            ];
                        }
                    }
                }
            }

            if($venue_schedule_detail)
            {
                $schedule_detail_model = new VenueScheduleDetail();
                $schedule_detail_model->BatchCreate($venue_schedule_detail);
            }
            if($venue_schedule_course_times)
            {
                $schedule_course_times_model = new  VenueScheduleCourseTime();
                $schedule_course_times_model->where('schedule_id', $schedule_id)->delete();
                $schedule_course_times_model->BatchCreate($venue_schedule_course_times);
            }
            Event::fire(new AdminLogger($venue_course['venue_id'],'create',"添加课程表【{$venue_course['schedule_name']}】"));
            DB::commit();
            return success('数据保存成功！');
        }
        catch (\Exception $e)
        {
            logResult('【道馆课程表数据创建失败】'. $e->__toString(),'error');
            DB::rollBack();
            return error($e->getMessage());
        }
    }
    
    /**
     * 获取课程表展示数据
     * @param $id
     * @return mixed
     * @author Red-Bo
     */
    public  function show($id)
    {

        try
        {
            $model = $this->model;
            $schedule = $model->find($id);
            if(empty($schedule))
                return error('未找到相关数据记录');
            $schedule_id = $schedule->id;
            // 组装数据返回
            $venue_course = $schedule->toArray();
            $venue_course['date_between'] = [
                $venue_course['start_time'],
                $venue_course['end_time'],
            ];
            $course_count = $venue_course['course_count'];
            $venue_schedule_detail_model = new VenueScheduleDetail();
            $details  = $venue_schedule_detail_model->getVenueSchedules($schedule_id);
            $venue_schedules = $course_times = [];

            $schedule_course_times_model = new  VenueScheduleCourseTime();
            $course_times  = $schedule_course_times_model->getScheduleCourseTime($schedule_id);

            $course_times_temp = [];
            foreach ($course_times as $k =>  $course_time)
            {
                $course_times_temp[$k] = [
                    date("Y-m-d H:i:s",strtotime($course_time[0])),
                    date("Y-m-d H:i:s",strtotime($course_time[1])),
                ];
            }
            $course_times = $course_times_temp;

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
            
            //$course_times = $this->reBuildCourseTimes($course_times);
            $data = compact('venue_course','venue_schedules','course_times');
            return success('数据获取成功', $data);
        }
        catch (\Exception $e)
        {
            logResult('【道馆课程表数据获取错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    /**
     * 更新课表数据
     * @param array $data
     * @param       $id
     * @return array|void
     * @author Red-Bo
     */
    public function update(array $data, $id)
    {
        try
        {
            $schedule = $this->find($id);
            if($schedule)
            {
                // 设置字段默认值
                DB::beginTransaction();
                $venue_course      = $data['venue_course_form'];
                $course_times      = $data['course_times'];
                $venue_schedules   = $data['venue_schedules'];
                $course_count      = $venue_course['course_count'];
                $operator_id       = $this->admin_id;

                $start_time = date("Y-m-d 00:00:00",strtotime($venue_course['date_between'][0]));
                $end_time   = date("Y-m-d 23:59:59",strtotime($venue_course['date_between'][1]));
                $params = [
                    'venue_id'     => $venue_course['venue_id'],
                    'start_time'   => $start_time,
                    'end_time'     => $end_time,
                    'id' => isset($venue_course['id']) ? $venue_course['id'] : 0,
                ];
                //保存之前要校验一下课程表时间是否合法
                $result = $this->model->getDateBetweenSchedule($params);
                if($result)
                {
                    return error('课程有效期出现重叠，请检查');
                }
                $venue_schedule   = [
                    'venue_id'     => $venue_course['venue_id'],
                    'course_count' => $venue_course['course_count'],
                    'schedule_name' => $venue_course['schedule_name'],
                    'start_time'   => $start_time,
                    'end_time'     => $end_time,
                    'status'       => $venue_course['status'],
                    'operator_id'  => $operator_id,
                ];

                // 设置字段默认值
                $schedule->update($venue_schedule);
                $schedule_id = $id;
                // 组装新的数据
                $now = getNow();
                $venue_schedule_detail = $venue_schedule_course_times = [];
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
                                $course_time = $course_times[$i];
                                $schedule    = $venue_schedule[$i];
                                $remark      = !empty($schedule['remark']) ? $schedule['remark'] : '';
                                $venue_schedule_detail[] = [
                                    'schedule_id' => $schedule_id,
                                    'class_id'    => $schedule['class_id'],
                                    'week'        => $schedule['week'],
                                    'section'     => $schedule['section'],
                                    'remark'      => $remark,
                                    'operator_id' => $operator_id,
                                    'created_at'  => $now,
                                ];
                                $venue_schedule_course_times[$schedule['section']] = [
                                    'schedule_id' => $schedule_id,
                                    'section'     => $schedule['section'],
                                    'start_time'  =>  date("H:i:s",strtotime($course_time[0])),
                                    'end_time'    => date("H:i:s",strtotime($course_time[1])),
                                    'operator_id' => $operator_id,
                                    'created_at'  => $now,
                                ];
                            }
                        }
                    }
                }

                if($venue_schedule_detail)
                {
                    $schedule_detail_model = new VenueScheduleDetail();
                    // 先删后增加 快且方便
                    $schedule_detail_model->where('schedule_id', $schedule_id)->delete();
                    $schedule_detail_model->BatchCreate($venue_schedule_detail);
                }


                if($venue_schedule_course_times)
                {
                    $schedule_course_times_model = new  VenueScheduleCourseTime();
                    $schedule_course_times_model->where('schedule_id', $schedule_id)->delete();
                    $schedule_course_times_model->BatchCreate($venue_schedule_course_times);
                }

                Event::fire(new AdminLogger($venue_course['venue_id'],'update',"编辑课程表【{$venue_course['schedule_name']}】"));
                DB::commit();
                return success('数据更新成功');
            }
            return error('记录不存在，请检查');
        }catch (\Exception $e)
        {
            logResult('【修改道馆课程表错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }


    }
    
    public  function  index(Request $request)
    {
        try
        {
            $pageSize  = $request->get('pageSize') ?: self::DEFAULT_PAGE_SIZE;
            $venuesIds = $this->getUserVenueIds();

            // 排序规则
            $orderBy   = $request->get('orderBy')?:'id';
            $sortBy    = $request->get('sortedBy')?:'desc';

            $query     = VenueSchedule::query();
            $data      = $query
                         ->with(['operator','venues'])
                         ->whereIn('venue_id', $venuesIds)
                         ->orderBy($orderBy, $sortBy)
                         ->paginate($pageSize)->toArray();
            return success('列表数据查询成功',$data);
        }
        catch (\Exception $e)
        {
            logResult('【道馆课程表列表获取错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    public  function  delete($id)
    {
        try
        {
            $venueIds = $this->getUserVenueIds();
            $venueSchedule = $this->model->where('id', $id)
                                         ->whereIn('venue_id', $venueIds)
                                         ->first();
            if (empty($venueSchedule))
            {
                return error('数据不存在');
            }

            if($venueSchedule['status'] == self::VENUE_SCHEDULE_ON_STATUS)
            {
                return error("课程已启用不可在删除");
            }

            $venue_id = $venueSchedule->venue_id;
            $schedule_name = $venueSchedule->schedule_name;

            // 删除道馆课程表详情数据
            $venue_schedule_detail_query = VenueScheduleDetail::query();
            $venue_schedule_detail_query
                                    ->where('schedule_id', $id)
                                    ->delete();

            // 删除课程数据表数据
            $schedule_course_times_query = VenueScheduleCourseTime::query();
            $schedule_course_times_query->where('schedule_id', $id)->delete();

            // 删除课表数据
            $venueSchedule->delete();
            Event::fire(new AdminLogger($venue_id,'delete',"删除课程表【{$schedule_name}】"));
            return success('数据删除成功');
        }
        catch (\Exception $e)
        {
            logResult('【删除道馆课程错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }

    /**
     * 修改数课程转态
     * @param $id
     * @param $status
     * @return mixed
     * @author Red-Bo
     */
    public function  changeStatus($id, $status)
    {
        try
        {
            $schedule = $this->find($id);
            if($schedule)
            {
               if(self::VENUE_SCHEDULE_ON_STATUS ==  $schedule['status'])
                   return error('该状态不可修改');
                // 更新数据状态
                $in_status = [
                    self::VENUE_SCHEDULE_OFF_STATUS,
                    self::VENUE_SCHEDULE_ON_STATUS
                ];
                if(in_array($status, $in_status) &&
                    $schedule->status != $status
                )
                {
                    $schedule->status = $status;
                    $schedule->save();
                    return success('状态修改成功');
                }
            }
            else
            {
                return error('数据不存在');
            }
        }
        catch (\Exception $e)
        {
           logResult('【课程状态修改失败】'.$e->__toString(),'error');
           return error($e->getMessage());
        }
    }
    
    protected  function  updateStatus($id, $status)
    {
        try
        {
            $schedule = $this->find($id);
            if($schedule)
            {
                if(self::VENUE_SCHEDULE_ON_STATUS ==  $schedule['status'])
                    return error('该状态不可修改');
                    // 更新数据状态
                $in_status = [
                    self::VENUE_SCHEDULE_OFF_STATUS,
                    self::VENUE_SCHEDULE_ON_STATUS
                ];
                if(in_array($status, $in_status) &&
                    $schedule->status != $status
                )
                {
                    $schedule->status = $status;
                    $schedule->save();
                    return success('状态修改成功');
                }
            }
            else
            {
                return error('数据不存在');
            }
        }
        catch (\Exception $e)
        {
            logResult('【课程状态修改失败】'.$e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    /**
     * 获取道馆当前的课表信息
     * @param Request $request
     * @return array|void
     * @author Red-Bo
     */
    public  function  getSchedules(Request $request)
    {
        $date= $request->get('date');
        if(empty($date))
        {
            $date = date("Y-m-d");
        }
        try 
        {
            $venueScheduleService  = ServiceFactory::getService("Admin\\VenueSchedule");
            $th_fields = $venueScheduleService->getScheduleHead($date); // 表头字段
            $schedule = $venueScheduleService->getSchedulesInUse($request);
            $schedule['fields'] = $th_fields;
            return success("数据获取成功",$schedule);
        } 
        catch (\Exception $e) 
        {
            logResult('【获取道馆课程错误】'.$e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    /**
     * 保存道馆课程补充数据
     *
     * @param array $data
     * @return array
     */
    public  function saveScheduleExtend(array $data)
    {
        try
        {
            $today_time         = strtotime(date("Y-m-d"));
            $schedule_date_time = strtotime($data['schedule_date']);
            // 1、判断请求时间是否已过期
            if($today_time > $schedule_date_time)
            {
                return error("课程已过期，不能设置");
            }
            $data['operator_id'] = $this->admin_id;
            $data['created_at']  = getNow();
            $data['remark'] = empty($data['remark']) ? '': $data['remark'];
            // 2、组装数据
            $model = ServiceFactory::getModel("Admin\\VenueScheduleDetailExtend");
            $fields = [
                'schedule_id','schedule_date','class_id','week','section',
                'remark','operator_id','created_at',
            ];
            foreach ($fields as $v)
            {
                $model->$v = isset($data[$v]) ? $data[$v] : '';
            }
            // 3、判断数据库中是否存在当天的数据
            $query = $model->query();
            $where = [
                ['schedule_id','=', $data['schedule_id']],
                ['schedule_date','=', $data['schedule_date']],
                ['week','=', $data['week']],
                ['section','=', $data['section']],
            ];
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
            $row = $query->first();
            if($row)
            {
                // 3.1 有 先删 后添加
                $row->delete();
                $id = $model->save();
            }
            else
            {
                // 3.2 无 添加数据
                $id = $model->save();
            }
            if($id)
            {
                return success("数据保存成功");
            }
            else
            {
                return error("数据保存失败");
            }
        }
        catch (\Exception $e)
        {
            logResult('【保存道馆扩展课程数据失败】'.$e->__toString(),'error');
            return error($e->getMessage());
        }
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
