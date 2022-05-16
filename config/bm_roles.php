<?php

/*
|--------------------------------------------------------------------------
| BM 模块配置（角色/权限管理）
|--------------------------------------------------------------------------
|
| BM: Backend Master
| Package Name:  geekor/backend-master
|
| 本配置主要配合命令 php artisan bm:import-roles 一起使用
|
*/

return [
    'permissions' => [
        'master:backend-login' => [ 'guard_name' => 'master', 'title' => '网页登录认证', 'description' => '可以在前端登录'  ],
        'master:backend-changelog-b' => [ 'guard_name' => 'master', 'title' => '查看后台更新日志', 'description' => '可以在后台管理界面查看后台更新日志'  ],

        'master:user-b'     => [ 'guard_name' => 'master', 'title' => '浏览用户', 'description' => '浏览普通（消费者）用户列表'  ],
        'master:user-r'     => [ 'guard_name' => 'master', 'title' => '查看用户', 'description' => '查看一个普通（消费者）用户信息'  ],
        'master:user-a'     => [ 'guard_name' => 'master', 'title' => '添加用户', 'description' => '创建一个普通（消费者）用户'  ],
        'master:user-e'     => [ 'guard_name' => 'master', 'title' => '编辑用户', 'description' => '编辑一个普通（消费者）用户的信息'  ],
        'master:user-d'     => [ 'guard_name' => 'master', 'title' => '删除用户', 'description' => '删除一个普通（消费者）用户'  ],

        'master:role-b'     => [ 'guard_name' => 'master', 'title' => '浏览角色', 'description' => '浏览角色列表'  ],
        'master:role-r'     => [ 'guard_name' => 'master', 'title' => '查看角色', 'description' => '查看一个角色信息'  ],
        'master:role-a'     => [ 'guard_name' => 'master', 'title' => '添加角色', 'description' => '创建一个角色'  ],
        'master:role-e'     => [ 'guard_name' => 'master', 'title' => '编辑角色', 'description' => '编辑一个角色的信息'  ],
        'master:role-d'     => [ 'guard_name' => 'master', 'title' => '删除角色', 'description' => '删除一个角色'  ],

        //---------------- 普通用户 -----
        'user:article-b'  => [ 'guard_name' => 'user', 'title' => '浏览文章', 'description' => '浏览文章列表'  ],
        'user:article-r'  => [ 'guard_name' => 'user', 'title' => '查看文章', 'description' => '查看文章信息'  ],
        'user:article-a'  => [ 'guard_name' => 'user', 'title' => '添加文章', 'description' => '创建文章'  ],
        'user:article-e'  => [ 'guard_name' => 'user', 'title' => '编辑文章', 'description' => '编辑我的文章'  ],
        'user:article-d'  => [ 'guard_name' => 'user', 'title' => '删除文章', 'description' => '删除我的文章'  ],
    ],

    'permissions_removed' => [ //已废除的权限
    ],

    'roles' => [
        'user'         => [ 'guard_name' => 'user', 'removable' => 0, 'level' => 1,    'title' => '用户',   'description' => '普通（仅可浏览）用户' ],
        'tester'       => [ 'guard_name' => 'user', 'removable' => 0, 'level' => 5,    'title' => '测试员', 'description' => '普通（仅可浏览）用户-内部测试员' ],
        'member'       => [ 'guard_name' => 'user', 'removable' => 0, 'level' => 10,   'title' => '会员',   'description' => '可以发表动态的用户' ],
        'author'       => [ 'guard_name' => 'user', 'removable' => 0, 'level' => 100,  'title' => '作者',   'description' => '可以发布文章的用户' ],
        'spokesman'    => [ 'guard_name' => 'user', 'removable' => 0, 'level' => 900,  'title' => '发言人', 'description' => '可以管理话题的用户，官方发言人' ],

        'data_master'  => [ 'guard_name' => 'master', 'removable' => 0, 'level' => 5000, 'title' => '数据管理员', 'description' => '云端数据分析员' ],
        'user_master'  => [ 'guard_name' => 'master', 'removable' => 0, 'level' => 8000, 'title' => '用户管理员', 'description' => '普通用户管理员' ],
        'super_master' => [ 'guard_name' => 'master', 'removable' => 0, 'level' => 9999, 'title' => '超级管理员', 'description' => '后台超级管理员' ],
    ],

    //...角色关联权限
    'role_permissions' => [
        // 管理员
        'super_master' => [ 'master:*', ],
        'user_master'  => [ 'master:user-*', 'master:backend-login', ],
        'data_master'  => [
            'master:data-*',
            'master:article-*',
            'master:event-*',
            'master:topic-*',
            'master:backend-login',
        ],

        // 普通用户
        'spokesman' => [
            'user:event-*',
            'user:article-*',
            'user:topic-*',
        ],
        'author'    => [
            'user:event-*',
            'user:article-*',
            'user:topic-*',
        ],
        'member'    => [
            'user:event-*',

            'user:topic-b',
            'user:topic-r',
            'user:article-b',
            'user:article-r',
        ],
        'tester'    => [
            'user:event-b',
            'user:event-r',
            'user:topic-b',
            'user:topic-r',
            'user:article-b',
            'user:article-r',
        ],
        'user'      => [
            'user:event-b',
            'user:event-r',
            'user:topic-b',
            'user:topic-r',
            'user:article-b',
            'user:article-r',
        ],
    ],
];
