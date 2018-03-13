<?php

namespace App\Services\Common;

class Dictionary 
{
    
    public static function UnitOptions($index =false)
    {
        $unitOptions = [
            'day' => '天',
            'mouth' => '月',
            'year' => '年'
        ];
        if($index !== false && isset($unitOptions))
            return $unitOptions[$index];
        return $unitOptions;
    }

    public  static  function  SexOptions($index = false )
    {
        $sexOptions = [
            -1  => '全部',
            0 => '女',
            1 => '男',

        ];

        if($index !== false && isset($sexOptions[$index]))
            return $sexOptions[$index];
        return $sexOptions;
    }

    static  function  CardTyeMap($index = false)
    {
        $cardType = [
            0  => '全部',
            1  => '期卡',
            2  => '次卡'
        ];

        if($index !== false && isset($cardType[$index]))
            return $cardType[$index];
        return $cardType;
    }

    /**
     * 学生卡券状态字典
     *
     * @param bool $index
     * @return array|mixed
     */
    public  static  function  StudentCardStatusMap($index = false)
    {
        $studentCardStatusMap = [
            0  => '未启用',
            1  => '启用',
            2  => '停用'
        ];
        if($index !== false && isset($studentCardStatusMap[$index]))
            return $studentCardStatusMap[$index];
        return $studentCardStatusMap;
    }


    static function classStatusOptions($index = false)
    {

        $statusOptions = [
            -1  => '全部',
            0   => '下线',
            1   => '启用',
        ];
        if($index != false && isset($statusOptions[$index]))
            return $statusOptions[$index];
        return $statusOptions;
    }

    /**
     * php 星期几中文字典
     * @param boolean $index 
     */
    public static function WeekMap($index = false)
    {
        $weekMap = [
            0 => '星期日',
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
        ];
        if($index !== false && isset($weekMap[$index]))
            return $weekMap[$index];
        return $weekMap;
    }

    /**
     * 学生签到状态字典
     *
     * @param bool $index
     * @return array|mixed
     */
    public  static  function  studentSignStatusMap($index = false)
    {
        $studentSignMap = [
            0 => '未签到',
            1 => '已签到',
            2 => '迟到',
            3 => '请假',
            4 => '旷课'
        ];
        if($index != false && isset($studentSignMap[$index]))
        {
            return $studentSignMap[$index];
        }
        return $studentSignMap;
    }


    public static  function  signTypeMap ($index = false)
    {

        $signTypeMap = [
            0 => '',
            1 => 'success',
            2 => 'gray',
            3 => 'warning',
            4 => 'danger'
        ];
        if($index != false && isset($signTypeMap[$index]))
        {
            return $signTypeMap[$index];
        }
        return $signTypeMap;
    }
}