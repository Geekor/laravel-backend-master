<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\BackendMaster\Models\Master;
use Geekor\BackendMaster\Models\Role;
use Geekor\Core\Support\GkApi as Api;

class MasterController extends BaseController
{
    public function index(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-b');

        //....
        $data = [];
        $conds = [];
        $orders = [];

        $params = $this->getPageParams($request);
        $username = $request->input('username', null);
        $sort = $request->input('sort', null);

        if ($sort) {
            switch ($sort) {
                case '-id':
                    $orders = ['id', 'desc'];
                    break;

                case 'id':
                case '+id':
                default:
                    $orders = ['id', 'asc'];
                    break;
            }
        }

        //....开始查找数据
        $query = Master::withRoles();
        if (empty($username)) {
            $query->where($conds);
        } else {
            $conds_1 = Arr::prepend($conds, ['id', 'like', '%' . $username . '%']);
            $conds_2 = Arr::prepend($conds, ['username', 'like', '%' . $username . '%']);
            $conds_3 = Arr::prepend($conds, ['name', 'like', '%' . $username . '%']);

            $query->where($conds_1)->orWhere($conds_2)->orWhere($conds_3);
        }

        $total = $query->count();
        $query->skip($params['offset'])->take($params['limit']);

        if (!empty($orders)) {
            $query = $query->orderBy($orders[0], $orders[1]);
        }

        $items = $query->get();

        $data = [
            'total' => $total,
            'items' => $items->toArray()
        ];

        return Api::success($data);
    }

    public function store(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-a');

        //...检查输入参数
        $this->checkRequestInput($request, [
            'username' => 'required',
            'password' => 'required',
            'role' => 'required',
        ]);

        //....
        $params = $request->only([
            'username', 'password', 'role'
        ]);

        $params['name'] = $request->input('name', $params['username']);

        if (Master::where('username', $params['username'])->exists()) {
            //'用户已存在'
            return Api::fail(__('string.adm_exception_member_exists'));
        }

        $role = Role::where('name', $params['role'])->first();
        if (!$role || $role->guard_name != 'master') {
            //'非法角色'
            return Api::fail(__('string.adm_exception_illegal_role'));
        }
        //---------------
        $attr = Arr::except($params, 'role');

        if ($admin = Master::create($attr)) {
            $admin->assignRole($role->name);

            return Api::success();
        } else {
            return Api::fail('写入数据库失败！');
        }

    }

    /**
     * 查看用户权限列表
     */
    public function userHasPermissions(Request $request, $id)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-b');

        //...........
        $data = [];

        if ($member = Master::find($id)) {
            $data = $member->getAllPermissions();
        }

        return Api::success($data);
    }
}
