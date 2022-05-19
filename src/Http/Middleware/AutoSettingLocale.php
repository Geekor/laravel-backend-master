<?php

namespace Geekor\BackendMaster\Http\Middleware;

use Closure;

class AutoSettingLocale
{
    /**
     * 用于 设置用户偏好的语言
     * -------------------
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //...获取用户语言设置
        $lang = strtolower( $request->header('user-locale', 'en') );
        $lang = str_replace(['-', '_'], '', $lang);

        if ('en' != $lang) {
            
            $locale = 'en';
            switch ($lang) {
                case 'zhrcn':
                case 'zhcn': 
                case 'zh':
                case 'cn':
                    $locale = 'zh-CN';
                    break;

                case 'zhrtw':
                case 'zhtw':
                case 'tw':
                    $locale = 'zh-TW';
                    break;
            }

            app()->setLocale($locale);
        }

        return $next($request);
    }

}
