<?php

namespace Geekor\BackendMaster\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Geekor\BackendMaster\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory()
    {
        // 重载 HasFactory 中的 newFactory()
        // 以便使用 User::factory() 时，得到正确的 UserFactory
        
        return UserFactory::new();
    }

    public function createPlainTextToken($device_name)
    {
        return $this->createToken($device_name)->plainTextToken;
    }

    //=======================================

    /**
     * Spatie\Permission 模块需要用到
     * 
     * 如果使用了 `use HasRoles`
     * 就必须指定 $guard_name，
     * 否则可能找不到对应的角色，或无法分配角色
     */
    protected $guard_name = 'user';

    /**
     * 默认加载的关联
     *
     * @var array
     */
    protected $with = ['banned', 'roles'];

    /**
     * 用户的封禁状态
     */
    public function banned()
    {
        return $this->belongsTo(Ban::class, 'ban_id', 'id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
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
        'ban_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //////////////////////////////


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

   ////////////////////////////////////// SCOPE ////

   public function scopeWithRoles($query)
   {
       return $query->with('roles:id,name,level,title,description');
   }
}
