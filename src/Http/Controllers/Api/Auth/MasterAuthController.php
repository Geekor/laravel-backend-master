<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\Core\Support\GkApi as Api;
use Geekor\Core\Support\GkVerify;

class MasterAuthController extends BaseController
{

    /***
     * 后台登录
     */
    public function login(Request $request)
    {
        /** 登录流程
         * ------------------------
         * [1] 检查输入参数是否符合要求
         * [2] 检查用户是否已注册，密码是否正确
         * [3] 创建 TOKEN
         * [4] 返回结果
         * ------------------------
         */

        //...[1]
        $this->checkRequestInput($request, [
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        //...[2]
        $user = Master::where('username', $request->username)->first();

        if (! $user || ! GkVerify::checkHash($request->password, $user->password)) {
            return Api::failx(Api::API_PARAM_ERROR, '帐号或密码错误');
        }

        //...[3]
        $token = $user->createToken($request->device_name);

        //...[4]
        return Api::success([
            'info' => $user,
            'token' => $token->plainTextToken,
        ]);
    }

    /**
     * 用户退出
     */
    public function logout(Request $request)
    {
        if ($token = $this->user()->currentAccessToken()) {
            $token->delete();
        }
        return Api::successDeleted();
    }

    /**
     * 用户信息
     */
    public function info(Request $request)
    {
        return $this->user();
    }
}
