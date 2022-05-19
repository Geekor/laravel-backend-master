<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

// 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Tests\TestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class MasterCommonTest extends TestCase
{
    use WithFaker;

    /**
     * 访问一个不存在的 API
     */
    public function test_try_a_not_exists_api()
    {
        $this->get('/api/just-not-exists')->assertNotFound();
    }
}
