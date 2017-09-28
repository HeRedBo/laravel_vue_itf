<?php

use itbdw\QiniuStorage\QiniuStorage;
use App\Services\Logs\BLogger;

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

/**
 * 判断字符串是否是base64编
 * @param string $image_content
 * @return bool 返回数据校验结果
 */
function checkBase64Image($image_content)
{
    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $image_content))
    {
        return true;
    }
    return false;
}

/**
 * @param string $data 日志内容
 * @param string $level 日志级别
 * @param string $filename 日志文件名称
 * @return bool
 */
function logResult($data, $level = 'info', $filename = '')
{
    $filename = $filename ?: BLogger::LOG_INFO;
    $levels = [
        'emergency', 'alert', 'critical',
        'error', 'warning', 'notice',
        'info', 'debug'
    ];
    if(!empty($level) && !in_array($level, $levels))
        $level = 'info';
    BLogger::getLogger($filename)->$level($data);
    return true;
}

if(!function_exists('error'))
{
   
    function error($msg = 'error', array $data = [])
    {
        return ['status' => 0, 'msg' => $msg, 'data' => $data];
    }
}

if(!function_exists('success'))
{
    function success($msg = 'success', array  $data = [])
    {
        return ['status' => 1, 'msg' => $msg, 'data' => $data];
    }
}

