<?php

namespace Geekor\BackendMaster\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class Master extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Spatie\Permission 模块需要用到
     * 
     * 如果使用了 `use HasRoles`
     * 就必须指定 $guard_name，
     * 否则可能找不到对应的角色，或无法分配角色
     */
    protected $guard_name = 'master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 获取所有我拥有的权限名
     */
    public function getAllPermissionNames()
    {
        $names = [];
        foreach ($this->getAllPermissions() as $p) {
            $names[] = $p->name;
        }

        return $names;
    }

    // ////////////////////////////////////// SCOPE ////

    public function scopeWithRoles($query)
    {
        return $query->with('roles:id,name,level,title,description');
    }
}
