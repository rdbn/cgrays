<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 23.08.17
 * Time: 10:47
 */

namespace AppBundle\Modal;

interface DictionaryInterface
{
    /**
     * Get id
     *
     * @return string
     */
    public function getId();

    /**
     * Set localizedTagName
     *
     * @param string $localizedTagName
     *
     * @return self
     */
    public function setLocalizedTagName($localizedTagName);

    /**
     * Get localizedTagName
     *
     * @return string
     */
    public function getLocalizedTagName();
}