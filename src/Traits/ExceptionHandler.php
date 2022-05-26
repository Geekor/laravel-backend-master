<?php

namespace Geekor\BackendMaster\Traits;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

use Geekor\Core\Support\GkApi as Api;
use Geekor\Core\Exceptions\PermissionException;
use Geekor\Core\Exceptions\InputException;

use BadMethodCallException;
use Geekor\BackendMaster\Consts as BM;
use Throwable;

trait ExceptionHandler
{
    public function render($request, Throwable $e)
    {
        // 只监听 API 错误并返回 JSON 错误信息
        if (! $request->is("api/*")) {
            return parent::render($request, $e);
        }

        $msg = $e->getMessage();

        if ($e instanceof NotFoundHttpException) {
            return Api::failxNotFound(BM::tr('api.req_err_api_not_defined') .' >> /' . $request->path());
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return Api::failxBadMethod('请求该接口的【方法】有误 >> '.$request->method());
        }

        if ($e instanceof BadMethodCallException) {
            return Api::failxServerError(vsprintf('%s (%s)', [
                '路由【绑定】的方法不存在！', $msg
            ]));
        }

        // 身份认证失败
        if ($e instanceof AuthenticationException) {
            return Api::failxUnauthenticated();

        } else if ($e instanceof PermissionException) {
            return Api::failxForbidden($msg ?? BM::tr('api.auth_err_account_has_no_access_permission'));

        } else if ($e instanceof PermissionDoesNotExist) {
            //类似这样的错： There is no permission named `master:role-a` for guard `user`
            return Api::failxForbidden(BM::tr('api.auth_err_account_has_no_access_permission'));
        }

        // 缺少设置 Accept 头
        if ($e instanceof RouteNotFoundException) {
            $contentTypes = $request->getAcceptableContentTypes();

            if (! in_array('application/json', $contentTypes)) {
                return Api::failx(Api::API_REQUEST_ERROR,
                    '请求 API 时，需要在 header 设置 Accept: application/json');
            }

            return Api::fail($msg);
        }

        // 参数有误
        if ($e instanceof InputException) {
            return Api::failx($e->code, $msg);
        }

        // --------------------------------- 未知错误 ---
        if (config('app.env') === 'production') {
            $fn = explode('/', $e->getFile());
            if (count($fn) > 4) {
                $fn = array_splice($fn, count($fn)-4);
            }
            $fn = '.../'. implode('/', $fn);

            return Api::failx(Api::JUST_FAILED, [
                'message' => $msg,
                'file' => $fn,
                'line' => $e->getLine() ?? '0'

            ], 500);
        } else {
            dd($e);
        }
    }
}
