<?php

namespace Geekor\BackendMaster\Models;

class Ban extends BaseModel
{
    protected $fillable = [
        'name', 'title', 'description', 'color'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
