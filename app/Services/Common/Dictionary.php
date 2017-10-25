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
}