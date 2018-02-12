<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.02.2018
 * Time: 19:03
 */

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class StatisticCasesAdmin extends AbstractAdmin
{
    protected $baseRouteName = 'sonata_statistic';
    protected $baseRoutePattern = 'statistic';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_page' => 1,
    ];
}