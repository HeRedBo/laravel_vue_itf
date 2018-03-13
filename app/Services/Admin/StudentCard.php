<?php
/**
 * 学生会员卡服务
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2017/12/8 14:15
 * Desc:
 */

namespace App\Services\Admin;

use App\Services\ServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\StudentNumberCardRepositoryEloquent;
use App\Repositories\VenueRepositoryEloquent;
use App\Repositories\StudentRepositoryEloquent;
use Illuminate\Support\Facades\Redis;
use App\Models\Admin\StudentNumberCard;
use App\Models\Admin\Student;
use App\Models\Admin\CardSnap;
use App\Models\admin\StudentCard as studentCardModel;
use App\Services\Common\Dictionary;
use  App\Services\BaseService;
use Illuminate\Support\Facades\Event;
use App\Events\AdminLogger;



class StudentCard extends  BaseService
{

    const STUDENT_CARD_CLOSE_STATUS = 2; // 卡券停用状态
    const STUDENT_CARD_ON_STATUS = 1; // 卡券启用状态
    const QI_CARD_TYPE   = 1; // 1 期卡 2 次卡
    const CI_CARD_TYPE   = 2; // 1 期卡 2 次卡

    /**
     * @var StudentNumberCardRepositoryEloquent
     */
    protected  $studentNumberCardRepository;
    protected  $venueRepository;
    protected  $studentRepository;
    protected  $venues_student_number_card_redis_key = 'venues_student_number_card_redis_key:';

    protected $attributeValue = [
        'total_class_number' => '课堂总数',
        'residue_class_number' => '已上课程数',
        'start_time' => '卡券有效期开始时间',
        'end_time' => '卡券有效期结束时间',
        'card_price' => '卡券价格',
        'status' => '卡券有效状态',
        'operator_id' => '操作人ID',
        'operator_name' => '操作用户姓名',
    ];



    
    public  function __construct(
        StudentNumberCardRepositoryEloquent $studentNumberCardRepository,
        VenueRepositoryEloquent $venueRepository,
        StudentRepositoryEloquent $studentRepository
    )
    {
        parent::__construct();
        $this->studentNumberCardRepository = $studentNumberCardRepository;
        $this->venueRepository             = $venueRepository;
        $this->studentRepository           = $studentRepository;
    }
    
    /**
     * 保存用户会员卡信息
     * @param int $student_id
     * @param array $data
     * @return bool
     * @author Red-Bo
     */
    public  function saveStudentNumberCard($student_id, $data)
    {
        if(empty($data['card_number']) && (isset($data['auto_create_number']) && $data['auto_create_number'] == 1))
        {
            $card_number = $this->createUserCardNumber($student_id);
        }
        else
        {
            $card_number = $data['card_number'];
        }
        $model = StudentNumberCard::where('student_id','=', $student_id)->first();
        if(!$model)
        {
            $model = new StudentNumberCard();
        }
        $model->number = $card_number;
        $model->student_id = $student_id;
        $model->status = 1;
        $model->operator_id = $data['operator_id'];
        $model->operator_name = $data['operator_name'];
        $model->save();
        return $model->id;
    }

    /**
     * 创建学生会员卡编号
     * @param int $student_id  学生ID
     * @return string 学生卡券编号
     * @author Red-Bo
     */
    public function createUserCardNumber($student_id)
    {
        $student_info = $this->studentRepository->find($student_id);
        if($student_info)
        {
            $venue_id = $student_info->venue_id;
            $venue_info = $this->venueRepository->find($venue_id);
            $card_prefix = $venue_info->card_prefix;
            //学生归属编号
            $student_num = str_pad(substr($student_id,-4), 5, "0", STR_PAD_LEFT);
            // 获取学生会员卡编号
            $card_number = $this->getUserCardNumber($venue_id);
            return $card_prefix .date('ymd'). $student_num . $card_number;
        }
        return false;
    }

    public  function  getStudentCardList(Request $request)
    {
        $where = [];
        $student_id = $request->get('student_id');
        $orderBy = $request->get('orderBy') ?: 'id';
        $sortBy =  $request->get('sortedBy') ?: 'desc';
        $pageSize = $request->get('pageSize') ?:  20;
        if($student_id)
        {
            $where[] = ['student_card.student_id','=', $student_id];
        }

        $DB = DB::table("student_card")
                ->join('card_snap', "student_card.card_snap_id","=", "card_snap.id")
                ->join('admin', "student_card.operator_id","=", "admin.id")
        ;
        if($where)
        {
            foreach ($where as $v)
            {
                $DB->where($v[0], $v[1], $v[2]);
            }
        }
        $DB->orderBy($orderBy, $sortBy);
        $fields = [
                "student_card.*",
                "card_snap.type",
                DB::raw("card_snap.name as card_name"),
                DB::raw("card_snap.card_price as price"),
                DB::raw("admin.name as operator_name"),
        ];
        $list = $DB->select($fields)->paginate($pageSize)->toArray();
        if($list['data'])
        {
            $data = $list['data'];
          
            foreach ($data as &$v)
            {
                $v->type_name = Dictionary::CardTyeMap($v->type);
                $v->status_name = Dictionary::StudentCardStatusMap($v->status);
            }
            
            $list['data'] = $data;
        }
        return $list;
    }

    
    /**
     * 创建学生卡券
     * @param  Request  $request  
     * @param  VenueBillService $bill_service 
     * @return  array
     */
    public function saveStudentCard(Request $request, VenueBillService $bill_service)
    {
        try 
        {
            $student_id      = $request->get('student_id');
            $studentCards    = $request->get('user_cards');
            $venue_id        = $request->get('venue_id');
            $student         = Student::find($student_id);
            // 如果学生不归属当前道馆 不可编辑
            if($student['venue_id']  !=  $venue_id)
            {
                return error('学生不归属当前道馆 不可操作');
            }

            if(empty($student))
            {
                return error('学生信息不存在');
            }
            $student_number_card = StudentNumberCard::where("student_id",'=', $student_id)->first();
            if(empty($student_number_card))
            {
                return error('学生会员卡未存在');
            }

            // 保存用户购买的卡券
            $number_card_id = $student_number_card->id;
            $user_card = $student->giveCardTo($studentCards, date("Y-m-d H:i:s"),$number_card_id);

            if($user_card && is_array($user_card))
            {
                $bill_service->createUserCardBill($user_card,$venue_id);
            }
            return success('学生卡券创建成功');



        } 
        catch (\Exception $e) 
        {
            logResult('【学生卡券创建错误】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }

    /**
     * 获取道馆会员卡卡号最新的一个编号
     * @param int $venue_id 道馆ID
     * @return string
     * @author RedBo
     */
    protected  function  getUserCardNumber($venue_id)
    {
        $number = 1; // 编号初始值
        $res = Redis::exists($this->venues_student_number_card_redis_key. $venue_id);
        if($res)
        {
            $number = Redis::incr($this->venues_student_number_card_redis_key.$venue_id);
        }
        else
        {
            // get the latest record  on studentNumberCard
            $where = [
                ['students.venue_id','=', $venue_id]
            ];
            $orderBy = [
                ['student_number_card.id','desc']
            ];
            $DB = DB::table('student_number_card')
                ->join("students", "student_number_card.student_id",'=', 'students.id');
            if($where)
            {
                foreach ($where as $v)
                {
                    $DB->where($v[0], $v[1], $v[2]);
                }
            }
            if($orderBy)
            {
                foreach ($orderBy as $v)
                {
                    $DB->orderBy($v[0], $v[1]);
                }
            }
            $result = $DB->first();
            if($result)
            {
                $number =substr($result['$result'],-4);
            }
            // 将数据存入redis 下次直接读取redis
            Redis::set($this->venues_student_number_card_redis_key. $venue_id, $number);
        }
        return str_pad($number, 5, "0", STR_PAD_LEFT);
    }


    /**
     * 获取学生状态卡券信息
     * @param $student_id
     * @param int $status
     * @return array
     */
    public  function  getStudentStatusCardInfo($student_id, $status = 1)
    {
        $where = [
            ['student_id','=', $student_id],
            ['status','=', $status]
        ];

        $student_card_model = new studentCardModel();
        foreach ($where as $v)
        {
            $student_card_model->where($v[0], $v[1], $v[2]);
        }
        $result =  studentCardModel::with('card')
                        ->where('student_id','=', $student_id)
                        ->where('status','=',1)
                        ->first();
        $student_card = [];
        if($result)
        {
            $result = $result->toArray();
            $student_card = $result;
            unset($student_card['card']);
            $student_card['type']      = $result['card']['type'];
            $student_card['type_name'] = Dictionary::CardTyeMap($result['card']['type']);
            $student_card['name']      = $result['card']['name'];
            // 计算 使用 百分比
            $student_card['percentage'] = 0;
            // 期卡
            if($student_card['type'] == 1)
            {
                $now_time     = time();
                $end_time     = strtotime($student_card['end_time']);
                $start_time   = strtotime($student_card['start_time']);
                $total_time   = $end_time - $start_time;
                $consume_time = $now_time - $start_time;
                $student_card['percentage'] = round(($consume_time / $total_time),2) * 100;
            }

            // 次卡
            if($student_card['type'] == 2) {
                $total_class_number   = $student_card['total_class_number']; // 总课程数
                $residue_class_number = $student_card['residue_class_number']; //  已经使用课程数
                $student_card['percentage'] = round(($residue_class_number/$total_class_number),2) * 100;
            }
            // 获取卡编号
            $student_card['student_card_number'] = '';
            $number_card_id = $student_card['number_card_id'];
            if($number_card_id)
            {
                $student_number_card_model = new StudentNumberCard();
                $number_card_info = $student_number_card_model
                                    ->where('id','=', $number_card_id)
                                    ->where('status','=',1)
                                    ->first();
                if($number_card_info)
                {
                    $number_card_info = $number_card_info->toArray();
                    $student_card['student_card_number'] = $number_card_info['number'];
                }
            }
        }
        return $student_card;

    }

    /**
     * 获取某个学生的卡券信息
     * @param  int $student_id 学生ID
     * @return string 学生编号
     */
    public  function  getStudentNumberCard($student_id)
    {
        $model =  new StudentNumberCard;
        $student_card_info = $model
                            ->where('student_id','=', $student_id)
                            ->where('status','=',1)
                            ->first();

        $student_number_card = '';
        if($student_card_info)
        {
            $student_number_card = $student_card_info->number;
        }
        return $student_number_card;
    }


    public  function  changeStudentCardStatus(Request $request)
    {
        try
        {
            $id     = $request->get('id');
            $status = $request->get('status');
            $remark = $request->get('remark') ?: '';
            $model  = new studentCardModel();
            $student_card = $model->find($id);
            if(empty($student_card))
            {
                return error("未找到相关卡券记录");
            }
            $student_id = $student_card->student_id;
            $student = Student::find($student_id);
            $venue_id = $student->venue_id;
            $common_service = ServiceFactory::getService("Common\\Common");
            $admin_venue_ids = $common_service->getUserVenueIds();
            if(!in_array($venue_id, $admin_venue_ids))
            {
                return error("你无权操作！");
            }
            $student_card_status_map = Dictionary::StudentCardStatusMap();
            $old_status = $student_card->status;

            if($status <= $old_status)
            {
                return error("当前卡券为为". $student_card_status_map[$old_status]. "不可修改为".$student_card_status_map[$status]);
            }
            if($old_status == self::STUDENT_CARD_CLOSE_STATUS)
            {
                return error("当前卡券为". $student_card_status_map[$old_status]. "不可在修改");
            }
            if($status == self::STUDENT_CARD_ON_STATUS)
            {
                // 开启卡券 校验是否有启用使用中的卡券信息
                $now = getNow();
                $where = [
                     ['id','!=', $id],
                     ['student_id','=',$student_id],
                    ['start_time','<=', $now],
                    ['end_time','>=', $now],
                    ['status','=', self::STUDENT_CARD_ON_STATUS]
                ];
                $query = $model->query();

                foreach ($where as $v)
                {
                    $query->where($v[0], $v[1], $v[2]);
                }
                // 有使用中卡券 提示用不不得修改 必须停掉使用中的卡券信息
                $result = $query->get()->toArray();
                if(!empty($result))
                {
                    return error("存在使用中的卡券，不能修改，如仍要修改，请停用使用中的卡券");
                }

            }

            // 修改状态
            $card_snap_id = $student_card->card_snap_id;
            $card_info    = CardSnap::find($card_snap_id);

            $total_class_number = $residue_class_number = 0;
            $start_time = $end_time = NULL;


            $card_type = $card_info->type;
            $unit      = $card_info->unit;
            $number    = $card_info->number;

            if(self::QI_CARD_TYPE == $card_type)
            {
                $start_time = $request->get('start_time');
                if(empty($effective_date))
                    $start_time = getNow();
                $end_time  = strtotime("$number $unit", strtotime($start_time));
                $end_time  = date("Y-m-d H:i:s", $end_time);
            }
            if(self::CI_CARD_TYPE == $card_type)
            {
                $total_class_number = $number;
            }
            $update_data = [
                'total_class_number' => $total_class_number,
                'start_time'         => $start_time,
                'end_time'           => $end_time,
                'status'             => $status,
                'operator_id'        => $this->admin_id,
                'operator_name'      => $this->admin_name,
                'remark'             => $remark,
            ];

            $old_card_data  = $student_card->toArray();
            $old_card_data['card_name'] = $card_info->name;

            //
            foreach ($update_data as $k => $v)
            {
                $student_card->$k = $v;
            }
            $student_card->save();
            // 记录数据日志
            $log_data = $this->buildStudentCardLog($old_card_data,$update_data,'修改卡券状态');
            if($log_data)
            {
                $student_log_service = ServiceFactory::getService("Logs\\StudentCardLogService");
                $student_log_service->addCardLog($log_data);
            }
            // 记录操作日志
            Event::fire(new AdminLogger($venue_id,'update',"修改学生【{$student->name}】的卡券【[{$id}]{$card_info->name}】状态为".  $student_card_status_map[$status]));
            return success("卡券状态修改成功");
        }
        catch (\Exception $e)
        {
            logResult('【修改学生卡券状态】'. $e->__toString(),'error');
            return error($e->getMessage());
        }
    }

    protected function buildStudentCardLog(array $oldData, array $newData, $operation = '')
    {
        $operation = $operation ? $operation : '修改用户卡券';
        $field = $newValues = $oldValues =[];
        $student_card_status_map = Dictionary::StudentCardStatusMap();

        foreach ($oldData as $k => $v)
        {
            if(!isset($newData[$k]))
                continue;
            if($newData[$k] != $v)
            {
                if($k == 'status')
                {
                    $oldData['status'] = $student_card_status_map[$v];
                    $newData['status'] = $student_card_status_map[$newData[$k]];
                }

                if(isset($this->attributeValue[$k]))
                {
                    $field[] = $this->attributeValue[$k];
                    $oldValues[] = $oldData[$k];
                    $newValues[] = $newData[$k];
                }
            }
        }

        $params = [
            'student_card_id'=> $oldData['id'],
            'student_id'    => $oldData['student_id'],
            'card_name'     => $oldData['card_name'],
            'operation'    => $operation,
            'field'        => $field,
            'oldValue'     => $oldValues,
            'newValue'     => $newValues,
        ];
        return !empty($params['field']) ? $params : [];
    }
}