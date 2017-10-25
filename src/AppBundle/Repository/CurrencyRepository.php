<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CurrencyRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function getDefaultCurrency()
    {
         $currency = $this->findBy([], ['id' => 'ASC'], 1, 0);

         return $currency[0];
    }
}