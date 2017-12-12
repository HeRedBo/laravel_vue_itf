<?php
/**
 * 道馆账单服务 
 */
namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Admin\VenueBill;
use App\Models\Admin\VenueBillLog;
use App\Models\Admin\Student;
use App\Models\Admin\Card;
use App\Models\Admin\Venue;
use App\Repositories\VenueBillRepositoryEloquent;
use Illuminate\Support\Facades\Redis;
use Mockery\CountValidator\Exception;


class VenueBillService
{
    protected $venueBillRepository;
    protected $venues_bill_number_no_redis_key = 'venues_bill_number_no_redis_key:';
    protected  $fields = [
        'venue_id'   => 0,
        'bill_no'   => '',
        'bill_type'  => 1,
        'data_type'  => '',
        'student_card_id' => 0,
        'money'     => 0,
        'currency' => 'cny',
        'status'   => 0,
        'bill_created_at' => '',
        'remark'          => '',
        'operate_remark' => '',
        'create_user_id'   => 0,
        'operator_id'     => '',
    ];


    public function __construct(
        VenueBillRepositoryEloquent $venueBillRepository
    )
    {
        $this->venueBillRepository = $venueBillRepository;
    }


    /**
     * 添加用户卡券账单数据
     * @param  array  $cards  用户购买的卡券数据
     * @param  int $venue_id 道馆ID
     * @throws  mixed
     * @return bool|void
     */
    public function createUserCardBill(array $cards,$venue_id)
    {
        if(empty($cards))
            return  false;
        $card_id_arr    = array_column($cards,'card_id');
        $student_id_arr = array_unique(array_column($cards,'student_id')); // 同一个用户ID

        $card_data      = Card::whereIn('id', $card_id_arr)->get()->toArray();
        $card_data      = array_column($card_data,NUll,'id');
        $student_data   = Student::whereIn('id', $student_id_arr)->get()->toArray();
        $student_data   = array_column($student_data,NULL,'id');
        $venue_info     = Venue::find($venue_id)->toArray();
        if($card_data  && $venue_info)
        {
            $venue_bill_data = [];
            $created_at = date("Y-m-d H:i:s");
            foreach ($cards as $k => $card) 
            {
                $card_info    = isset($card_data[$card['card_id']]) ? $card_data[$card['card_id']] : [];
                $student_info = isset($student_data[$card['student_id']]) ? $student_data[$card['student_id']] : [];
                $student_name =  $student_info['name'];
                $bill_no = $this->createVenueBillOrderNo($venue_id);
                if($card_info)
                {
                    $card_name = $card_info['name'];
                    $venue_bill_data[] = [
                        'venue_id'   => $venue_id,
                        'bill_no'   =>  $bill_no,
                        'bill_title'   => $student_name ."购买{$card['number']}张卡券({$card_name})" ,
                        'bill_type'  => 1,
                        'data_type'  => 1,
                        'student_card_id' => $card['id'],
                        'money'     => $card['card_price'],
                        'status'   => 0,
                        'bill_created_at' => $created_at,
                        'remark'          => '',
                        'operate_remark' => '系统自动生成账单',
                        'operator_id'     => 0,
                        'create_user_id'   => 0,
                        'created_at' => $created_at,
                        'updated_at' => $created_at,
                   ];
                }
            }
            if($venue_bill_data)
            {
                try
                {
                    DB::beginTransaction();
                    DB::table('venue_bill')->insert($venue_bill_data);
                    $venue_bill_log_data = [];
                    foreach ($venue_bill_data as $k => $v)
                    {
                        unset($v['updated_at']);
                        $venue_bill_log_data[] = $v;
                    }
                    DB::table('venue_bill_log')->insert($venue_bill_log_data);
                }
                catch (\Exception $e)
                {
                    DB::rollBack();
                    throw new \Exception($e->getMessage());
                    logResult('【创建系统账单失败】 原因：'. $e->__toString(),'error');
                }
                DB::commit();
                return true;
            }
        }
        return false;
        
    }

    /**
     * 创建道馆应收账单
     * @param  array  $data 账单数据
     * @return bool
     * @throws  mixed
     */
    public function createVenueBill(array $data)
    {
        try 
        {
            $bill = new VenueBill;
            // 设置字段默认值
            foreach(array_keys($this->fields) as $field)
            {
                $bill->$field = (!isset($data[$field]) || is_null($data[$field]))   ? $this->fields[$field] : $data[$field];
            }
            $bill->save();
            $billLog = new VenueBillLog();
            // 添加账单日志信息
            foreach(array_keys($this->fields) as $field)
            {
                $billLog->$field = (!isset($data[$field]) || is_null($data[$field]))   ? $this->fields[$field] : $data[$field];
            }
            $billLog->created_at     = date("Y-m-d H:i:s");
            $billLog->operate_remark = isset($data['operate_remark']) ? $data['operate_remark'] : '系统自动创建账单';
            $billLog->save();
        } 
        catch (\Exception $e) 
        {
            throw new \Exception($e->getMessage());
            logResult('【创建系统账单失败】 原因：'.$e->__toString(),'error');
        }
        return true;    
    }


    /**
     * 创建道馆账单编号
     * @param  int $venue_id 道馆ID
     * @return  string 
     * @author Red-Bo
     * @date 2017-12-12 18:12:30
     */
    public function createVenueBillOrderNo($venue_id)
    {
        $number = 1;
        $key = $this->venues_bill_number_no_redis_key. $venue_id;
        $res = Redis::exists($key);
        if($res)
        {
            $number = Redis::incr($key);
        }
        else
        {
            $row = VenueBill::where('venue_id', $venue_id)->orderBy('id','desc')->first();
            if($row)
            {
                $number =substr($row['bill_no'],-5);
            }
            // 将数据存入redis 下次直接读取redis
            Redis::set($key, $number);
        }
        return date("YmdHis"). str_pad($number, 5, "0", STR_PAD_LEFT);
    }
}

