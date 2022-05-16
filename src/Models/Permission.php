<?php

namespace Geekor\BackendMaster\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    public function addAll(Array $data)
    {
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }
}
