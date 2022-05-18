<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Tests\TestCase; // 注意：这里是用的主项目中的基类，如果有改过 namespace 这里也要改
use Geekor\BackendMaster\Tests\Feature\Traits\AuthTokenCheck;

class MesterGetInfoTest extends TestCase
{
    use AuthTokenCheck;

    protected $my_testing_api = '/api/backend/auth/info';
    protected $my_testing_method = 'get';
    protected $my_device_name = 'php-auto-test';
}
