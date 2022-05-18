<?php

namespace Geekor\BackendMaster\Tests\Feature\Traits;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait AuthTokenCheck
{
    /**
     * headers 不带 TOKEN
     */
    public function test_call_api_without_token()
    {
        $resp = null;
        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->getJson($this->myTestingApi());
                break;

            case 'delete':
                $resp = $this->deleteJson($this->myTestingApi());
                break;

            // 不需要定义 default, 测试框架会自动提示的
        }

        $resp && $resp->assertUnauthorized();
    }

    /**
     * 使用错误的 TOKEN
     */
    public function test_call_api_by_bad_token()
    {
        $resp = null;
        $token = Str::random(10);

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi());
                break;

            case 'delete':
                $resp = $this->withToken($token)->deleteJson($this->myTestingApi());
                break;
        }

        $resp && $resp->assertUnauthorized();
    }

    /**
     * 使用普通用户身份的 TOKEN, 但不带角色/权限
     */
    public function test_call_api_by_normal_user_token_without_permissions()
    {
        $this->handleCallApiByNormalUserToken(false);
    }

    /**
     * 使用普通用户身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_normal_user_token_with_right_permissions()
    {
        $this->handleCallApiByNormalUserToken(false);
    }

    /**
     * 使用管理员身份的 TOKEN, 但不带角色/权限
     */
    public function test_call_api_by_master_user_token_without_permissions()
    {
        $this->handleCallApiByMasterUserToken(false);
    }

    /**
     * 使用管理员身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_master_user_token_with_right_permissions()
    {
        $this->handleCallApiByMasterUserToken(true);
    }


    //////////////////////////////////////////////////////////////////////


    private function handleCallApiByNormalUserToken($usePermission)
    {
        $resp = null;
        $arr = $this->makeNormalUserAndToken($usePermission); //[user, token]
        $token = $arr['token'];
        $ok_status_code = 200;

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi());
                break;

            case 'delete':
                $resp = $this->withToken($token)->deleteJson($this->myTestingApi());
                $ok_status_code = 204;
                break;
        }

        if ($resp) {
            if ($this->isMasterGuard()) {
                $resp->assertUnauthorized();
            } else {
                if ($this->isPermissionRequired() && ! $usePermission) {
                    $ok_status_code = 403; // 没有权限
                }
                $resp->assertStatus($ok_status_code);
            }
        }
    }

    private function handleCallApiByMasterUserToken($usePermission)
    {
        $resp = null;
        $arr = $this->makeMasterUserAndToken($usePermission); //[user, token]
        $token = $arr['token'];
        $ok_status_code = 200;

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi());
                break;

            case 'delete':
                $resp = $this->withToken($token)->deleteJson($this->myTestingApi());
                $ok_status_code = 204;
                break;
        }

        if ($resp) {
            if ($this->isMasterGuard()) {
                if ($this->isPermissionRequired() && ! $usePermission) {
                    $ok_status_code = 403; // 没有权限
                }
                $resp->assertStatus($ok_status_code);
            } else {
                $resp->assertUnauthorized();
            }
        }
    }
}
