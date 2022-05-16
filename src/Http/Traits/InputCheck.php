<?php

namespace Geekor\BackendMaster\Http\Traits;

use Illuminate\Http\Request;

use Geekor\Core\Exceptions\InputException;
use Geekor\Core\Support\GkVerify;

trait InputCheck
{
    protected function checkRequestInput(Request $request, $validator)
    {
        if (GkVerify::checkRequestFailed($request, $validator)) {
            throw new InputException();
        }
    }

    /**
     * 获取分页信息
     *
     * @return limit 分页大小，
     * @return offset 开始位置
     */
    protected function getPageParams(Request $request)
    {
        $limit =  (int) $request->input('limit', 10);
        $offset = (int) $request->input('start', -1);
        $page = (int) $request->input('page', 1);
        if ($offset < 0) {
            $offset = ($page - 1) * $limit;
        }

        return [
            'limit' => $limit, //page size
            'offset' => $offset,
        ];
    }

    /**
     * 获取可选参数
     * - 返回存在于 $keys 中的参数，没有则不出现在结果中
     * - 避免出现读取参数为 null 的问题
     * @return false 没有任何参数
     */
    protected function getOptionInputs(Request $request, $keys=[])
    {
        $list = [];

        foreach ($keys as $k) {
            if ($v = $request->input($k)) {
                $list[$k] = $v;
            }
        }

        return count($list)>0 ? $list : false;
    }

    /**
     * 获取所有指定参数
     * @return false 缺少一个以上的参数
     */
    protected function getAllInputs(Request $request, $keys=[])
    {
        $list = [];

        foreach ($keys as $k) {
            if ($v = $request->input($k)) {
                $list[$k] = $v;
            }
        }

        return count($list) == count($keys) ? $list : false;
    }
}
