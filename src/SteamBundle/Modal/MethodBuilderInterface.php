<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 29.05.17
 * Time: 10:46
 */

namespace SteamBundle\Modal;

interface MethodBuilderInterface
{
    /**
     * Возвращет урл для обращения к меетоду уапи
     * @return string
     */
    public function setUrl();

    /**
     * Возращет параметры для запроса в апи
     * @return array
     */
    public function setParameters();

    /**
     * Возращет параметры для запроса в апи
     * @return ApiMethod
     */
    public function getApiMethod();
}