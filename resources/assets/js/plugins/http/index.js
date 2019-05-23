import axios from 'axios'
import { apiUrl } from 'config/base'
import NProgress from 'nprogress' // Progress 进度条
import { Message} from 'element-ui'
/**
 * Create Axios
 */
export const http = axios.create({
    baseURL: apiUrl,
})

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
http.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};


// Add a request interceptor 
http.interceptors.request.use(function (config) {
    // Do something before request is sent 
    NProgress.start() // 开启Progress
    return config;
  }, function (error) {
    // Do something with request error 
    return Promise.reject(error);
  });
var that = this;
// respone拦截器
http.interceptors.response.use(function (response) {
    NProgress.done() // 结束Progress
    // Do something with response data 
    return response;

     /**
    * 下面的注释为通过response自定义code来标示请求状态，当code返回如下情况为权限有问题，登出并返回到登录页
    * 如通过xmlhttprequest 状态码标识 逻辑可写在下面error中
    */
    //  const res = response.data;
    //     if (res.code !== 20000) {
    //       Message({
    //         message: res.message,
    //         type: 'error',
    //         duration: 5 * 1000
    //       });
    //       // 50008:非法的token; 50012:其他客户端登录了;  50014:Token 过期了;
    //       if (res.code === 50008 || res.code === 50012 || res.code === 50014) {
    //         MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
    //           confirmButtonText: '重新登录',
    //           cancelButtonText: '取消',
    //           type: 'warning'
    //         }).then(() => {
    //           store.dispatch('FedLogOut').then(() => {
    //             location.reload();// 为了重新实例化vue-router对象 避免bug
    //           });
    //         })
    //       }
    //       return Promise.reject('error');
    //     } else {
    //       return response.data;    
    //     }


  }, function (error) {
     
    const { response } = error
    if ([401].indexOf(response.status) >= 0) 
    {
     
        if (response.status == 401 && response.data.message != 'Unauthorized') {
          return Promise.reject(error);
        }
        // 跳转到登录页面
        window.location = '/admin/login';
    }

    if ([403].indexOf(response.status) >= 0) 
    {
        // 跳转提示用户无权限操作
        var {data} = response;
        Message({
           message: data.message,
           type: 'error',
           duration: 3 * 1000
        });
        //window.location = '/admin/error/403';
    }
    console.log('err' + error)// for debug
     // Message({
     //   message: error.message,
     //   type: 'error',
     //   duration: 5 * 1000
     // })

    NProgress.done() // 结束Progress
    return Promise.reject(error);
  });

export default function install (Vue) {
    Object.defineProperty(Vue.prototype, '$http', {
          get() {
              return http
          }
      }),
   
    Vue.prototype.can = function (as) {
        var permissions = window.Permissions;
        if ($.inArray(as, permissions)>-1 || window.User.id == 1) {
            return true;
        }
        return false;
    }
}


