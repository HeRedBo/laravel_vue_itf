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

}