<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.09.17
 * Time: 23:38
 */

namespace AdminBundle\Controller;

use AdminBundle\Form\StatisticCasesFilterType;
use AppBundle\Entity\StatisticCases;
use Doctrine\DBAL\DBALException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class StatisticCasesCRUDController extends Controller
{
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(StatisticCasesFilterType::class);
        $form->handleRequest($this->getRequest());

        $filter = [];
        foreach ($form->getIterator() as $name => $item) {
            $filter[$name] = $item->getData();
        }

        try {
            $statistics = $this->getDoctrine()->getRepository(StatisticCases::class)
                ->findStatisticByFilter($filter);
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $statistics = [];
        }

        return $this->render(':admin:statistic_list.html.twig', [
            'action' => 'list',
            'statistics' => $statistics,
            'csrf_token' => $this->getCsrfToken('sonata.batch'),
            'form' => $form->createView(),
        ], null);
    }
}