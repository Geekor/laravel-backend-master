<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;

use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\Core\Support\GkApi as Api;
use Geekor\Core\Support\GkVerify;

class TokenController extends BaseController
{

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
        //...检查输入参数
        $this->checkRequestInput($request, [
            'device_name' => 'required',
        ]);

        $token = $request->user()->createToken($request->device_name);

        return Api::successCreated([
            'token' => $token->plainTextToken
        ]);
    }

    public function tokens(Request $request)
    {
        $query = $request->user()->tokens();
        $query->select('name as device_name', 'last_used_at', 'created_at');
        $query->orderBy('created_at', 'desc');

        // dd($request->user()->tokens);
        $arr = $query->get();
        return Api::success($arr ?? []);
    }

    public function removeToken(Request $request, $id)
    {
        if ($token = $request->user()->tokens()->where('id', $id)->first()) {
            if ($token->delete()) {
                return Api::successDeleted();
            }
        } else {
            return Api::fail('指定的 ID 不存在，又或者那个 ID 不是你的');
        }

        return Api::fail('反正就是删除失败了');
    }

    public function removeAllTokens(Request $request)
    {
        $request->user()->tokens()->delete();

        return Api::successDeleted();
    }
}
