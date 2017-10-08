export function stack_error(error) {
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