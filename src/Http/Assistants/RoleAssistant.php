<?php

namespace Geekor\BackendMaster\Http\Assistants;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

use Geekor\BackendMaster\Models\Role;

class RoleAssistant
{
    public static function importRolesToDatabase($cfg='bm_roles.roles')
    {
        $attrs = [];
        $tip = [];
        $dt = Carbon::now();

        $arr = Arr::flatten( Role::select('name')->get()->toArray() );

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
            $per = new Role();
            $per->addAll($attrs);
        }

        Cache::forget('role_id_map');
    }
}
