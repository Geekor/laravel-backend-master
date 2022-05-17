<?php

namespace Geekor\BackendMaster\Console\Commands;

use Illuminate\Console\Command;

class Fresh extends Command
{
    protected $hidden = true; // 设置(php artisan)不可见

    protected $signature = 'bm:fresh';
    protected $description = 'Geekor Backend Master 完全重置数据库';

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

        if ($this->confirm('This will reset your database, sure?', false)) {
            $this->call('migrate:fresh');

            // 必须先导入角色信息，后续才能在创建用户时分配角色/权限
            $this->call('bm:import-roles');

            $this->call('bm:import-masters');


        } else {
            $this->info(' > You have saved the world!');
            $this->newLine();
        }

    }
}
