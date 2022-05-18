<?php

namespace Geekor\BackendMaster\Tests\Feature\Traits;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

use Geekor\Core\Support\GkTestUtil;

/**
 * 本模块只用于需要 token 验证的模块
 */
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
                $resp = $this->getJson($this->myTestingApi(), $this->myTestingParams());
                break;

            case 'post':
                $resp = $this->postJson($this->myTestingApi(), $this->myTestingParams());
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
                $resp = $this->withToken($token)->getJson($this->myTestingApi(), $this->myTestingParams());
                break;

            case 'post':
                $resp = $this->withToken($token)->postJson($this->myTestingApi(), $this->myTestingParams());
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
        $this->callApiByNormalUserToken(false);
    }

    /**
     * 使用普通用户身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_normal_user_token_with_right_permissions()
    {
        $this->callApiByNormalUserToken(false);
    }

    /**
     * 使用管理员身份的 TOKEN, 但不带角色/权限
     */
    public function test_call_api_by_master_user_token_without_permissions()
    {
        $this->callApiByMasterUserToken(false);
    }

    /**
     * 使用管理员身份的 TOKEN, 带正确角色/权限
     */
    public function test_call_api_by_master_user_token_with_right_permissions()
    {
        $this->callApiByMasterUserToken(true);
    }


    //////////////////////////////////////////////////////////////////////

    /**
     * 这个接口用于普通账户正常访问的情况
     * ------------------------------
     * 默认带正确的 token 和 角色/权限
     */
    protected function callApiByNormalUser($params, $customAssertFn)
    {
        $this->callApiByNormalUserToken($this->isPermissionRequired(), $params, $customAssertFn);
    }

    /**
     * 这个接口用于普通账户
     * -----------------------------------------
     * 默认带正确 token。是否使用正确权限由调用者决定
     *
     * @param usePermission 是否启用正确的权限配置
     * @param params 请求接口时带的参数（不填则使用默认配置）
     * @param customAssertFn 自定义的断言函数（不设置时则按一般流程进行断言）
     */
    private function callApiByNormalUserToken($usePermission, $params=null, $customAssertFn=null)
    {
        $resp = null;
        $arr = $this->makeNormalUserAndToken($usePermission); //[user, token]
        $token = $arr['token'];
        $ok_status_code = 200;
        if (is_null($params)) {
            $params = $this->myTestingParams();
        }

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi(), $params);
                break;

            case 'post':
                $resp = $this->withToken($token)->postJson($this->myTestingApi(), $params);
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

                if ($customAssertFn) {
                    $customAssertFn($resp);
                } else {
                    $resp->assertStatus($ok_status_code);
                }
            }
        }
    }

    //=================================================================

    /**
     * 这个接口用于管理员账户正常访问的情况
     * ------------------------------
     * 默认带正确的 token 和 角色/权限
     */
    protected function callApiByMasterUser($params, $customAssertFn)
    {
        $this->callApiByMasterUserToken($this->isPermissionRequired(), $params, $customAssertFn);
    }

    /**
     * 这个接口用于管理员账户
     * ------------------------------------------
     * 默认带正确 token。是否使用正确权限由调用者决定
     *
     * @param usePermission 是否启用正确的权限配置
     * @param params 请求接口时带的参数（不填则使用默认配置）
     * @param customAssertFn 自定义的断言函数（不设置时则按一般流程进行断言）
     */
    protected function callApiByMasterUserToken($usePermission, $params=null, $customAssertFn=null)
    {
        $resp = null;
        $arr = $this->makeMasterUserAndToken($usePermission); //[user, token]
        $token = $arr['token'];
        $ok_status_code = 200;
        if (is_null($params)) {
            $params = $this->myTestingParams();
        }

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi(), $params);
                break;

            case 'post':
                $resp = $this->withToken($token)->postJson($this->myTestingApi(), $params);
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

                if ($customAssertFn) {
                    $customAssertFn($resp);
                } else {
                    $resp->assertStatus($ok_status_code);
                }
            } else {
                $resp->assertUnauthorized();
            }
        }
    }
}
