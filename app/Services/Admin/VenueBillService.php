<?php
/**
 * 道馆账单服务 
 */
namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;
use App\Models\Admin\VenueBill;
use App\Models\Admin\VenueBillLog;
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
}

