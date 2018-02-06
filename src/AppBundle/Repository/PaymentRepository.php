<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.04.17
 * Time: 11:11
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class PaymentRepository extends EntityRepository
{
    /**
     * @return QueryBuilder
     */
    public function querySonata()
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->addSelect('u')
            ->addSelect('c')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.currency', 'c');

        return $qb;
    }
}