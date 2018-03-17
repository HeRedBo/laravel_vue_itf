<?php

namespace App\Repositories;

use App\Services\ServiceFactory;
use Illuminate\Container\Container as Application;

use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\AdminCommonRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StudentRepository;
use App\Models\Admin\Student;
use App\Models\Admin\Card;
use App\Models\Admin\RelationName;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\Admin\StudentCard;
use App\Services\Admin\VenueBillService;
use App\Services\Admin\StudentService;
use App\Services\Admin\VenueSchedule;
use App\Services\Common\Dictionary;
use App\Services\Logs\StudentCardLogService;
use Illuminate\Support\Facades\Event;
use App\Events\AdminLogger;


/**
 * Class StudentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class StudentRepositoryEloquent extends AdminCommonRepository implements StudentRepository
{
    protected $fields = [
        'name'          => '',
        'native_place'  => '',
        'sex'           => '',
        'picture'       => '',
        'birthday'      => '',
        'id_card'       => '',
        'school'        => '',
        'province_code' => 0,
        'province'      => '',
        'city_code'     => 0,
        'city'          => '',
        'area_code'     => 0,
        'area'          => '',
        'address'       => '',
        'sign_up_at'    => '',
        'venue_id'      => '',
        'status'        => 1,
        'operator_id'   => 0
    ];

    protected $fieldSearchable = [
        'name'=>'like',
        'sex',
        'venue_id',
    ];

    protected $pageSize = 15;

    protected  $studentCardService;

    protected  $venueService;
    
    protected $studentService;
    
    protected  $class_sign_start_minute = 20; //  班级签到开始分钟 既什么时候一接口可以提前多少分钟签到
    protected  $sign_can_modify_minute = 10; // 签到多久后可以修改
    
    const STUDENT_CARD_LOGGER_TYPE ='student_card';
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Student::class;
    }

    
    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    public function __construct(StudentService $studentService)
    {
        parent::__construct();
        $this->studentService = $studentService;
    }

    public  function  studentList(Request $request)
    {
        $pageSize = $request->get('pageSize') ?: $this->pageSize;
        $sex     =  $request->get('sex','-1');
        $status  = $request->get('status','-1');
        $venue_id = $request->get('venue_id');
        $class_id = $request->get('class_id');
        $name     = $request->get('name');
        $sign     = $request->get('sign');
        $params   = $request->all();
        $where = [];
        if($sign)
        {
            $status = 1;
        }


        if(!empty($venue_id))
        {
            $where[] = ['venue_id','=', $venue_id];
        }
        if(!empty($name))
            $where[] = ['name','like',"%{$name}%"];

        if(is_numeric($sex) && in_array($sex,[0,1]))
        {
            $where[] = ['sex','=',$sex];
        }
        if(is_numeric($status) && in_array($status,[0,1]))
        {
            $where[] = ['status','=',$status];
        }

        if(!empty($class_id))
            $where[] = ['admin_student_class.class_id','=',$class_id];

        $orderBy = $request->get('orderBy')?:'id';
        $sortBy  = $request->get('sortedBy')?:'desc';

        $order_by = [
            [$orderBy,$sortBy]
        ];

        $query = $this->model->query();
        $query->join("admin_student_class",'admin_students.id','=','admin_student_class.student_id');
        if($where)
        {
            foreach ($where as $v)
            {
                $query->where($v[0], $v[1], $v[2]);
            }
        }

        if($order_by)
        {
            foreach ($order_by as $v)
            {
                $query->orderBy($v[0], $v[1]);
            }
        }

        $fields = ["admin_students.*"];
        $query->groupBy('admin_students.id');
        $list =  $query->with(['operator','venues','classes'])
                        ->select($fields)
                        ->paginate($pageSize)
                        ->toArray();
        if(!empty($list['data']))
        {
            $data = $list['data'];
            $student_sign_data    = [];
            $date_venue_schedules = [];
            $course_times         = [];
            $date = $request->get('date');
            if(empty($date))
                $date = date("Y-m-d");
            $params['date'] = $date;
            
            
            $sign_time = strtotime($date);
            $time      = time();
            //  签到器签到时间必须小于当天
            if($sign && $sign_time  <  $time )
            {
                $student_ids = array_column($data,'id');

                $student_sign_data = $this->studentService->getStudentSignData($student_ids, $params);
                $venueScheduleService = ServiceFactory::getService("Admin\\VenueSchedule");
                $venueSchedule = $venueScheduleService->getSchedulesInUse($request);
                $venue_schedules = $venueSchedule['venue_schedules'];
                $course_times    = $venueSchedule['course_times'];
                $w   = getDateWeek($date);
                $date_venue_schedules = isset($venue_schedules[$w]) ? $venue_schedules[$w] : [];
            }

            foreach ($data as &$v)
            {
                $v['sign_data']  = [];
                $v['can_sign']   = 0;
                $classes         = $v['classes'];
                $class_ids       = array_column($classes,'id');
                $classes         = array_column($classes,NULL,'id');
                $student_id      = $v['id'];
                $sign_up_at      = $v['sign_up_at'];// 学生的报名时间
                $sign_at_time    = strtotime($sign_up_at);
                $sign_data       = [];
                if($date_venue_schedules)
                {
                    $student_sign_tmp = isset($student_sign_data[$student_id]) ? $student_sign_data[$student_id]: [];
                    $student_sign = isset($student_sign_tmp[$date]) ? $student_sign_tmp[$date] : [];
                    foreach ($date_venue_schedules as $date_venue_schedule)
                    {
                        if(empty($date_venue_schedule))
                        {
                            continue;
                        }
                        $class_id    = $date_venue_schedule['class_id'];
                        $section     = $date_venue_schedule['section'];
                        $course_time = $course_times[$section];
                        $class_sign_start_minute = $this->class_sign_start_minute;
                        $course_start_time = strtotime("{$date} ".$course_time[0]);
                        $compare_time    =  $time + $class_sign_start_minute * 60;

                        if(in_array($class_id,$class_ids) && $course_start_time > $sign_at_time)
                        {
                            // 未签到数据初始化
                            $can_sign     = 1;
                            $class_name = "【{$section}】". $date_venue_schedule['class_name'];
                            $status = 0;
                            $sign_class_data = [
                                "venue_id"    => $v['venue_id'],
                                "student_id"  => $student_id,
                                "class_id"    => $class_id,
                                "section"      => $section,
                                'status_name' => '未签到',
                                'type_name'   => '',
                                'sign_date'   => '',
                                'sign_at'     => '',
                                'status'      => 0,
                                'class_name'  => $class_name
                            ];
                            $sign_at = 0;
                            if($student_sign)
                            {
                                $student_section_sign = isset($student_sign[$section]) ? $student_sign[$section] : [];
                                if($student_section_sign)
                                {
                                    $sign_class_data  = $student_section_sign;
                                    $sign_class_data['sign_at'] = $student_section_sign['created_at'];
                                    $sign_at  = $sign_class_data['sign_at'];
                                    $status  = $student_section_sign['status'];
                                    unset($student_section_sign['created_at']);
                                }
                                // 删除签到数据的数据
                                unset($student_sign[$section]);
                            }
                            if($status == 0 && $compare_time < $course_start_time)
                            {
                                $can_sign = 0;
                            }
                            
                            if($status > 0)
                            {
                                 $sign_at_time = strtotime($sign_at);
                                 $sign_at_compare_time = $sign_at_time + $this->sign_can_modify_minute * 60;
                                 if($sign_at_compare_time < $time)
                                 {
                                     $can_sign = 0;
                                 }
                            }
                            $sign_class_data['can_sign']    = $can_sign;
                            $sign_data[] = $sign_class_data;
                            $v['can_sign'] = $can_sign;
                        }
                    }
                    // 如果还有其他数据数据继续追加的用户数据中
                    if(!empty($student_sign))
                    {
                        foreach ($student_sign as $section => $sign)
                        {
                            $sign['can_sign'] = 0;
                            $sign['sign_at'] = $sign['created_at'];;
                            $sign_data[] = $sign;
                        }
                    }
                }
                $v['sign_data'] = $sign_data;
            }
            $list['data'] = $data;
        }
        return $list;
    }

    /**
     * create a new student record
     * @param array $data
     * @param StudentCard $studentCard
     * @param VenueBillService $bill_service
     * @return array
     * @author Red-Bo
     */
    public  function  createStudent(array  $data, StudentCard $studentCard,VenueBillService $bill_service)
    {
        try
        {
            //
            DB::beginTransaction();
            $student = $this->model;
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $student->$field = (!isset($data[$field]) || is_null($data[$field]))   ? $this->fields[$field] : $data[$field];
            }
            //保存用户信息
            $student->save();
            $studentContacts = $data['user_contacts'];
            $studentCards    = $data['user_cards'];
            $classId         = $data['class_id'];
            $sign_up_at      = $data['sign_up_at'];
            $student_id      = $student->id;

            $this->studentCardService = $studentCard;
            $this->venueService       = $bill_service;
            // 处理用户会员卡问题
            $number_card_id = $this->studentCardService->saveStudentNumberCard($student_id, $data);
            // 保存用户课程信息
            $student->giveClassTo($classId);
            // 保存用户联系人
            $student->giveContactsTo($studentContacts);
            // 保存用户购买的卡券
            $user_card = $student->giveCardTo($studentCards,$sign_up_at, $number_card_id);
            if($user_card && is_array($user_card))
            {
                $this->venueService->createUserCardBill($user_card,$data['venue_id']);
            }

            DB::commit();
            return success('学生信息创建成功');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            logResult('【学生信息新增错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    /**
     * update student info
     * @param array $data
     * @param  int     $id
     * @param StudentCard $studentCard
     * @return array|void
     * @author Red-Bo
     */
    public function  updateStudent(array $data, $id, StudentCard $studentCard)
    {
        $student = $this->find($id);
        if(!$student)
        {
            return error('未找到相关数据记录信息');
        }
        $this->studentCardService = $studentCard;
        try
        {
            DB::beginTransaction();
            // 设置字段默认值
            $old_picture = $student->picture;
            foreach(array_keys($this->fields) as $field)
            {
                if($field == 'picture')
                {
                    if(strrpos($data[$field],'http:') !== false) {
                        continue;
                    }
                }
                $student->$field = (!isset($data[$field]) || is_null($data[$field])) ? $this->fields[$field] : $data[$field];
            }

            if($old_picture != $data['picture'])
            {
                // 删除旧图
                $manager = app('uploader');
                $manager->deleteFile($old_picture);
            }

            //修改用户信息
            $student->save();
            $studentContacts = $data['user_contacts'];
            $classId         = $data['class_id'];
            // 保存用户课程信息
            $student->giveClassTo($classId);
            // 保存用户联系人
            $student->giveContactsTo($studentContacts);
            // 处理用户会员卡问题
            $number_card_id = $this->studentCardService->saveStudentNumberCard($id, $data);

            DB::commit();
            return success('数据修改成功');
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            logResult('【学生信息更新错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }
    
    public  function  getRelationOptions()
    {
        return  RelationName::all(['id','name'])->toArray();
    }
    
    /**
     * get student info
     * @param integer  $id student_id
     * @author Red-Bo
     */
    public  function  getStudentInfo($id, StudentCard $studentCard)
    {
        $student = $this->with(['classes','cards','contacts'])->find($id);
        if($student)
        {
            // 获取数据归属的班级
            $class = $student->classes;
            $classIds = array_column($class->toArray(),'id');
            $student['class_id'] = $classIds;
            // 获取用户卡券
            $student_cards = $student->cards->toArray();
            $card_ids = array_column($student_cards,'card_id');
            $cards_info = Card::whereIn('id',$card_ids)->get()->toArray();
            $cards_info = array_column($cards_info,NULL, 'id');

            foreach ($student_cards as &$student_card) {
                $card_info = $cards_info[$student_card['card_id']];
                $student_card['name'] = $card_info['name'];
                $student_card['buy_number'] = $student_card['number'];
                $student_card['card_price'] = $card_info['card_price'];
                $student_card['total_price'] = $student_card['number'] * $card_info['card_price'];
            }
            // 获取学生会员卡编号
            $student['card_number'] = $studentCard->getStudentNumberCard($id);

            $contacts =  $student->contacts->toArray();
            $relations_ids = array_column($contacts,'relation_id');
            $relation_names = RelationName::whereIn('id', $relations_ids)->get()->toArray();
            $relation_names = array_column($relation_names,NULL,'id');

            foreach ($contacts as &$contact) {
                $contact['relation_name'] = $relation_names[$contact['relation_id']]['name'];
            }
            $student['user_cards'] = $student_cards;
            $student['user_contacts'] = $contacts;
            $student = $student->toArray();
            unset($student['cards']);
            unset($student['contacts']);
            unset($student['classes']);
            return success('数据获取成功', $student);
        }
        else
        {
            return error('学生信息不存在');
        }
    }

    /**
     * get student base info
     * @param int $student_id
     * @return array
     * @author Red-Bo
     * @date 2017-12-31
     */
    public  function  getStudentBaseInfo($student_id, StudentCard $studentCard)
    {
        $student = $this->with(['classes','contacts','venues'])->find($student_id);
        if($student)
        {
            // 获取数据归属的班级
            $class = $student->classes;
            $classIds = array_column($class->toArray(),'id');
            $student['class_id'] = $classIds;
            // 获取用户卡券
            $in_use_card_info = $studentCard->getStudentStatusCardInfo($student_id, 1);
            $student['in_user_student_card'] = $in_use_card_info; // 使用的学生卡券
            $contacts =  $student->contacts->toArray();
            $relations_ids = array_column($contacts,'relation_id');
            $relation_names = RelationName::whereIn('id', $relations_ids)->get()->toArray();
            $relation_names = array_column($relation_names,NULL,'id');
            foreach ($contacts as &$contact) {
                $contact['relation_name'] = $relation_names[$contact['relation_id']]['name'];
            }
            $student['user_contacts'] = $contacts;
            $student = $student->toArray();
            $student['venue_name'] = $student['venues']['name'];
            $student['sex_map']   = Dictionary::SexOptions();
            unset($student['contacts']);
            //unset($student['classes']);
            unset($student['venues']);
            return success('数据获取成功', $student);
        }
        else
        {
            return error('学生信息不存在');
        }
    }

    /**
     * @param array $params
     * @return mixed
     */
    public  function  sign(array $params, StudentCard $studentCard)
    {
        try
        {
            // 校验当前课程是否有效
            $venueScheduleService = ServiceFactory::getService("Admin\\VenueSchedule");
            $params['date'] = $params['sign_date'];
            $schedule = $venueScheduleService->searchSchedule($params);

            if(empty($schedule))
            {
                return error("未找到该时间段课程表，请检查参数");
            }
            $schedule_id = $schedule['id'];
            $request = [
                'schedule_id' => $schedule_id,
                'section' => $params['section'],
                'class_id' => $params['class_id'],
                'date'    => $params['sign_date'],
            ];
            $schedule = $venueScheduleService->getSignDataValidate($request);
            if(empty($schedule))
            {
                error("未找到该时间相关课程，请检查参数是否有有误");
            }

            $student_ids = $params['student_ids'];
            $venue_id    = $params['venue_id'];
            $class_id    = $params['class_id'];
            $remark      = isset($params['remark']) ? $params['remark']?: '':'';
            if(!is_array($student_ids))
                $student_ids = [$student_ids];
            $query = $this->model->query();
            $fields = ['id','name','venue_id','sex','picture'];
            $result = $query->with('classes')
                            ->whereIn('id', $student_ids)
                            ->where('venue_id','=', $venue_id)
                            ->select($fields)
                            ->get()
                            ->toArray();
            $insert_data = $sign_student_ids = [];
            if($result)
            {
                foreach ($result as $v)
                {
                    $now = getNow();
                    $temp_class_id = array_column($v['classes'],'id');
                    if(in_array($class_id, $temp_class_id))
                    {
                        $insert_data[] = [
                            'venue_id'   => $venue_id,
                            'student_id' => $v['id'],
                            'class_id'   => $class_id,
                            'sign_date'  => $params['sign_date'],
                            'status'     => $params['status'],
                            'section'     => $params['section'],
                            'remark'     => $remark,
                            'operator_id' => $this->admin_id,
                            'created_at' => $now,
                        ];
                        $sign_student_ids[] =  $v['id'];
                    }
                }
            }
            else
            {
                return error('学生信息不存在');
            }
            if($insert_data)
            {
                $model = ServiceFactory::getModel("Admin\\VenueStudentSign");
                // 先删旧数据
                $query = $model->query();
                $where = [
                    ['venue_id','=', $venue_id],
                    ['class_id','=', $class_id],
                    ['sign_date', '=', $params['sign_date']],
                    ['section', '=', $params['section']],
                ];
                foreach ($where as $v)
                {
                    $query->where($v[0], $v[1],$v[2]);
                }
                
                $query->whereIn('student_id',$student_ids)
                       ->delete();
                $res = $model->BatchInsert($insert_data);
                if($res)
                {
                    // 签到成功 调用签到用户修改卡券使用接接口
                    if($sign_student_ids)
                    {
                        $this->studentCardService =$studentCard;
                        $this->studentCardService->updateCardResidueNumber($sign_student_ids);
                    }
                    
                    $student_names = array_column($result,'name');
                    $student_names = implode(',', $student_names);
                    Event::fire(new AdminLogger($venue_id,'sign',"【{$student_names}】签到"));
                    return success('签到成功');
                }
            }
            
        }
        catch (\Exception $e)
        {
            logResult('【学生签到错误】'. $e->__toString(),'error');
            return error('签到错误'. $e->getMessage());
        }
        return error('签到失败');
    }
    
    public function  getSignCalendar(Request $request)
    {
        try
        {
            $service = ServiceFactory::getService("Admin\\VenueSchedule");
            $result = $service->getSignCalendar($request);
            return success('数据获取成功过',$result);
        }catch (\Exception $e)
        {
            logResult('【学生签到记录获取错误】'. $e->__toString(),'error');
            return error('签到错误'. $e->getMessage());
        }
    }


    /**
     * 获取签到道馆班级课程下拉框
     * @param  Request $request 请求参数
     * @return mixed
     */
    public function signClassOptions(Request $request)
    {
        try
        {
            $params = $request->all();
            $date     = $request->get('date');
            if(empty($date))
                $date = date("Y-m-d");
            $params['date'] = $date;
            $result = $this->studentService->signClassOptions($params);
            return success('数据获取成功',$result);
        }
        catch (\Exception $e)
        {
            logResult('【学生签到课程记录】'. $e->__toString(),'error');
            return error('签到课程获取失败'. $e->getMessage());
        }
    }

    /**
     * 获取学生卡券操作日志
     * @param Request $request
     * @return array
     */
    public function getStudentCardLogger(Request $request)
    {

        $student_id = $request->get('student_id');
        if(empty($student_id))
            return error("学生ID不能为空！");
        $where =  [
            ['student_id','=', $student_id],
        ];
        $orderBy = $request->get('orderBy')?:'id';
        $sortBy  = $request->get('sortedBy')?:'desc';
        $pageSize = $request->get('pageSize') ?: self::DEFAULT_PAGE_SIZE;
        $operator_name  = $request->get('operator_name');
        $card_name  = $request->get('card_name');
        $search_time   = $request->get('search_time');
        if($operator_name)
        {
            $where[] = ['operator_name','like',"%{$operator_name}%"];
        }
        if(!empty($card_name))
            $where[] = ['card_name','=', $card_name];

        if($search_time && is_array($search_time))
        {
            $where[] = ['created_at','>=', $search_time[0]];
            $where[] = ['created_at','<=', $search_time[1]];
        }

        $order_by = [
            [$orderBy,$sortBy]
        ];
        $studentCardLogServices = new StudentCardLogService();
        $data =  $studentCardLogServices->searchLog(self::STUDENT_CARD_LOGGER_TYPE, $where,$order_by,$pageSize);
        return success("数据获取成功",$data);
    }

}
