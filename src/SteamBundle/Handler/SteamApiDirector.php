<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 10:41
 */

namespace SteamBundle\Handler;

use SteamBundle\Modal\ApiMethod;
use SteamBundle\Modal\MethodBuilderInterface;

/**
 * Получаем данные из апи стимы по аназванию метода
 *
 * Class SteamApiHandler
 * @package SteamAuthBundle\Handler
 */
class SteamApiDirector
{
    /**
     * @param MethodBuilderInterface $method
     * @return ApiMethod
     */
    public static function builder(MethodBuilderInterface $method)
    {
        $method->setUrl();
        $method->setParameters();

        return $method->getApiMethod();
    }
}