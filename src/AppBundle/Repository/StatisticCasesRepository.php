<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * StatisticCasesRepository
 */
class StatisticCasesRepository extends EntityRepository
{
    /**
     * @param array $filter
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findStatisticByFilter(array $filter)
    {
        $dbal = $this->getEntityManager()->getConnection();
        $qb = $dbal->createQueryBuilder();
        $qb
            ->addSelect('SUM(sc.hit_cases) as hit_cases')
            ->addSelect('SUM(sc.open_cases) as open_cases')
            ->addSelect('SUM(sc.pick_up_skins) as pick_up_skins')
            ->addSelect('SUM(sc.sell_skins) as sell_skins')
            ->from('statistic_cases', 'sc')
            ->andWhere('sc.date_at >= :date_from')
            ->andWhere('sc.date_at <= :date_to')
            ->groupBy('ident');

        switch ($filter['group']) {
            case "date";
                $qb->addSelect('sc.date_at as ident');
                break;
            case "user";
                $qb
                    ->addSelect('u.username as ident')
                    ->leftJoin('sc', 'users', 'u', 'sc.user_id = u.id');
                break;
            case "cases";
                $qb
                    ->addSelect('c.name as ident')
                    ->leftJoin('sc', 'cases', 'c', 'sc.cases_id = c.id');
                break;
            case "skins";
                $qb
                    ->addSelect('s.name as ident')
                    ->leftJoin('sc', 'skins', 's', 'sc.skins_id = s.id');
                break;
            case "cases_category";
                $qb
                    ->addSelect('cc.name as ident')
                    ->leftJoin('sc', 'cases_category', 'cc', 'sc.cases_category_id = cc.id');
                break;
            case "cases_domain";
                $qb
                    ->addSelect('cd.domain as ident')
                    ->leftJoin('sc', 'cases_domain', 'cd', 'sc.cases_domain_id = cd.id');
                break;
        }

        if (isset($filter['user'])) {
            $qb->andWhere($qb->expr()->eq('sc.user_id', $filter['user']->getId()));
        }

        if (isset($filter['cases'])) {
            $qb->andWhere($qb->expr()->eq('sc.cases_id', $filter['cases']->getId()));
        }

        if (isset($filter['skins'])) {
            $qb->andWhere($qb->expr()->eq('sc.skins_id', $filter['skins']->getId()));
        }

        if (isset($filter['cases_category'])) {
            $qb->andWhere($qb->expr()->eq('sc.cases_category_id', $filter['cases_category']->getId()));
        }

        if (isset($filter['cases_domain'])) {
            $qb->andWhere($qb->expr()->eq('sc.cases_domain_id', $filter['cases_domain']->getId()));
        }

        $stmt = $dbal->prepare($qb->getSQL());
        $stmt->bindParam('date_from', $filter['date_from'], \PDO::PARAM_STR);
        $stmt->bindParam('date_to', $filter['date_to'], \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
