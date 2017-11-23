<?php
namespace App\Services\Logs;

use App\Services\Common\OperationLogHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2017/11/20 15:02
 * Desc:
 */

class OperationLogServices
{
    protected  $table = 'admin_data_operate_logger';
    protected  $code  = 1;
    protected  $message = 'ok';
    
    /**
     * 保存数据操作日志
     * @param array $logData
     * ### 数据格式示例
     *@example $logData =  [
     * "type" => "card"
     *  "data" =>  [
     *      "type_id" => 23
     *      "log" =>  [
     *              []
     *         ]
     *      ]
     * ]
     * @return array
     * @author Red-Bo
     */
    public  function  saveLog(array $logData)
    {
        $type = $this->getLogType($logData);
        $result = $this->validateLog($logData, $type);
        if($result) {
            if(Auth::guard('admin')->check())
            {
                $user_object =  Auth::guard('admin')->user();
                $default_operator_id    =  $user_object->id;
                $default_operator_name = $user_object->name;
            }
            else
            {
                $default_operator_id = 0;
                $default_operator_name = '系统操作';
            }
            $logData['data']['created_at'] = date("Y-m-d H:i:s");
            $logData['data']['operator_id'] = isset($logData['data']['operator_id']) ?  $logData['data']['operator_id'] : $default_operator_id;
            $logData['data']['operator_name'] = isset($logData['data']['operator_name']) ? $logData['data']['operator_name'] : $default_operator_name;
            $this->saveOperateData($logData);
        }
        return $this->getResponseObj();
    }
    
    /**
     * 搜索数据操作日志
     *
     * @param string $type 日志类型
     * @param int $page_size 页码
     * @param array $where 查询条件
     * @param array $orderBy 排序规则
     * @return mixed
     * @author Red-Bo
     */
    public  function  searchLog($type, array $where = [], array $orderBy = [], $page_size = 20)
    {
        $DB = DB::table($this->table);
        $DB->where('type','=', $type);
        if($where)
        {
            foreach ($where as $v) {
                $DB->where($v[0], $v[1], $v[2]);
            }
        }
        if(empty($orderBy))
        {
            $orderBy = [
                ['id','desc']
            ];
        }
        
        if($orderBy)
        {
            foreach ($orderBy as $v) {
                $DB->orderBy($v[0], $v[1]);
            }
        }
        
        return $DB->paginate($page_size)->toArray();
    }
    
    protected  function  saveOperateData(array $logData)
    {
        $insert_data   = [];
        $type          = $logData['type'];
        $log           = $logData['data']['log'];
        $type_id       = $logData['data']['type_id'];
        $created_at    = $logData['data']['created_at'];
        $operator_id   = $logData['data']['operator_id'];
        $operator_name = $logData['data']['operator_name'];
        $row_data = [
            'type'         => $type,
            'type_id'      => $type_id,
            'operation'     => '',
            'field'         => '',
            'oldValue'      => '',
            'newValue'      => '',
            'created_at'    => $created_at,
            'operator_id'   => $operator_id,
            'operator_name' => $operator_name,
        ];
        
        foreach ($log as $k => $v) {
            $row_data['operation'] = $v['operation'];
            $row_data['field']     = $v['field'];
            $row_data['oldValue']     = $v['oldValue'];
            $row_data['newValue']     = $v['newValue'];
            $insert_data[] = $row_data;
        }
        
        if($insert_data)
        {
            try
            {
                DB::table($this->table)->insert($insert_data);
            }
            catch (\Exception $e)
            {
                logResult("【{$type} 数据日志插入失败】". $e->getMessage());
                $this->setMessage($e->getMessage(),0);
                return false;
            }
            return true;
        }
        return false;
    }
    
    
    protected  function  validateLog($log, $type)
    {
        if(empty($type))
        {
            $this->setMessage('日志类型错误',0);
            return false;
        }
        $structure = OperationLogHelper::getLogStructure($type);
        if(empty($structure))
        {
            $this->setMessage("缺少日志[{$type}]校验的mapping",0);
            return false;
        }
        
        if(empty($log['data']) || !is_array($log['data']))
        {
            $this->setMessage('没有日志或日志格式不正确',0);
        }
        // 校验数据格式
        $result = true;
        foreach ($log['data'] as $field => $value) {
            $result = $result && $this->validateField($field, $value, $structure);
            if(!$result)
                return false;
        }
        return true;
    }

    protected  function  validateField($field, $value, $structure)
    {
         $fieldPass = isset($structure[$field]);
         if(!$fieldPass)
         {
             $this->setMessage('日志结构校验失败,请检查日志数据结构:'. $field, -1);
             return false;
         }
         
         $isMultiRow = is_array($structure[$field]);
         $result = true;
         if($isMultiRow && is_array($value))
         {
             foreach ($value as $k => $row)
             {
                 if(!is_array($row)) {
                     $this->setMessage("日志数据校验失败,请检查日志数据结构: {$k} - {$row}", 0);
                     return false;
                 }

                 foreach ($row as  $key => $val) {
                     $result = $result && $this->validateField($key, $val, $structure[$field]);
                 }
             }
         }
         return $result;
     }
    
    /**
     * 获取日志类型
     * @param array $logData
     * @return string|bool
     * @author Red-Bo
     */
    protected function  getLogType(array $logData)
    {
        return isset($logData['type']) ? $logData['type'] : false;
    }
    
    
    
    
    public  function  setCode($code)
    {
        $this->code = (int) $code;
    }
    
    public  function  getCode()
    {
        return $this->code;
    }
    
    public  function  setMessage($message, $code = null)
    {
        if(!is_null($code))
            $this->code = (int) $code;
        $this->message = $message;
    }
    
    
    public  function getMessage()
    {
        return $this->message;
    }
    
    /**
     * 返回结果结果对象
     * @return array
     * @author Red-Bo
     */
    protected function  getResponseObj()
    {
        return ['status' => $this->code, 'msg' => $this->message];
    }
    
}