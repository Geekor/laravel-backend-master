<?php

namespace Geekor\BackendMaster\Http\Controllers\Api\Member;

use Illuminate\Http\Request;

use Geekor\BackendMaster\Models\Permission;
use Geekor\BackendMaster\Http\Controllers\Api\BaseController;
use Geekor\BackendMaster\Http\Traits\PermissionCheck;
use Geekor\Core\Support\GkApi as Api;

class PermissionController extends BaseController
{
    use PermissionCheck;

    public function index(Request $request)
    {
        //...权限拦截
        $user = $this->getUserAndCheckHasRole('super_master');

        //.......
        $data = [
            'total' => 0,
            'items' => []
        ];
        $conds = [];

        $params = $this->getPageParams($request);

        //....开始查找数据
        $total = Permission::where($conds)->count();
        $query = Permission::where($conds)
                ->skip($params['offset'])
                ->take($params['limit']);

        $items = $query->get();

        $data = [
            'total' => $total,
            'items' => $items->makeVisible([])->toArray()
        ];

        return Api::success($data);
    }
}
