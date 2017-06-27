<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 31.05.17
 * Time: 21:37
 */

namespace AppBundle\Services;

class UploadImageService
{
    const UPLOAD_IMAGE_PATH = 'image/';

    /**
     * @var string
     */
    private $steamCommunityImageUrl;

    /**
     * UploadImageService constructor.
     * @param $steamCommunityImageUrl
     */
    public function __construct($steamCommunityImageUrl)
    {
        $this->steamCommunityImageUrl = $steamCommunityImageUrl;
    }


    public function getPath($nameImage)
    {
        return __DIR__.'/../../../web/'.self::UPLOAD_IMAGE_PATH.$nameImage;
    }

    /**
     * @param string $url
     * @return string
     */
    public function upload($url)
    {
        $byteImage = file_get_contents($this->steamCommunityImageUrl.$url);
        $nameImage = md5(uniqid(time(), true)).'.png';
        file_put_contents($this->getPath($nameImage), $byteImage);

        return $nameImage;
    }
}
