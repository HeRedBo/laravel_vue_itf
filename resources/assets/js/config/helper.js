export function stack_error(error) {
    console.log(error)
    var that = this;
    if(!error.hasOwnProperty('response')) {
        if(typeof error == 'object' && error instanceof Error)
        {
            
            var message = error.name + " : " + error.message;
            toastr.error(message);
        }
        return ;

    }
    const {response} = error;
    if(typeof response.data == 'string') {
        toastr.error(response.status + ' ' + response.statusText);
    } 
    else 
    {
     
        let data = response.data;
        let content  = '';
        if(data.hasOwnProperty('data') && data.hasOwnProperty('message') && data.hasOwnProperty('code')) 
        {
            if(isEmpty(data.data))
            {
                toastr.error(data.code + ' ' + data.message)
            }
        } 
        else if(typeof data == 'object')
        {
            var errorStr = '';
            for (var Key in data) {
                errorStr += ''+ Key+':'+data[Key]+';';
            }
            toastr.error(errorStr,'出错啦！');
            // Object.keys(data).map(function(key, index) {
            //     let value = data[key];
            //     content += '<span style="color: #e74c3c">' + value[0] + '</span><br>';
            // });
           
            // swal({
            //     title: "Error Text!",
            //     type: 'error',
            //     text: content,
            //     html: true
            // });


        }
        
    }
}

function isEmpty(obj) 
{
    if (obj == null) return true;
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }
    return true;
}

export function str_repeat(str, num) { 
    return new Array( num + 1 ).join( str ); 
} 

/**
 * 组装常规url请求参数
 * @param mixed param 需要组装的参数
 * @param {*} key 
 */
export function paraseParam(param, key) {
    var paramStr=""; 
    if(typeof param === 'string' || typeof param === 'number' || typeof param === 'boolean')
    {
        paramStr+="&"+key+"="+encodeURIComponent(param);
    }
    else
    {
       for (var i in param) 
       {
           var k = key == null ? i : key + (param instanceof Array ? "["+i+"]" :i);
           paramStr += '&' + parseParam(param[i], k);
           
       }
    } 
    return paramStr.substr(1);
}

/**
 * 组装 l5-response repository 搜索搜索函数
 * @param {mixed} param 
 * @param {*} key 
 */
export function parseSearchParam(param,key)
{
    var paramStr=""; 
    if(typeof param === 'string' || typeof param === 'number' || typeof param === 'boolean'){
        paramStr+=";"+key+":"+param;
    }
    else
    {
       for (var i in param) 
       {
           var k = key == null ? i : key + (param instanceof Array ? "["+i+"]" :i);
           paramStr += ';' + parseSearchParam(param[i], k);
           
       }
    } 
    return paramStr.substr(1);
}

export function isMobile(mobile)
{
    if(mobile != '')
    {
        if((/^1[3|4|5|7|8][0-9]{9}$/.test(mobile)))
        {
            return true;
        }
    } 
    return false;
}

/**
 * 转换js 格林格式时间为时间格式
 * @param  string time  需要转换的格林时间
 * @param  数据转换格式 cFormat 
 * @return string 返回数据转换后的结果
 */
export function parseTime(time, cFormat) 
{
   if (arguments.length === 0) {
     return null
   }
   const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}'
   let date
   if (typeof time === 'object') 
   {
     date = time
   } 
   else 
   {
     if (('' + time).length === 10) time = parseInt(time) * 1000
     date = new Date(time)
   }
   const formatObj = {
         y: date.getFullYear(),
         m: date.getMonth() + 1,
         d: date.getDate(),
         h: date.getHours(),
         i: date.getMinutes(),
         s: date.getSeconds(),
         a: date.getDay()
   }
   const time_str = format.replace(/{(y|m|d|h|i|s|a)+}/g, (result, key) => {
        let value = formatObj[key]
        if (key === 'a') return ['一', '二', '三', '四', '五', '六', '日'][value - 1]
        if (result.length > 0 && value < 10) {
            value = '0' + value
         }
     return value || 0
   })
   return time_str
 }
