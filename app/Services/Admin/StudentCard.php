<?php
/**
 * 学生会员卡服务
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2017/12/8 14:15
 * Desc:
 */

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Repositories\StudentNumberCardRepositoryEloquent;
use App\Repositories\VenueRepositoryEloquent;
use App\Repositories\StudentRepositoryEloquent;
use Illuminate\Support\Facades\Redis;
use App\Models\Admin\StudentNumberCard;


class StudentCard
{
    /**
     * @var StudentNumberCardRepositoryEloquent
     */
    protected  $studentNumberCardRepository;
    protected  $venueRepository;
    protected  $studentRepository;
    
    protected  $venues_student_number_card_redis_key = 'venues_student_number_card_redis_key:';
    
    public  function __construct(
        StudentNumberCardRepositoryEloquent $studentNumberCardRepository,
        VenueRepositoryEloquent $venueRepository,
        StudentRepositoryEloquent $studentRepository
    )
    {
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
        if($data['auto_create_number'] == 1)
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
}