<?php

namespace Geekor\BackendMaster\Models;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{

    // hidden 加了没什么效果
    //
    protected $hidden = [
        'pivot'
    ];

    //    ///////////////////////////////////////////// SCOPE ////
    public const NORMAL_SELECT = ['id', 'name', 'level', 'title', 'description'];

    public static function getNormalUsers()
    {
        return Role::select(self::NORMAL_SELECT)->where('guard_name', 'user')->get();
    }

    public function addAll(Array $data)
    {
        $rs = DB::table($this->getTable())->insert($data);
        return $rs;
    }

    ///////////////////////////////////////////// SCOPE ////

    // public function scopeSuperMaster($query)
    // {
    //     return $query->where('name', 'super_master');
    // }

    // public function scopeAdmins($query)
    // {
    //     return $query->where('guard_name', 'admin');
    // }

    // public function scopeUsers($query)
    // {
    //     return $query->where('guard_name', 'user');
    // }

    ////
}
