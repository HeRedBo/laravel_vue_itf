import axios from 'axios'
import { apiUrl } from 'config/base'

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

// respone拦截器
http.interceptors.response.use(function (response) {
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
    if ([401].indexOf(response.status) >= 0) {
        if (response.status == 401 && response.data.error.message != 'Unauthorized') {
          return Promise.reject(response);
        }
        window.location = '/login';
      }
     console.log('err' + error)// for debug
     Message({
       message: error.message,
       type: 'error',
       duration: 5 * 1000
     })
     return Promise.reject(error);
  });

  export default function install (Vue) {
      Object.defineProperty(Vue.prototype, '$http', {
          get() {
              return http
          }
      })
  }