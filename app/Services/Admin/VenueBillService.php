<?php
/**
 * 道馆账单服务 
 */
namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Admin\VenueBill;
use App\Models\Admin\VenueBillLog;
use App\Models\Admin\Studnet;
use App\Models\Admin\Venue;
use App\Repositories\VenueBillRepositoryEloquent;



class VenueBillService
{
    protected $venueBillRepository;

    protected  $fields = [
        'venue_id'   => 0,
        'bill_type'  => 1,
        'data_type'  => '',
        'student_card_id' => 0,
        'money'     => 0,
        'status'   => 0,
        'bill_created_at' => '',
        'remark'          => '',
        'operate_remark' => '',
        'input_user_id'   => 0,
        'operator_id'     => '',
    ];


    protected function __construnct(VenueBillRepositoryEloquent $venueBillRepository)
    {
        $this->venueBillRepository;
    }


    /**
     * 添加用户卡券账单数据
     * @param  array  $cards  用户购买的卡券数据
     * @param  int $student_id 学生ID
     * @param  in $venue_id 道馆ID 
     * @return bool|void
     */
    public function createUserCardVill(array $cards,$venue_id)
    {
        if(empty($cards))
            return  false;
        $card_id_arr    = array_column($cards,'card_id');
        $student_id_arr = array_unique(array_column($cards,'student_id')); // 同一个用户ID
        $student_bill   = [];
        $card_data      = Card::whereIn('id', $card_id_arr)->get()->toArray();
        $card_data      = array_column($card_data,NUll,'id');
        $student_data   = Student::whereIn('id', $student_id_arr)->get()->toArray();
        $studnet_data   = array_column($student_data,NULL,'id');
        $venue_info     = Venue::find($venue_id);
        if($card_data  && $venue_info)
        {
            $venue_bill_data = [];
            $created_at = date("Y-m-d H:i:s");
            foreach ($cards as $k => $card) 
            {
                $card_info    = isset($card_data[$card['card_id']]) ? $card_data[$card['card_id']] : [];
                $studnet_info = isset($studnet_data[$card['student_id']]) ? $studnet_data[$card['student_id']] : [];
                $studnet_name =  $student_info['name'];
                if($card_info)
                {
                    $card_name = $card_info['name'];
                    $venue_bill_data[] = [
                        'venue_id'   => $venue_id,
                        'bill_no'   =>  '',
                        'bill_title'   => $studnet_name ."购买{$card['number']}张卡券({$name})" ,
                        'bill_type'  => 1,
                        'data_type'  => 1,
                        'student_card_id' => $card['id'],
                        'money'     => $card['card_price'],
                        'status'   => 0,
                        'bill_created_at' => $created_at,
                        'remark'          => '',
                        'operate_remark' => '系统自动生成账单',
                        'operator_id'     => 0,
                        'input_user_id'   => 0,
                        'created_at' => $created_at,
                        'updated_at' => $created_at,
                   ];
                }
            }
        }
        return false;
        
    }

    /**
     * 创建道馆应收账单
     * @param  array  $data 账单数据
     * @return bool 
     */
    public function creaetVenueBill(array $data)
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
    protected function createVenueBillOrderNo($venue_id)
    {
        
    }
}

