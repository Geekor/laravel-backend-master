# laravel-backend-master

本模块适合新建项目时快速配置后台 RESTful 相关的接口。
## 如何使用？

1. 新建项目 (目录名例如： bm-demo)

```sh
composer create-project laravel/laravel bm-demo --prefer-dist
cd bm-demo
```

2. 修改 .env 文件，配置数据库连接信息。

3. 安装依赖

```sh
composer require geekor/laravel-backend-master:*
```

上面的是安装 dev 版，也可以指定版本；

4. 一键配置后台

```sh
./vendor/geekor/laravel-backend-master/scripts/install-module.sh
```

## 其他说明
本目录中的 composer.json 只用于发布到 packagist.org 仓库中。

如果只是在本地简单的使用，在按下面步骤完成 1 和 2 后，直接在项目根目录运行下面的脚本即可：

```sh
./_packages/geekor/laravel-backend-master/scripts/install-module.sh
```

如不使用权限系统，可以不使用上面的脚本，手动完成下面的步骤

1. 项目根目录中的 `/composer.json` 文件中加入自动导入配置
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",

            "Geekor\\BackendMaster\\": "_packages/geekor/laravel-backend-master/src/",
        }
    }
}
```

2. 在 `/config/app.php` 添加服务

```php
    //...

    'providers' => [
        //...

        \Geekor\BackendMaster\ServiceProvider::class,
    ],

    //...
```

3. 生成配置

```sh
php artisan vendor:publish --provider="Geekor\BackendMaster\ServiceProvider"
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

注意： 添加自定义命令后，需要添加到 `src/ServiceProvider.php` 中的 `COMMANDS`.


## 添加拓展包的测试用例（在本地开发拓展包阶段）

- 添加包发现到根目录 composer.json
```json
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/",

        "Geekor\\BackendMaster\\Database\\Factories\\": "_packages/geekor/laravel-backend-master/database/factories/",
        "Geekor\\BackendMaster\\Tests\\": "_packages/geekor/laravel-backend-master/tests/"
    }
},
```

- 添加拓展包中的测试用例到根目录 phpunit.xml 文件中的 `<testsuites>` 字段

```xml
    <testsuites>
        <testsuite name="GeekorBackendMasterFeature">
            <directory suffix="Test.php">./_packages/geekor/laravel-backend-master/tests/Feature</directory>
        </testsuite>
        <testsuite name="GeekorBackendMasterUnit">
            <directory suffix="Test.php">./_packages/geekor/laravel-backend-master/tests/Unit</directory>
        </testsuite>
    </testsuites>
```

- 添加 Feature 测试用例

```php
<?php
namespace Geekor\BackendMaster\Tests\Feature;

use Geekor\BackendMaster\Tests\Base\TestAuthCase;
use Geekor\BackendMaster\Tests\Feature\Traits\AuthTokenCheck;

class BrowseAdminsTest extends TestAuthCase
{
    use AuthTokenCheck;

    // 下面属性的更多说明可查看 /tests/Base/TestAuthCase.php

    /** 用户登录时生成 TOKEN 需要的参数，用于表明是在哪台设备登录 */
    protected $my_device_name = 'php-auto-test';

    /** 标记当前测试 API 是管理员后台还是普通用户后台 */
    protected $my_guard_is_master = true;
    /** 标记当前 API 是否需要特定的角色/权限才能访问 */
    protected $my_guard_need_permission = true;
    /** 需要的特定角色/权限 */
    protected $my_guard_roles = ['super_admin'];
    protected $my_guard_permissions = [];

    /** 当前测试的 API */
    protected $my_testing_api = '/api/backend/admins';
    /** 测试 API 需要的请求方式 */
    protected $my_testing_method = 'get';
    /** 参数 */
    protected $my_testing_params = [];
}

```
