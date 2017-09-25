<?php

use itbdw\QiniuStorage\QiniuStorage;

function upBase64Img($base64_image_content, $path = 'images')
{
    $new_file = '';
   
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
        $disk = QiniuStorage::disk('qiniu');
        $type = $result[2];
        $name = date('YmdHis') . microtime(true) . rand(100, 999) . '.' . $type;
        $new_file = $path . '/' . $name;
        $contents = base64_decode(str_replace($result[1], '', $base64_image_content));
        $res = $disk->put($new_file,$contents); 
    }
    return $new_file;

}