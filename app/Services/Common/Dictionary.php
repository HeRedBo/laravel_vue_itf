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
        if($index != false && isset($unitOptions))
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

        if($index != false && isset($sexOptions[$index]))
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

        if($index != false && isset($cardType[$index]))
            return $cardType[$index];
        return $cardType;
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
        if($index != false && isset($weekMap[$index]))
            return $weekMap[$index];
        return $weekMap;
    }
}