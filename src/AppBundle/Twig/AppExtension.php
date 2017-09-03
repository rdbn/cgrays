<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('strlen', array($this, 'strlenFilter')),
        );
    }

    public function strlenFilter($string, $length = 50)
    {
        if (strlen($string) > $length) {
            return mb_strimwidth($string, 0, $length, '...', 'utf-8');
        }

        return $string;
    }
}
