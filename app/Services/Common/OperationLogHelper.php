<?php
namespace App\Services\Common;

class OperationLogHelper
{
    /**
     * 获取日志结构
     * @param string $type 获取日志类型
     * @return  mixed
     * @author Red-Bo
     */
    public static function  getLogStructure($type)
    {
        $mapping = self::getLogMapping($type);
        if(empty($mapping) )
            return false;
        $structure = self::_parseMapping($mapping);
        return $structure;
    }
    
    /**
     * 解析mapping
     * @param array $mapping
     * @return array
     * @author Red-Bo
     */
    private static function  _parseMapping(array $mapping)
    {
        $structure = [];
        foreach ($mapping as $field  => $fieldSet)
        {
            $structure[$field] = self::parseMappingNode($field, $fieldSet);
        }
        return $structure;
    }
    
    protected  static  function parseMappingNode($field, $set)
    {
        if(!is_array($set) || !isset($set['properties']))
            return $field;

        $node = [];
        foreach ($set['properties'] as $key => $value) {
            $node[$key] = self::parseMappingNode($key, $value);
        }
        return $node;
    }
    
    /**
     * 获取日志的mapping
     * @param string|bool $type
     * @return  mixed
     * @author Red-Bo
     */
    public  static  function  getLogMapping($type = false)
    {
        $default_mapping = [
            'operator_id'    => ['type'=>'integer'],
            'operator_name'  => ['type'=>'string'],
            'created_at'     => ['type'=>'date','format'=>'yyyy-MM-dd HH:mm:ss'],
        ];
        $mapping = self::getOperationLogMapping();
        foreach ($mapping as &$map) {
            $map = array_merge($map, $default_mapping);
        }
        if(is_bool($type))
            return $mapping;
        return isset($mapping[$type]) ? $mapping[$type] : false;
    }
    
    public  static  function getOperationLogMapping()
    {
        return [
            //后台卡券数据操作日志
			'card' => [
                'type_id'	   => ['type'=>'integer'],
                'operation'    => ['type'=>'string'],
                'log' => [
                    'properties' => [
                        'field'     => ['type'=>'string'],
                        'oldValue'  => ['type'=>'string'],
                        'newValue'  => ['type'=>'string'],
                     ]
                 ],
            ],

            //后台卡券数据操作日志
            'student_card' => [
                'student_id'        => ['type'=>'integer'],
                'student_card_id'   => ['type'=>'integer'],
                'card_name'         => ['type'=>'string'],
                'operation'         => ['type'=>'string'],
                'log' => [
                    'properties' => [
                        'field'     => ['type'=>'string'],
                        'oldValue'  => ['type'=>'string'],
                        'newValue'  => ['type'=>'string'],
                    ]
                ],
            ],
        ];
    }
}