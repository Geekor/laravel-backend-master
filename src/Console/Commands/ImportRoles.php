<?php

namespace Geekor\BackendMaster\Console\Commands;


use Illuminate\Console\Command;

use Geekor\BackendMaster\Models\Role;
use Geekor\BackendMaster\Models\Permission;
use Geekor\BackendMaster\Http\Assistants\RoleAssistant;
use Geekor\BackendMaster\Http\Assistants\PermissionAssistant;

class ImportRoles extends Command
{
    protected $signature = 'bm:import-roles';
    protected $description = '导入角色和权限信息， 可以反复调用';

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
        $this->line(' ');
        $this->line(' # '.$this->signature);
        $this->line(' ');

        //...必须想清空旧的权限缓存
        $this->call('permission:cache-reset');

        //...导入权限列表
        $this->info("");
        $this->info('import table: permissions');
        PermissionAssistant::importPermissionsToDatabase();

        //...导入角色列表
        $this->info('import table: roles');
        RoleAssistant::importRolesToDatabase();

        //...获取角色，导入默认权限
        // super_master
        $role_pms = config('bm_roles.role_permissions');
        foreach ($role_pms as $role_name => $permissions) {
            //...搜索出所有的权限名
            $pms=[];
            foreach ($permissions as $item) {
                $item = str_replace("*", "%", $item);
                foreach (Permission::where('name', 'like', $item)->pluck('name') as $name) {
                    $pms[] = $name;
                }
            }

            //...给角色赋权
            if (count($pms) > 0){
                if ($role = Role::where('name', $role_name)->first()) {
                    $role->syncPermissions($pms);
                }
            }
        }
    }
}
