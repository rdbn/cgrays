<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.05.17
 * Time: 21:52
 */

namespace SteamBundle\Modal;

abstract class AbstractMethod implements MethodBuilderInterface
{
    /**
     * @var ApiMethod
     */
    protected $apiMethod;

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $locale;

    /**
     * AbstractMethod constructor.
     * @param ApiMethod $apiMethod
     * @param $apiUrl
     * @param apiKey
     * @param $locale
     */
    public function __construct(ApiMethod $apiMethod, $apiUrl, $apiKey, $locale)
    {
        $this->apiMethod = $apiMethod;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->locale = $locale;
    }

}