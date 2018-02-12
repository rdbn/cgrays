<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.02.2018
 * Time: 15:26
 */

namespace AppBundle\Services\Helper;

class MbStrimWidthHelper
{
    public static function strimWidth($str)
    {
        return mb_strimwidth($str, 0, 15, '...', 'utf-8');
    }
}