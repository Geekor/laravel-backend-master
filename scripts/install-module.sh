#!/bin/bash

echo " "
echo "-------- BM: INSTALL ------"
echo " "
RET=$?

# ==== permissions ====

echo " 使用 -i 参数可以安装「权限管理库」"

if [ ${RET} -eq 0 ] && [ "$1" == "-i" ]; then
    composer require spatie/laravel-permission
    RET=$?
    echo " "
    echo "-------- BM: require spatie/laravel-permission done ------"
    echo " "
fi

# 注意[]中间要加空格
if [ ${RET} -eq 0 ]; then
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" &&
    RET=$?
    echo " "
    echo "-------- BM: publish spatie/laravel-permission provider done ------"
    echo " "
fi

# ==== backend masters ====

if [ ${RET} -eq 0 ]; then
    php artisan vendor:publish --provider="Geekor\BackendMaster\ServiceProvider"
    RET=$?
    echo " "
    echo "-------- BM: publish geekor/laravel-backend-master provider done ------"
    echo " "
fi

# ==== 执行数据库迁移 ====

if [ ${RET} -eq 0 ]; then
    read -p "你是想升级数据库(u)，还是完全重置数据库(r)? " ur
    if [ "$ur" == "u" ] || [ $ur == 'U' ]; then
        php artisan migrate
    elif [ "$ur" == "r" ] || [ $ur == "R" ]; then
        php artisan bm:fresh
    fi

    echo " "
    echo "-------- BM: database migrate done ------"
    echo " "

    php artisan route:list
    php artisan permission:show
fi
