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
}