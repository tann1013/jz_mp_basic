<?php
/**
 * Created by PhpStorm.
 * User: chniccs
 * Date: 2019-07-20
 * Time: 11:01
 */

namespace app\helper;


class CommonHelper
{
    public static function getFileExName($fileName)
    {
        return substr($fileName, strrpos($fileName, '.'));
    }

    public static function checkStrIsInvalid($str){
        return !isset($str)||$str==null||$str=='';
    }

}