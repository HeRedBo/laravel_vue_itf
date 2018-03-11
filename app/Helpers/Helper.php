<?php

use itbdw\QiniuStorage\QiniuStorage;
use App\Services\Logs\BLogger;
use Illuminate\Http\Request;

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


if(!function_exists('UnixToGmt')) {

    /**
     * 把时间戳转换为格林威治时间
     *
     * 建议使用php自带的 gmdate / date
     */
    function UnixToGmt($UnixTime = 0, $format_string = "l d F Y H:i:s")
    {
        $UnixTime = $UnixTime ?: time();
        return @gmdate($format_string,$UnixTime)." GMT";;
    }

}


if(!function_exists('DateTimeToGmt')) {
    /**
     * 时间日期格式转换成格林时间
     * @param int $datetime 日期时间
     * @param string $format_string 转换的格林数据格式
     * @return string
     */
    function DateTimeToGmt($datetime = 0, $format_string = "l d F Y H:i:s")
    {
        $unix_time = $datetime ? strtotime($datetime) : time();
        return @gmdate($format_string, $unix_time)." GMT";
    }
}


if(!function_exists('getClientIp')) 
{
    function getClientIp() 
    {
        $request = request();
        $request->setTrustedProxies(array('10.32.0.1/16'));  
        return $request->getClientIp();
    }
}

if(!function_exists('getNow'))
{
    /**
     * 获取当前时间的时间日期格式
     * @return false|string
     * @author Red-Bo
     */
    function getNow()
    {
        return date("Y-m-d H:i:d");
    }
}


if(!function_exists('getWeekBE'))
{
    /**
     * 获得任意一个星期的开始时间，结束时间 | 返回时间日期格式
     * @param string $day 某一天的日期时间格式
     * @return array
     */
    function getWeekBE($day)
    {
        $last_day = date('Y-m-d 23:59:59',strtotime("$day Sunday"));
        $first_day= date('Y-m-d 00:00:00',strtotime("$last_day -6 days"));
        return array($first_day, $last_day);
    }
}



if(!function_exists('getMonthBE'))
{

    /**
     * 获取一个月的开始月结束时间
     * @param  string $date
     * @return array
     */
    function getMonthBE($date = '')
    {
        if(empty($date))
            $date = date("Y-m-d");
        $first_day = date('Y-m-01 00:00:00', strtotime($date)); //月初
        $last_day = date('Y-m-d 23:59:59', strtotime("$first_day +1 month -1 day"));//月末
        return array($first_day, $last_day);
    }

    if(!function_exists('getMouthWeekCount'))
    {
        /**
         * 获取时间是的某个月的第几周
         *
         * @param  string  $date 时间日期
         * @return int
         */
        function getMouthWeekCount($date = null)
        {
            if(empty($date))
            {
                $date_time = time();
            }
            else
            {
                $date_time = strtotime($date);
            }

            $mouth_day_count = date('t', $date_time);
            $week_count      = (int)ceil($mouth_day_count/7);
            return $week_count;
        }
    }

    if(!function_exists('getDateWeekOrder'))
    {
        /**
         * 获取某个时间在某个月的月排序
         * @param string  $date
         * @return float
         */
        function getDateWeekOrder($date = null)
        {
            if(empty($date))
            {
                $date_time = time();
            }
            else
            {
                $date_time = strtotime($date);
            }
            $date_now   =date('j',$date_time); //得到今天是几号
            $cal_result =ceil ($date_now / 7); //计算是第几个星期几
            return $cal_result;
        }
    }
}


if(!function_exists('getDateWeek'))
{
    function getDateWeek($date = NULL )
    {
        if(empty($date))
        {
            $date_time = time();
        }
        else
        {
            $date_time = strtotime($date);
        }
        $w = date("w", $date_time);
        if($w == 0)
            $w =7;
        return $w;
    }
}

if(!function_exists('getDateCalendarX'))
{
    function getDateCalendarX($date = NULL)
    {
        if(empty($date))
            $date_time = time();
        else
            $date_time = strtotime($date);

        $mouth_start_day = date("Y-m-01", $date_time);
        $mouth_start_day_week = date('w', strtotime($mouth_start_day));
        if($mouth_start_day_week == 0)
            $mouth_start_day_week = 7;
        $mouth_day = date('j', $date_time);
        $offset  = $mouth_start_day_week-1;
        $val     = round(($mouth_day + $offset)/7,2) * 100;
        $min     = floor(($mouth_day + $offset)/7) *100;
        $max     = ceil(($mouth_day + $offset) / 7) * 100;
        $centre_line = round(($min + $max) /2,2);
        if($val == $min && $val == $max)
        {
            $val = $val/100;
        }
        if ($val > $min && $val < $max)
        {
            if($val < $centre_line)
            {
                $val = ceil($val/100);
            }
            if($val > $centre_line && $val < $max)
            {
                $val =  ceil($val/100);
            }

        }
        return $val;
    }
}

if(!function_exists('getMouthXLength'))
{
    function getMouthXLength($date = null)
    {
        if(empty($date))
            $date_time = time();
        else
            $date_time = strtotime($date);

        $first_day = date('Y-m-01',$date_time);
        $last_day = date('Y-m-d',strtotime("$first_day +1 month -1 day"));
        return getDateCalendarX($last_day);
    }
}








