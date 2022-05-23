<?php
namespace Geekor\BackendMaster\Support;

use Illuminate\Support\Facades\Cache;

use Geekor\BackendMaster\Models\Configure;

/** -------------------------------------------------------
 *   缓存读写工具函数
 *  ------------------------------------------------------- */

class BmCache
{
    /**
    * 读取 缓存数据库中的配置
    */
    public static function getConfigCache($name, $default=null)
    {
        $key = 'bm_db_config_'.$name;
        return Cache::get($key, $default);
    }

    /**
     * 读取 存储在数据库中的配置
     * -----------------------
     * 优先从缓存读取
     */
    public static function getConfig($name, $default=null)
    {
        $key = 'bm_db_config_'.$name;
        if (Cache::has($key)) {
            return Cache::get($key);

        } else {
            $data = Configure::where('name', $name)->value('content');
            if (!$data) {
                Cache::forever($key, $default);
                $data = $default;
            }

            return $data;
        }
    }

    /**
     * 保存 配置到数据库中
     */
    public static function setConfig($name, $value, $minutes=0)
    {
        Configure::updateOrInsert(['name' => $name],[
            'name' => $name,
            'content' => $value
        ]);

        if ($minutes>0) {
            Cache::put('bm_db_config_'.$name, $value, $minutes);
        } else {
            Cache::forever('bm_db_config_'.$name, $value);
        }
    }

    /**
     * 清除 所有“配置”缓存
     */
    public static function clearAllConfigureCache()
    {
        foreach (Configure::pluck('name') as $name) {
            Cache::forget('bm_db_config_'.$name);
        }
    }

}
