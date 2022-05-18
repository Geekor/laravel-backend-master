<?php

namespace Geekor\BackendMaster\Tests\Feature\Backend;

use Geekor\BackendMaster\Tests\Base\TestCase;
use Geekor\BackendMaster\Tests\Feature\Traits\AuthTokenCheck;

class MesterLogoutTest extends TestCase
{
    use AuthTokenCheck;

    protected $my_device_name = 'php-auto-test';
    protected $my_guard_is_master = true;
    protected $my_guard_need_permission = false;
    protected $my_guard_roles = [];
    protected $my_guard_permissions = [];

    protected $my_testing_api = '/api/backend/auth/me';
    protected $my_testing_method = 'delete';
    protected $my_testing_params = [
        'delete' => []
    ];
}
