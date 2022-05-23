<?php

namespace Geekor\BackendMaster\Http\Middleware;

use Closure;
use Geekor\BackendMaster\Consts as BM;
use Geekor\Core\Exceptions\InputException;
use Geekor\Core\Support\GkApi;
use Illuminate\Support\Arr;

class ApiHeadersCheck
{
    /**
     * 用于检查当前请求的头是否带了我们需要的信息
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 只监听 API 访问的情况
        if (! $request->is("api/*")) {
            return $next($request);
        }

        $reqires = [
            'app-mark' => '',
            'app-version' => '',
        ];
        $empties = [];

        foreach ($reqires as $k => $v) {
            $reqires[$k] = $request->header($k);

            if (empty($reqires[$k])) {
                $empties[] = $k;
            }
        }

        if (count($empties) > 0) {
            throw new InputException(
                GkApi::API_PARAM_MISS,
                BM::tr('api.headers_reqires', [
                    'headers' => Arr::join($empties, ', ')
                ])
            );
        }

        return $next($request);
    }

    // public function terminate($request, $response)
    // {
    // }
}
