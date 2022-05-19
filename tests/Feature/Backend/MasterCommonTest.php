<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestNormalCase;
use Geekor\BackendMaster\Tests\Base\Traits\ApiPrefixUtil;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class MasterCommonTest extends TestNormalCase
{
    use WithFaker;
    use ApiPrefixUtil;

    protected $my_testing_api = '/api/backend/just-not-exists';

    /**
     * 访问一个不存在的 API
     */
    public function test_try_a_not_exists_api()
    {
        $this->get($this->my_testing_api)->assertNotFound();
    }
}
