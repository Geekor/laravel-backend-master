<?php

namespace Geekor\BackendMaster\Http\Assistants;

use Carbon\Carbon;
use Illuminate\Support\Arr;

use Geekor\BackendMaster\Models\Permission;

class PermissionAssistant
{

    public static function importPermissionsToDatabase($cfg='bm_roles.permissions')
    {
        $attrs = [];
        $tip = [];
        $dt = Carbon::now();

        $arr = Arr::flatten( Permission::select('name')->get()->toArray() );
        foreach (config($cfg) as $name => $values) {
            if (in_array($name, $arr)) {
                continue;
            }

            foreach ($values as $k => $v) {
                $tip[$k] = $v;
            }

            $tip['name'] = $name;
            $tip['created_at'] = $dt;
            $tip['updated_at'] = $dt;
            $tip['removable'] = 0;
            $attrs[] = $tip;
        }

        if (count($attrs)>0) {
            $per = new Permission();
            $per->addAll($attrs);
        }

        //-------- 移除废弃的权限
        if ($cfgs = config('bm_roles.permissions_removed')) {
            foreach ($cfgs as $name) {
                Permission::where('name', $name)->delete();
            }
        }
    }
}
