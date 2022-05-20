<?php

namespace Geekor\BackendMaster;

use Illuminate\Filesystem\Filesystem;
use App\Http\Kernel as AppKernel;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        config([
            //------------------------------------- 增加 guards [auth:user]
            // guards 是单数
            'auth.guards.user' => array_merge([
                'driver' => 'sanctum',
                'provider' => 'bm_users',
            ], config('auth.guards.user', [])),

            // providers 是复数
            'auth.providers.bm_users' => array_merge([
                'driver' => 'eloquent',
                'model' => \Geekor\BackendMaster\Models\User::class,
            ], config('auth.providers.bm_users', [])),

            //------------------------------------- 增加 guards [auth:master]
            // guards 是单数
            'auth.guards.master' => array_merge([
                'driver' => 'sanctum',
                'provider' => 'masters',
            ], config('auth.guards.master', [])),

            // providers 是复数
            'auth.providers.masters' => array_merge([
                'driver' => 'eloquent',
                'model' => \Geekor\BackendMaster\Models\Master::class,
            ], config('auth.providers.masters', [])),
        ]);

        if (! app()->configurationIsCached()) {
            // 记得还要在下面的 publish 再添加一次
            $this->mergeConfigFrom(__DIR__.'/../config/bm.php', 'bm');
            $this->mergeConfigFrom(__DIR__.'/../config/bm_roles.php', 'bm_roles');
            $this->mergeConfigFrom(__DIR__.'/../config/bm_masters.php', 'bm_masters');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslations();

        if (app()->runningInConsole()) {

            $this->loadCommands();

            $this->loadConfigs();

            $this->loadMigrations();
        }

        /**
         * factoies 已经在 compser.json 中通过 autoload 配置了，所以不用手动 load 啦
         */
        $this->defineRoutes();
        $this->configureGuard();
        $this->configureMiddleware();
    }

    /**
     * Register master's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        // if (Master::shouldRunMigrations()) {
        //     return $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName)
    {
        $timestamp = date('Y_m_d');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }

    /**
     * 导入数据库迁移配置
     */
    protected function loadMigrations()
    {
        // .stub 的迁移文件不会在 php artisan migrate 中出现
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // 发布迁移文件（不管理日期）
        // ---------------------
        // 这方法可以批量发布整个目录的文件，
        // 但是日期是固定的，而且要先设置好
        // $this->publishes([
        //     __DIR__.'/../database/migrations' => database_path('migrations'),
        // ]);

        // 发布迁移文件（自动自定日期为当前）
        // ----------------------------
        // 下面的两个配置其实是可以合并的，但是为了先创建权限表，所以才拆开

        $arr = [];
        foreach ([
            // 为了保住能顺利排序，最好从 250000 之后开始
            '300000_create_permission_extra_info.php',
            '400000_create_backend_masters_table.php',
            '500010_create_bans_table.php',
            '500020_create_configures_table.php',
        ] as $f) {
            $arr[vsprintf('%s/../database/migrations/%s.stub',[__DIR__, $f])] = $this->getMigrationFileName($f);
        }
        $this->publishes($arr, 'geekor-bm-migrations');
    }

    /**
     * 导入翻译
     */
    protected function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', AppConst::LANG_NAMESPACE);

        // TODO:
        //-------
        // 目前来说没必要把翻译发布出去，
        // 后续可以考虑通过调用命令的方式发布到主项目
        //
        // if (app()->runningInConsole()) {
        //     $this->publishes([
        //         __DIR__.'/../lang' => app()->langPath('vendor/' . AppConst::LANG_NAMESPACE),
        //     ], AppConst::LANG_NAMESPACE . '-lang');
        // }
    }

    protected function loadConfigs()
    {
        // 发布 配置文件
        // ---------------
        // (拷贝到 /app/config/ 目录)
        $this->publishes([
            __DIR__.'/../config/bm.php' => config_path('bm.php'),
            __DIR__.'/../config/bm_roles.php' => config_path('bm_roles.php'),
            __DIR__.'/../config/bm_masters.php' => config_path('bm_masters.php'),
        ], 'geekor-bm-configs');
    }

    /**
     * 导入 BM 的命令行命令
     */
    protected function loadCommands()
    {
        // 用于注册命令行命令（手动填写的方式）
        // $this->commands([
        //     Console\Commands\Check::class,
        //     Console\Commands\Fresh::class,
        //     Console\Commands\MakeApiDoc::class,
        //     Console\Commands\ImportMasters::class,
        //     Console\Commands\ImportRoles::class,
        // ]);

        // --------------------------- 自动扫描目录中的命令 ----
        // [1] 扫描出 php 文件
        // [2] 把 .../Commands/Check.php 转换成 Geekor\BackendMaster\Console\Commands\Check 的形式
        // [3] 注册
        $dir = '/Console/Commands/';
        $path = __DIR__.$dir;

        // [1]
        $fs = $this->app->make(Filesystem::class);
        $list = $fs->glob($path.'*.php');

        if (count($list) > 0) {
            $cmds = [];
            $tmp = [];

            // [2]
            foreach ($list as $txt) {
                $tmp = substr($txt, strrpos($txt, '/')+1);
                $tmp = substr($tmp, 0, strrpos($tmp, '.'));
                $cmds[] = vsprintf('%s%s%s', [
                    __NAMESPACE__,
                    str_replace('/', '\\', $dir),
                    $tmp
                ]);
            }

            // [3]
            $this->commands($cmds);
        }
    }

    /**
     * Define the Sanctum routes.
     *
     * @return void
     */
    protected function defineRoutes()
    {
        if (app()->routesAreCached()) {
            return;
        }

        if (config('bm.routes.backend') === true) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api/backend.php');
        }

        if (config('bm.routes.normal') === true) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api/normal.php');
        }

        if (config('bm.routes.token') === true) {
            $this->loadRoutesFrom(__DIR__.'/../routes/api/token.php');
        }
    }

    /**
     * Configure the Sanctum authentication guard.
     *
     * @return void
     */
    protected function configureGuard()
    {
        // Auth::resolved(function ($auth) {
        //     $auth->extend('sanctum', function ($app, $name, array $config) use ($auth) {
        //         return tap($this->createGuard($auth, $config), function ($guard) {
        //             app()->refresh('request', $guard, 'setRequest');
        //         });
        //     });
        // });
    }

    /**
     * Register the guard.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @param  array  $config
     * @return RequestGuard
     */
    // protected function createGuard($auth, $config)
    // {
    //     return new RequestGuard(
    //         new Guard($auth, config('sanctum.expiration'), $config['provider']),
    //         request(),
    //         $auth->createUserProvider($config['provider'] ?? null)
    //     );
    // }

    /**
     * 添加中间件
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        $routeMiddlewares = [
            'bm.login' => Http\Middleware\AppAccessCheck::class
        ];

        // Kernel 在 App 中是单例，
        // 通过 make() 实际只是取得单例。
        $kernel = app()->make(Kernel::class);

        if ($kernel instanceof AppKernel) {

            // ----- 下面是要注册到 全局中间件列表

            // AutoSettingLocale 这个翻译的必须要优先设置
            $kernel->prependMiddleware(Http\Middleware\AutoSettingLocale::class);

            // 根据配置来判断是否加载 请求头的检测
            if (config('bm.use_api_headers_check', false)) {
                $kernel->prependMiddleware(Http\Middleware\ApiHeadersCheck::class);
            }

            // ----- 下面是要注册到 路由中间件列表

            foreach ($routeMiddlewares as $key => $middleware) {
                $kernel->appendToRouteMiddleware($key, $middleware);
            }

            // 下面的方法不起作用啊 ....
            // $router = app('kernel');
            // $router = app()->make(\Illuminate\Routing\Router::class);
            // if (PHP_VERSION_ID >= 80000) {
            //     // var_dump($router);
            //     foreach ($routeMiddlewares as $key => $middleware) {
            //         $router->aliasMiddleware($key, $middleware);
            //     }
            // } else {
            //     foreach ($routeMiddlewares as $key => $middleware) {
            //         $router->middleware($key, $middleware);
            //     }
            // }
            // var_dump($kernel->getRouteMiddleware());
        }

    }
}
