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
    }
    
    
    protected  function  validateLog($log, $type)
    {
        if(empty($type))
        {
            $this->setMessage('日志类型错误',0);
            return false;
        }
        $structure = OperationLogHelper::getLogMapping($type);
        if(empty($structure))
        {
            $this->setMessage("缺少日志[{$type}]校验的mapping",0);
            return false;
        }
        
        if(empty($log['data']) || !is_array($log['data']))
        {
            $this->setMessage('');
        }
        
        
        
        
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