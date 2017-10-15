export function stack_error(error) {
    console.log(error);
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
