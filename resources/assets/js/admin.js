
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import httpPlugin from 'plugins/http';
import VueRouter from 'vue-router';
import routes from './admin-routes.js';
import ElementUI from 'element-ui';
import App from './App.vue';
import store from './store';
import axios from 'axios'
import NProgress from 'nprogress' // Progress 进度条


Vue.use(VueRouter);
Vue.use(ElementUI);
Vue.use(httpPlugin);

require('jstree');
require('admin-lte');
require('icheck');

window.swal = require('sweetalert2');
window.toastr = require('toastr');
window.toastr.options = {
    closeButton: true,
	positionClass: "toast-top-right",
    showDuration: "300",
    hideDuration: "1000",
    timeOut: "5000",
    extendedTimeOut: "1000",
    showEasing: "swing",
    hideEasing: "linear",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
}

// 全局组件
Vue.component(
	'vTable',
	require('components/admin/Table.vue')
);



/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// 3. 创建 router 实例，然后传 `routes` 配置
// 你还可以传别的配置参数, 不过先这么简单着吧。
const router = new VueRouter({
    mode: 'history',
    base: __dirname,
    linkActiveClass: 'active',
    routes: routes
});

router.beforeEach((to, from, next) => {
   
    if (!window.User) {
        return next('/admin/login')
    }
    
    NProgress.start() // 开启Progress
    var url = '/admin/checkAcl', path =to.path;

    if(path == '/admin/login') 
    {
        NProgress.done() // 结束Progress
        return next();
    }
    axios({
        method:'GET',
            url:url,
            responseType:'json',
            params :{
                path:path,
                _token : Laravel.csrfToken
            }
       })
      .then(function(response) {
            NProgress.done() // 结束Progress
            let {data} = response;
            let responseData = data.data;

            if(!responseData.status) {
                return next({ path: '/admin/error'});
            }
      })
     .catch(function(error) {
         NProgress.done() // 结束Progress
         return next({ path: '/admin/login' });
     });
    
        NProgress.done() // 结束Progress
        return next();
    });
    

// 4. 创建和挂载根实例。
// 记得要通过 router 配置参数注入路由，
// 从而让整个应用都有路由功能
const app = new Vue(Vue.util.extend({ router,store },App)).$mount('#app');


