<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Member;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\BackendMaster\Models\Ban;
use Geekor\BackendMaster\Models\Role;
use Geekor\BackendMaster\Models\User;
use Geekor\Core\ApiEventConstant as Api;

class UserController extends BaseController
{
    public function filters(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-b');
       
        $data = [
            'bans' => Ban::all(),
            'roles' => Role::getSimpleUsers()
        ];

        return Api::success($data);
    }

    /**
     * 获取用户列表
     */
    public function index(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-b');

        //....
        $data = [];
        $conds = [];
        $orders = [];
        $total = 0;
        $items = [];

        $params = $this->getPageParams($request);
        $username = $request->input('username', null);
        $roles = $request->input('roles', null);
        $banned = $request->input('banned', null);
        $sort = $request->input('sort', null);

        if ($sort) {
            switch ($sort) {
                case '-id':
                    $orders = ['id', 'desc'];
                    break;

                case 'lt':
                    $orders = ['login_count', 'desc'];
                    break;

                case 'id':
                case '+id':
                default:
                    $orders = ['id', 'asc'];
                    break;
            }
        }

        //....开始查找数据
        $query = User::withRoles();
        if (!empty($username)) {
            $conds_1 = Arr::prepend($conds, ['id', 'like', '%' . $username . '%']);
            $conds_2 = Arr::prepend($conds, ['name', 'like', '%' . $username . '%']);
            $conds_3 = Arr::prepend($conds, ['email', 'like', '%' . $username . '%']);

            $query->where($conds_1)->orWhere($conds_2)->orWhere($conds_3);
        }

        if (isset($roles)) {
            $query->role($roles);
        }

        if (isset($banned)) {
            $query->whereIn('ban_id', $banned);
        }

        $total = $query->count();
        $query->skip($params['offset'])->take($params['limit']);

        if (empty($orders)) {
            $items = $query->get();
        } else {
            $items = $query->orderBy($orders[0], $orders[1])->get();
        }

        // if (!empty($items)) {
        //     $items = $items->toArray();
        //     //$items = $items->makeVisible(['platform_id'])->toArray();
        // }

        $data = [
            'total' => $total,
            'items' => $items
        ];

        return Api::success($data);
    }

    public function store(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-a');

        //...
        $user_mode = $request->input('user_mode', false);

        if (!$user_mode || $user_mode != 'admin') {
            return Api::fail('Illegal [ user_mode ]');
        }

        $params = $request->only(['username', 'password', 'role']);
        if (count($params) < 3) {
            //'缺少输入字段'
            return Api::fail(__('string.adm_exception_params_miss'));
        }

        $email = App::make_email_of_backend($params['username']);
        if (User::where('email', $email)->exists()) {
            //'用户已存在'
            return Api::fail(__('string.adm_exception_member_exists'));
        }

        $role = Role::where('name', $params['role'])->first();
        if (!$role || $role->level < 100) { //后台用户的level至少为100
            //'非法角色'
            return Api::fail(__('string.adm_exception_illegal_role'));
        }
        //---------------
        //TODO
        // $params['name'] = $params['username'];
        // $user = MemberAssistant::createBackendUser(
        //     $params['username'],
        //     $params['password'],
        //     $params['name']
        // );

        return Api::success();
    }

    public function storeGroupUser(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasPermission('master:user-a');

        ///......正式开始
        $params = $request->only(['username', 'password', 'group_id', 'role']);
        if (count($params) < 4) {
            //'缺少输入字段'
            return Api::fail(__('string.adm_exception_params_miss'));
        }

        //TODO
        // $email = App::make_email_of_backend($params['username']);
        // if (User::withTrashed()->where('email', $email)->exists()) {
        //     //'用户已存在'
        //     return Api::fail(__('string.adm_exception_member_exists'));
        // }

        //....
        //TODO
        // $params['name'] = $params['username'];
        // $user = MemberAssistant::createBackendUser(
        //     $params['username'],
        //     $params['password'],
        //     $params['name'],
        //     $params['role']
        // );

        // //... add group info
        // if (isset($params['group_id'])) {
        //     \DB::table('group_has_members')->insert([
        //         'group_id' => $params['group_id'],
        //         'member_id' => $user->member_id,
        //     ]);
        // }

        return Api::success();
    }

    public function banUser(Request $request, $id)
    {
        //...禁止「非超级管理员」做权限修改的动作
        $user = $this->getUserAndCheckHasRole(['super_master', 'user_master']);

        $input = $this->checkInputRequiredParams($request, ['is_banned']);

        if (!$user = User::find($id)) {
            //'用户不存在'
            return Api::fail(__('string.adm_exception_member_not_exists'));
        }

        $ban = 0;
        if ($input['is_banned']) {
            $ban = 1;
        } else {
            if ($user->hasRole('tester')) {
                $ban = 2;
            }
        }
        $user->update([
            'ban_id' => $ban
        ]);

        return Api::success();
    }

    /**
     * 修改用户角色
     */
    public function updateRoles(Request $request, $id)
    {
        //...禁止「非超级管理员」做权限修改的动作
        $user = $this->getUserAndCheckHasRole(['super_master', 'user_master']);

        //...禁止对「原生·超级管理员」做角色改动
        // if (1 == $id) {
        //     return Api::json(Api::SET_DATA_FAILED, [
        //         'detail' => __('string.adm_exception_forbidden_set_super_master_info') //'你不能对「超级管理员」动手动脚！'
        //     ]);
        // }

        if (!$user = User::find($id)) {
            //'用户不存在'
            return Api::fail(__('string.adm_exception_member_not_exists'));
        }

        $data = [];
        //TODO 加入操作日志

        //...更新角色
        $roles = $request->input('roles', []);

        //...最终操作：修改角色
        $banned = $user->ban_id;
        //TODO
        // if (in_array('tester', $roles)) {
        //     $banned = App::MEMBER_BANNED_TESTER;
        // } else if ($banned == App::MEMBER_BANNED_TESTER){
        //     $banned = App::MEMBER_BANNED_NORMAL;
        // }

        if ($user->ban_id != $banned) {
            $user->update([ 'ban_id' => $banned ]);
        }

        $user->syncRoles([$roles]); //替换角色，assignRole() 是添加

        return Api::success();
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

        if ($member = User::find($id)) {
            $data = $member->getAllPermissions();
        }

        return Api::success($data);
    }

    //////////////////////////////////////////////////////////////// !!! 超级危险接口 !!! 请做好权限控制 /////////

    public function removeData(Request $request, $id)
    {
        $data = [];

        //...检查操作者的权限！
        //...只有「超级管理员」有权限修改
        $user = $this->getUserAndCheckHasRole('super_master');

        // ///................
        // if ($member = Member::with('role')->find($id)) {
        //     if ('tester' != $role->name) {
        //         return Api::json(Api::SET_DATA_FAILED, [
        //             'detail' => __('string.amd_only_tester_data_can_be_deleted') //'只能删除「测试员」账户下的数据！'
        //         ]);
        //     }
        // }

        // if ($txt = $request->input('options', null)) {
        //     $options = explode(',', $txt);

        //     //...删除宠物及其报告
        //     if (in_array('pets', $options)) {
        //         \App\Models\Pet::withTrashed()->where('owner_id', $id)->forceDelete();

        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_PET]
        //         ])->forceDelete();

        //         $data['detail'][] = 'pets';
        //         $data['detail'][] = 'pets.reports';
        //     }

        //     //...删除 PET 报告
        //     if (in_array('pet_reports', $options)) {
        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_PET]
        //         ])->forceDelete();

        //         $data['detail'][] = 'pet_reports';
        //     }
        //     //...删除 PET 通用数据
        //     if (in_array('pet_comm_data', $options)) {
        //         \App\Models\CommData::where([
        //             ['member_id', $id],
        //             ['title', 'like', 'PET_%']
        //         ])->forceDelete();

        //         $data['detail'][] = 'pet_comm_data';
        //     }
        //     //...删除 PET 通用标记
        //     if (in_array('pet_comm_mark', $options)) {
        //         \App\Models\CommMark::where([
        //             ['member_id', $id],
        //         ])->forceDelete();

        //         $data['detail'][] = 'pet_comm_mark';
        //     }

        //     //...删除 lh 报告
        //     if (in_array('lh_reports', $options)) {
        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_LH]
        //         ])->forceDelete();

        //         $data['detail'][] = 'lh_reports';
        //     }
        //     //...删除 LH 通用数据
        //     if (in_array('lh_comm_data', $options)) {
        //         \App\Models\CommData::where([
        //             ['member_id', $id],
        //             ['title', 'like', 'LH_%']
        //         ])->forceDelete();

        //         $data['detail'][] = 'lh_comm_data';
        //     }
        //     //...删除 LH 通用标记
        //     if (in_array('lh_comm_mark', $options)) {
        //         \App\Models\CommMark::where([
        //             ['member_id', $id],
        //         ])->forceDelete();

        //         $data['detail'][] = 'lh_comm_mark';
        //     }

        //     //...删除 HPY 报告
        //     if (in_array('hpy_reports', $options)) {
        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_HPY]
        //         ])->forceDelete();

        //         $data['detail'][] = 'hpy_reports';
        //     }
        //     //...删除 HPY 通用数据
        //     if (in_array('hpy_comm_data', $options)) {
        //         \App\Models\CommData::where([
        //             ['member_id', $id],
        //             ['title', 'like', 'HPY_%']
        //         ])->forceDelete();

        //         $data['detail'][] = 'hpy_comm_data';
        //     }
        //     //...删除 HPY 通用标记
        //     if (in_array('hpy_comm_mark', $options)) {
        //         \App\Models\CommMark::where([
        //             ['member_id', $id],
        //         ])->forceDelete();

        //         $data['detail'][] = 'hpy_comm_mark';
        //     }

        //     //...删除 HIV 报告
        //     if (in_array('hiv_reports', $options)) {
        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_HIV]
        //         ])->forceDelete();

        //         $data['detail'][] = 'hiv_reports';
        //     }
        //     //...删除 HIV 通用数据
        //     if (in_array('hiv_comm_data', $options)) {
        //         \App\Models\CommData::where([
        //             ['member_id', $id],
        //             ['title', 'like', 'HIV_%']
        //         ])->forceDelete();

        //         $data['detail'][] = 'hiv_comm_data';
        //     }
        //     //...删除 HIV 通用标记
        //     if (in_array('hiv_comm_mark', $options)) {
        //         \App\Models\CommMark::where([
        //             ['member_id', $id],
        //         ])->forceDelete();

        //         $data['detail'][] = 'hiv_comm_mark';
        //     }

        //     //...删除 CVD 报告
        //     if (in_array('cvd_reports', $options)) {
        //         \App\Models\CommReport::withTrashed()->where([
        //             ['member_id', $id],
        //             ['report_type', App::REPORT_TYPE_CVD]
        //         ])->forceDelete();

        //         $data['detail'][] = 'cvd_reports';
        //     }
        //     //...删除 CVD 通用数据
        //     if (in_array('cvd_comm_data', $options)) {
        //         \App\Models\CommData::where([
        //             ['member_id', $id],
        //             ['title', 'like', 'CVD_%']
        //         ])->forceDelete();

        //         $data['detail'][] = 'cvd_comm_data';
        //     }
        //     //...删除 CVD 通用标记
        //     if (in_array('cvd_comm_mark', $options)) {
        //         \App\Models\CommMark::where([
        //             ['member_id', $id],
        //         ])->forceDelete();

        //         $data['detail'][] = 'cvd_comm_mark';
        //     }

        //     return Api::json(Api::SET_DATA_SUCCESS, $data);
        // }

        return Api::fail();
    }

    /**
     * 原则上我们只删除 user 表中的账号，即解除了 user 与 member 的关联。
     * member 表中的账号进行软删除，但其所有数据依然保留！
     *
     * 用户重新注册时会另外新建 member 账户。
     */
    public function destroy(Request $request, $id)
    {
        $data = [];
        //...只有「超级管理员」有权限修改
        $user = $this->getUserAndCheckHasRole('super_master');

        //TODO
        ///...................
        // if ($member = Member::with('role')->find($id)) {
        //     if ('tester' != $role->name) {
        //         return Api::json(Api::SET_DATA_FAILED, [
        //             'detail' => __('string.amd_only_tester_data_can_be_deleted') //'只能删除「测试员」账户！'
        //         ]);
        //     }

        //     $update([
        //         'banned' => App::MEMBER_BANNED_DELETED
        //     ]);
        //     $delete();
        // }

        // if ($user = User::withTrashed()->where('member_id', $id)->first()) {
        //     if (App::PLATFORM_PHONE == $user->platform_id) {
        //         //删除手机注册表中的数据
        //         $email = $user->email;
        //         if (substr($email, 0, 4) == 'sms_') {
        //             $uid = substr($email, 0, strpos($email, "@"));
        //             list($x, $region, $number) = explode('_', $uid);
        //             foreach (PhoneUser::where([['region', 'like', $region], ['number', 'like', $number]])->get() as $u) {
        //                 $u->forceDelete();
        //             }
        //         }

        //     } else if (App::is_3rd_platform_id($user->platform_id)) {
        //         //...如果是第三方账户，取消绑定
        //         // \App\Models\Member\UserUnion::where('user_id')
        //     }

        //     $user->forceDelete();
        // }

        return Api::success($data);
    }
}
