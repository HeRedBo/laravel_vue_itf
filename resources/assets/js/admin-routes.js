/* layout */
import Layout from './views/admin/layout/Layout.vue'
import Parent from './views/admin/layout/Parent.vue'


export default [
    { path: '/admin/login', component: () => import('views/admin/login/index.vue')},
    {
        path: '/admin',
        component : Layout,
        redirect: '/admin/dashboard',
        name: '首页',
        children : [
            {
               path: 'dashboard', component : () => import('views/admin/dashboard/index.vue'),
            },
            {
                path : 'venue',
                name : '道馆管理',
                component : Parent,
                children : [
                    {
                        path: 'create',
                        name : '添加道馆',
                        component : () => import('views/admin/venue/Create.vue'),
                    },
                    {
                        path: 'update/:id',
                        name : '编辑道馆',
                        component : () => import('views/admin/venue/Edit.vue'),
                    },
                    {
                        path: 'form',
                        name : '表单测试',
                        component : () => import('views/admin/venue/Form.vue'),
                    },
                    {
                        path: 'pantbumb',
                        name : '图片上传',
                        component : () => import('views/admin/venue/pantbumb.vue'),
                    },
                    {
                        path: 'distpicker',
                        name : '省市区联动',
                        component : () => import('views/admin/venue/Distpicker.vue'),
                    }
                ]
                // component : require('views/admin/venue/Index.vue')

            }
            // ,
            // {
            //     path : 'role',
            //     component:Parent,
            //     name : '角色管理',
            //     children : [
            //         {
            //             path: 'index',
            //             name : '权限列表',
            //             component : require('./views/admin/role/Index.vue')
            //         },
            //         {
            //             path: 'create',
            //             name : '添加角色',
            //             component : require('./views/admin/role/Create.vue')
            //         },
            //          {
            //             path: 'update/:id',
            //             name : '编辑角色',
            //             component : require('./views/admin/role/Update.vue')
            //         },
            //         {
            //             path: 'setacl/:id',
            //             name: '设置权限',
            //             component: require('./views/admin/role/Acl.vue')
            //         }
            //     ]
            // },
        ]
    }

];


