<?php

/*
|--------------------------------------------------------------------------
| BM 模块配置（默认管理员导入）
|--------------------------------------------------------------------------
|
| BM: Backend Master
| package name:  geekor/backend-master
|
| 如果你不要导入默认数据，请留空
|
*/

$DEF_PSW = '123456';

return [
    'admin' => [
        'name' => '超级管理员',
        'roles' => [ 'super_master' ],
        'password' => $DEF_PSW
    ],
    'logico' => [
        'name' => '数据管理员',
        'roles' => ['data_master', 'user_master'],
        'password' => $DEF_PSW
    ],
];