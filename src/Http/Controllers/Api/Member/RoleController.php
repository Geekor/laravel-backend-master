<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Member;

use Illuminate\Http\Request;

use Geekor\BackendMaster\Models\Role;
use Geekor\BackendMaster\Models\Permission;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\Core\Support\GkApi as Api;

class RoleController extends BaseController
{
    public function index(Request $request)
    {
        //...权限拦截（目前只允许超级管理员访问）
        $user = $this->getUserAndCheckHasRole('super_master');

        //.......
        $data = [
            'total' => 0,
            'items' => []
        ];
        $conds = [];

        $params = $this->getPageParams($request);

        //....开始查找数据
        $total = Role::where($conds)->count();
        $query = Role::where($conds)
                ->skip($params['offset'])
                ->take($params['limit'])
                ->orderBy('level','desc');

        $items = $query->get();

        $data = [
            'total' => $total,
            'items' => $items->makeVisible([])->toArray()
        ];

        return Api::success($data);
    }

    public function show(Request $request, $id)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasRole('super_master');

        //.......
        if ($role = Role::find($id)) {
            $role->getAllPermissions();

            $perms = Permission::where('guard_name', $role->guard_name)->orderBy('name', 'asc')->get();
            if ($perms) {
                $perms = $perms->toArray();
            } else {
                $perms = [];
            }

            return Api::success([
                'role' => $role->toArray(),
                'permissions' => $perms
            ]);
        }

        return Api::failx404('role not found');
    }

    public function store(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasRole('super_master');

        //...输入检测
        $this->checkRequestInput($request, [
            'name' => 'required',
            'level' => 'required',
            'title' => 'required',
        ]);

        //.......
        $params = $request->only(['name', 'title', 'level', 'description']);
        $perms = $request->input('permissions', null);
        if ($role = Role::create($params)) {
            if ($perms) {
                $role->syncPermissions($perms);
            }

            return Api::success_created();
        }

        return Api::failx500('database create role failed');
    }

    public function update(Request $request, $id)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasRole('super_master');

        //...输入检测
        $this->checkRequestInput($request, [
            'name' => 'required',
            'level' => 'required',
            'title' => 'required',
        ]);

        //.......
        $params = $request->only(['name', 'title', 'level', 'description']);
        $perms = $request->input('permissions', []);
        if ($role = Role::find($id)) {
            $role->update($params);
            $role->syncPermissions($perms);

            return Api::success();
        }

        return Api::failx404('role not found');
    }

    public function roleOptions(Request $request)
    {
        $user_mode = $request->input('user_mode', false);

        if (!$user_mode || !in_array($user_mode, ['normal', 'master'])) {
            return Api::fail('Illegal [ user_mode ]');
        }

        //-----------------------------
        $data = [];
        $roles = null;
        $query = Role::select('id', 'name', 'title', 'description');

        if ($user_mode == 'normal') {
            $roles = $query->where('guard_name', 'user')->orderBy('level','desc')->get();
        } else if ($user_mode == 'master') {
            $roles = $query->where('guard_name', 'master')->orderBy('level','desc')->get();
        }

        if ($roles) {
            $data = $roles->toArray();
        }

        return Api::success($data);
    }
}
