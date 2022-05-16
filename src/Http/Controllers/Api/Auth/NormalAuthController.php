<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Geekor\BackendMaster\Models\User;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\Core\Support\GkApi as Api;
use Geekor\Core\Support\GkVerify;

class NormalAuthController extends BaseController
{
    /**
     * 用户注册
     */
    public function register(Request $request)
    {
        /** 注册流程
         * ------------------------
         * [1] 检查输入参数是否符合要求
         * [2] 检查用户是否已注册
         * [3] 创建用户
         * [4] 返回结果
         * ------------------------
         */

        //...[1]
        if (GkVerify::checkRequestFailed($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ])) {
            return Api::fail('缺少参数');
        }

        //...[2]
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return Api::fail('用户已存在');
        }

        //...[3]
        $attr = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => GkVerify::makeHash($request->password)
        ];
        DB::beginTransaction();
        if ($user = User::create($attr)) {
            $user->syncRoles(['user']);
        }
        DB::commit();

        //...[4]
        return Api::successCreated($user);
    }

    /**
     * 用户登录
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
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ])) {
            return Api::fail('缺少参数');
        }

        //...[2]
        $user = User::where('email', $request->email)->first();

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
}
