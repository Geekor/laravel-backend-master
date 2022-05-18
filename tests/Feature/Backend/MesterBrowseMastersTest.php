<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestCase;
use Geekor\BackendMaster\Tests\Feature\Traits\AuthTokenCheck;

class MesterBrowseMastersTest extends TestCase
{
    use AuthTokenCheck;

    // 下面属性的更多说明可查看 /tests/Base/TestCase.php

    protected $my_device_name = 'php-auto-test';
    protected $my_guard_is_master = true;
    protected $my_guard_need_permission = true;
    protected $my_guard_roles = ['super_master'];
    protected $my_guard_permissions = [];

    protected $my_testing_api = '/api/backend/masters';
    protected $my_testing_method = 'get';
    protected $my_testing_params = [
        'get' => []
    ];
}
