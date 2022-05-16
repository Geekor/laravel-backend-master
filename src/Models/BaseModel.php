<?php

namespace Geekor\BackendMaster\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * 指定不可被修改的字段
     */
    protected $guarded = ['id'];
}
