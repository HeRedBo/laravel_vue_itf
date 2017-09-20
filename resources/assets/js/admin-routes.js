/* layout */
import Layout from './views/admin/layout/Layout'


export default [
    { path: '/admin/login', component: () => import('views/admin/login/index.vue')},
    {
        path: '/admin',
        component : Layout,
        name : '首页',
        children : [
            {
               path: 'dashboard', component : () => import('views/admin/dashboard/index.vue'),
            }
        ]
    }

];


