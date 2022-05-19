<?php

namespace Geekor\BackendMaster\Tests\Base\Traits;

use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

use Geekor\Core\Support\GkTestUtil;

/**
 * 本模块只用于需要 token 验证的模块
 */
trait AuthUserHelper
{
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
        $ok_status_codes = [ 200 ];
        if (is_null($params)) {
            $params = $this->myTestingParams();
        }

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi(), $params);
                break;

            case 'post':
                $resp = $this->withToken($token)->postJson($this->myTestingApi(), $params);
                $ok_status_codes[] = 201;
                break;

            case 'put':
                $resp = $this->withToken($token)->putJson($this->myTestingApi(), $params);
                break;

            case 'delete':
                $resp = $this->withToken($token)->deleteJson($this->myTestingApi());
                $ok_status_codes[] = 204;
                break;
        }

        if ($resp) {
            if ($this->isMasterGuard()) {
                $resp->assertUnauthorized();
            } else {
                if ($this->isPermissionRequired() && ! $usePermission) {
                    $ok_status_codes = [ 403 ]; // 没有权限，禁止访问
                }

                if ($customAssertFn) {
                    $customAssertFn($resp);

                } else {
                    // if (! GkTestUtil::isAcceptableStatus($resp, $ok_status_codes)) {
                    //     var_dump($resp->getStatusCode(), $ok_status_codes);
                    // }
                    //注意：是用 $this
                    $this->assertTrue(GkTestUtil::isAcceptableStatus($resp, $ok_status_codes));
                }
            }
        }

        // 删除临时生成的帐号
        $arr['user'] && $arr['user']->delete();
    }

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
        // defined in Base/TestAuthCase.php
        $arr = $this->makeMasterUserAndToken($usePermission); //[user, token]
        $token = $arr['token'];
        $ok_status_codes = [ 200 ];
        if (is_null($params)) {
            $params = $this->myTestingParams();
        }

        switch ($this->myTestingMethod()) {
            case 'get':
                $resp = $this->withToken($token)->getJson($this->myTestingApi(), $params);
                break;

            case 'post':
                $resp = $this->withToken($token)->postJson($this->myTestingApi(), $params);
                $ok_status_codes[] = 201;
                break;

            case 'put':
                $resp = $this->withToken($token)->putJson($this->myTestingApi(), $params);
                break;

            case 'delete':
                $resp = $this->withToken($token)->deleteJson($this->myTestingApi());
                $ok_status_codes[] = 204;
                break;
        }

        if ($resp) {
            if ($this->isMasterGuard()) {
                if ($this->isPermissionRequired() && ! $usePermission) {
                    $ok_status_codes = [ 403 ]; // 没有权限，禁止访问只能允许这一种存在
                }

                if ($customAssertFn) {
                    $customAssertFn($resp);

                } else {
                    // if (! GkTestUtil::isAcceptableStatus($resp, $ok_status_codes)) {
                    //     var_dump($resp);
                    // }
                    //注意：是 $this
                    $this->assertTrue(GkTestUtil::isAcceptableStatus($resp, $ok_status_codes));
                }
            } else {
                $resp->assertUnauthorized();
            }
        }

        // 删除临时生成的帐号
        $arr['user'] && $arr['user']->delete();
    }
}
