<?php

namespace Geekor\BackendMaster\Console\Commands;

use Illuminate\Console\Command;

use Geekor\BackendMaster\Models\Master;
use Geekor\Core\Support\GkVerify;

class ImportMasters extends Command
{
    protected $hidden = true; // 设置(php artisan)不可见
    protected $signature = 'bm:import-masters';
    protected $description = 'Geekor Backend Master: import default masters';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->newLine();
        $this->line(' # '.$this->signature);
        $this->newLine();

        // ------------------------------------
        $psw = '123456';
        if (config('app.env') !== 'local') {
            $psw = $this->ask('请输入管理员密码');
        }
        $defs = [
            'admin' => [
                'name' => '超级管理员',
                'roles' => [ 'super_master' ],
                'password' => $psw
            ]
        ];

        // 从配置导入管理员信息，
        // 如果没有配置文件就导入 $defs 默认值
        $masters = config('bm_masters', $defs);

        foreach ($masters as $username => $data) {
            if ($admin = Master::updateOrCreate(['username' => $username], [
                'name' => $data['name'],
                'username' => $username,
                'password' => GkVerify::makeHash($psw),
            ])) {
                $admin->syncRoles($data['roles']);

                $this->info(vsprintf(' >  设置管理员：[%s]', [$username]));
                $this->info(vsprintf(' >> 分配角色： %s', [implode(", ", $data['roles'])]));
                $this->newLine();
            }
        }

        $this->newLine();
        $this->info(' >> import finished!');
        $this->newLine();

        return 0;
    }
}
