# laravel-backend-master



## 其他说明
本目录中的 composer.json 只用于发布到 packagelist 仓库中。

如果只是在本地简单的使用，在按下面步骤完成 1 和 2 后，直接在项目根目录使用脚本即可

```sh
./_packages/geekor/backend-master/scripts/install-module.sh
```

如不使用权限系统，可以不使用上面的脚本，手动完成下面的步骤

1. 项目根目录中的 `/composer.json` 文件中加入自动导入配置
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",

            "Geekor\\BackendMaster\\": "_packages/geekor/backend-master/src/",
        }
    }
}
```

2. 在 `/config/app.php` 添加服务

```php
    //...

    'providers' => [
        //...

        \Geekor\BackendMaster\MasterServiceProvider::class,
    ],

    //...
```

3. 生成配置

```sh
php artisan vendor:publish --provider="Geekor\BackendMaster\MasterServiceProvider"
```

4. 更新缓存

```sh
composer dump-autoload
```

5. 执行数据库迁移（如果刚刚自动生成的 迁移文件排序不再最新，请自行修改文件名）

```sh
php artisan migrate
```

如果是项目刚刚开始构建，下面的命令获取对你有用：

```sh
php artisan bm:check    #环境自查
php artisan bm:import-masters  #生成默认管理员（此命令为不可见命令）
php artisan bm:import-roles  #导入默认权限

php artisan bm:refresh  #重建数据库（慎用，此命令为不可见命令）
```
更多命令可以自定查看 `src/Console/Commands/` 目录。

注意： 添加自定义命令后，需要添加到 `src/MasterServiceProvider.php` 中的 `COMMANDS`.



