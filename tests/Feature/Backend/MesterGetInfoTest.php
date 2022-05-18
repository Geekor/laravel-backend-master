<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Models\Master;
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

    public function test_call_api_success()
    {
        $arr = $this->makeMasterUserAndToken();

        $resp = $this->withToken($arr['token'])->getJson($this->myTestingApi());
        $resp->assertOk()->assertJsonStructure([
            'id', 'username', 'name'
        ]);
    }
}
