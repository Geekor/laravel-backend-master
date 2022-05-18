<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestCase;
use Geekor\BackendMaster\Tests\Feature\Traits\AuthTokenCheck;

class MesterGetInfoTest extends TestCase
{
    use AuthTokenCheck;

    protected $my_device_name = 'php-auto-test';
    protected $my_guard_is_master = true;
    protected $my_testing_api = '/api/backend/auth/info';
    protected $my_testing_method = 'get';
    protected $my_testing_params = [
        'get' => []
    ];
}
