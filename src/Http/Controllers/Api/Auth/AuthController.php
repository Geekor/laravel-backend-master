<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\Core\Support\GkApi as Api;
use Geekor\Core\Support\GkVerify;

class AuthController extends BaseController
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
        if (GkVerify::checkRequestFailed($request, [
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ])) {
            return Api::fail('缺少参数');
        }

        //...[2]
        $user = Master::where('username', $request->username)->first();

        if (! $user || ! GkVerify::checkHash($request->password, $user->password)) {
            return Api::fail('帐号或密码错误');
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
     * 用户登出
     */
    public function logout(Request $request)
    {
        if ($token = $this->user()->currentAccessToken()) {
            $token->delete();
        }
        return Api::success_deleted();
    }

    public function info(Request $request)
    {
        //TODO
        return $this->user();
    }

    /*
    |--------------------------------------------------------------------------
    | 令牌（TOKEN）管理
    |--------------------------------------------------------------------------
    |
    | $request->user() 不设置 guard 时，可以自动根据 token 判断是管理员还是普通用户
    |
    */

    /**
     * 生成新 TOKEN
     */
    public function createToken(Request $request)
    {
        if (GkVerify::checkRequestFailed($request, [
            'device_name' => 'required',
        ])) {
            return Api::fail('缺少参数');
        }

        $token = $request->user()->createToken($request->device_name);

        return Api::success_created([
            'token' => $token->plainTextToken
        ]);
    }

    public function tokens(Request $request)
    {
        $query = $request->user()->tokens();
        $query->select('name as device_name', 'last_used_at', 'created_at');
        $query->orderBy('created_at', 'desc');

        // dd($request->user()->tokens);
        return Api::success($query->get());
    }

    public function removeToken(Request $request, $id)
    {
        if ($token = $request->user()->tokens()->where('id', $id)->first()) {
            if ($token->delete()) {
                return Api::success_deleted();
            }
        } else {
            return Api::fail('指定的 ID 不存在，又或者那个 ID 不是你的');
        }

        return Api::fail('反正就是删除失败了');
    }

    public function removeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return Api::success_deleted();
    }
}
