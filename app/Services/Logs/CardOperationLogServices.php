<?php
namespace App\Services\Logs;


class CardOperationLogServices extends  OperationLogService
{
    protected  $table = 'admin_data_operate_logger';
    
    /**
     * 添加卡券操作日志
     * @param array $params
     * @param array 格式1 (单个字段) :
     * [
     *     'card_id' => 'id1',
     *     'operation' => '操作',
     *     'field' => '字段',
     *     'oldValue' => '旧值',
     *     'newValue' => '新值',
     * ];
     * 格式2 (多个字段)
     * [
     *     'card_id'   => 'id1',
     *     'operation' => '操作',
     *     'field'     => ['字段1','字段2','字段3'],
     *     'oldValue'  => ['旧值1','旧值2','旧值3',],
     *     'newValue' => ['新值1','新值2','新值3',]
     *]
     * @return array
     * @author Red-Bo
     */
    public  function  addCardLog(array $params)
    {
        logResult(json_encode($params));
        if( empty($params['card_id']) || !is_numeric($params['card_id']) ){
            return error('参数不正确');
        }

        if(empty($params['operation'])) {
            return error('操作描述不能为空');
        }
        
        $data = [
            'type' => 'card',
            'data' => [
                'type_id' => $params['card_id'],
                'operation' => $params['operation'],
                'log' => [],
            ]
        ];
        
        $params['field']     = isset($params['field']) ? $params['field'] : '';
        $params['oldValue']  = isset($params['oldValue'])? $params['oldValue'] :'';
        $params['newValue']  = isset($params['newValue']) ? $params['newValue'] :'';
        
        if(!is_array($params['field']))
        {
            $data['data']['log'][] = [
                "field"     => $params['field'],
                "oldValue"  => $params['oldValue'],
                "newValue"  => $params['newValue'],
            ];
        }
        else
        {
            foreach ($params['field'] as $k => $filed)
            {
                $data['data']['log'][] = [
                    "field"     => $filed,
                    "oldValue"  => $params['oldValue'][$k],
                    "newValue"  => $params['newValue'][$k],
                ];
            }
        }
        
        $result = $this->saveLog($data);
        if($result['status'] == 0) {
            return error($result['msg']);
        }
        return success($result['msg']);
    }
    
}