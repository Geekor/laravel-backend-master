<?php

namespace Geekor\BackendMaster;

/**
 * use Geekor\BackendMaster\Consts as BM;
 *
 * BM::tr('xxx');
 *
 */
class Consts
{
    public const LANG_NAMESPACE = 'geekor-bm';

    //========================================

    public static function tr($key = null, $replace = [], $locale = null)
    {
        return trans(self::LANG_NAMESPACE . '::' . $key, $replace, $locale);
    }
}
