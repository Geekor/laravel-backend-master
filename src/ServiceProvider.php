<?php

namespace Geekor\BackendMaster;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

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
        if (app()->runningInConsole()) {
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
            $this->publishes($arr, 'bm-migrations');

            // 发布 配置文件
            // ---------------
            // (拷贝到 /app/config/ 目录)
            $this->publishes([
                __DIR__.'/../config/bm.php' => config_path('bm.php'),
                __DIR__.'/../config/bm_roles.php' => config_path('bm_roles.php'),
                __DIR__.'/../config/bm_masters.php' => config_path('bm_masters.php'),
            ], 'bm-configs');

            $this->loadCommands();
        }

        $this->defineRoutes();
        $this->configureGuard();
        $this->configureMiddleware();

        $this->configureFactories();
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
     * Configure the Sanctum middleware and priority.
     *
     * @return void
     */
    protected function configureMiddleware()
    {
        // $kernel = app()->make(Kernel::class);

        // $kernel->prependToMiddlewarePriority(EnsureFrontendRequestsAreStateful::class);
    }

    protected function configureFactories()
    {
        // $this->loadFactoriesFrom(__DIR__.'/../database/factories/');
    }
}
