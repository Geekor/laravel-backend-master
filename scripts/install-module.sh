#!/bin/bash

echo " "
echo "-------- BM: INSTALL ------"
echo " "
RET=$?

# ==== permissions ====

if [ ${RET} -eq 0 ] && [ "$1" != '-x' ]; then
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
    # 等 1 秒，保证【权限】表先创建
    sleep 1
    php artisan vendor:publish --provider="Geekor\BackendMaster\MasterServiceProvider"
    RET=$?
    echo " "
    echo "-------- BM: publish geekor/backend-master provider done ------"
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
