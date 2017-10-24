/* layout */
import Layout from './views/admin/layout/Layout.vue'
import Parent from './views/admin/layout/Parent.vue'


export default [
    { path: '/admin/login', component: () => import('views/admin/login/index.vue')},
    {
        path: '/admin',
        component : Layout,
        redirect: '/admin/dashboard',
        name: '',
        children : [
            {
               name : '控制面板',
               path: 'dashboard', component : () => import('views/admin/dashboard/index.vue')
               
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
                        path: 'Index',
                        name : '道馆列表',
                        component : () => import('views/admin/venue/Index.vue'),
                    },

                    {
                        path: 'Table',
                        name : '表格',
                        component : () => import('views/admin/venue/Index_table.vue'),
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

            },
            {
                path : 'role',
                component:Parent,
                name : '角色管理',
                children : [
                    {
                        path: 'index',
                        name : '角色列表',
                        component : require('./views/admin/role/Index.vue')
                    },
                    {
                        path: 'setacl/:id',
                        name: '设置权限',
                        component: () => import('views/admin/role/Acl.vue')
                    }
                ]
            },
            {
                path : 'permission',
                component:Parent,
                name : '权限管理',
                children : [
                    {
                        path: 'index',
                        name : '权限列表',
                        component : require('./views/admin/permission/Index.vue')
                    }
                ]
            },
            {
                path : 'user',
                component:Parent,
                name : '用户管理',
                children : [
                    {
                        path: 'create',
                        name : '用户新增',
                        component : () => import('views/admin/user/Create.vue')
                    },
                    {
                        path: 'update/:id',
                        name : '角色编辑',
                        component : () => import('views/admin/user/Update.vue')
                    },
                    {
                        path: 'index',
                        name : '用户列表',
                        component : () => import('views/admin/user/Index.vue')
                    },
                ]
            },
            // {
            //     path : 'error',
            //     component:Parent,
            //     name : '错误',
            //     children : [
            //         {
            //             path: '403',
            //             name : '403错误',
            //             component : () => import('views/admin/errorPage/error403.vue')
            //         }
            //     ]
            // },
            {
                path: 'class',
                component : Parent,
                name : '班级',
                children : [
                     {
                        path: 'index',
                        name: '班级列表',
                        component : () => import('views/admin/class/Index.vue')
                     }
                  
                ]
            }

        ]
    }

];


