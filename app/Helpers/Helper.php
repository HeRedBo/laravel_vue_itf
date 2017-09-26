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


if(!function_exists('human_filesize'))
{
    /**
     * 返回可读性更好的文件尺寸
     */
    function human_filesize($bytes, $decimals = 2)
    {
        $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .@$size[$factor];
    }
}


if(!function_exists('is_image')) 
{
    /**
     * 判断文件的MIME类型是否为图片
     */
    function is_image($mimeType)
    {
        return starts_with($mimeType, 'image/');
    }
}
