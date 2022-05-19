<?php

namespace Geekor\BackendMaster;

/**
 * use Geekor\BackendMaster\AppConst as BM;
 *
 * BM::tr('xxx');
 *
 */
class AppConst
{
    public const LANG_NAMESPACE = 'geekor-bm';

    //========================================

    public static function tr($key = null, $replace = [], $locale = null)
    {
        return trans(self::LANG_NAMESPACE . '::' . $key, $replace, $locale);
    }
}
