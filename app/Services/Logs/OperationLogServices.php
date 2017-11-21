<?php
namespace App\Services\Logs;

use App\Services\Common\OperationLogHelper;


/**
 * Created by PhpStorm.
 * User: Red-Bo
 * Date: 2017/11/20 15:02
 * Desc:
 */
class OperationLogServices
{
    protected  $table = 'admin_data_operate_logger';
    protected  $code = 1;
    protected  $message  = 'ok';
    
    /**
     * 保存数据操作日志
     * @param array $logData
     * @example $logData = [
     *      'type' => 'logType',
     *      'data' => [],
     * ];
     * @author Red-Bo
     */
    public  function  saveLog(array $logData)
    {
        $type = $this->getLogType($logData);
        $result = $this->validateLog($logData, $type);
        dd($result);
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

         $isMultiRow = is_array(    $structure[$field]);
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
    
    
    protected function  getLogType($logData)
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
    
}