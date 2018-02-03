<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('strlen', array($this, 'strlenFilter')),
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecode')),
        );
    }

    public function strlenFilter($string, $length = 50)
    {
        if (strlen($string) > $length) {
            return mb_strimwidth($string, 0, $length, '...', 'utf-8');
        }

        return $string;
    }

    public function jsonDecode($string)
    {
        return json_decode($string, 1);
    }
}
