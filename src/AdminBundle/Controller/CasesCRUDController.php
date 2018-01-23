<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 18.09.17
 * Time: 23:38
 */

namespace AdminBundle\Controller;

use AdminBundle\Form\CasesFormFilterType;
use AdminBundle\Form\CasesFormType;
use AppBundle\Entity\Cases;
use Sonata\AdminBundle\Controller\CRUDController as Controller;

class CasesCRUDController extends Controller
{
    public function createAction()
    {
        $request = $this->getRequest();
        $this->admin->setFormTabs(['default' => ['groups' => []]]);

        $cases = new Cases();
        $form = $this->createForm(CasesFormType::class, $cases, [
            'action' => $this->generateUrl('sonata_cases_create'),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $cases->upload();

            $em = $this->getDoctrine()->getManager();
            $em->persist($cases);
            $em->flush();

            return $this->redirectToRoute('sonata_cases_edit', ['id' => $cases->getId()]);
        }

        $formFilter = $this->createForm(CasesFormFilterType::class);
        $listCasesSkins = $this->get('admin.service.cases_list');

        return $this->render($this->admin->getTemplate('edit'), [
            'action' => 'create',
            'object' => $cases,
            'listCasesSkins' => $listCasesSkins->getList(),
            'countSkins' => $listCasesSkins->getCountSkins(),
            'form' => $form->createView(),
            'formFilter' => $formFilter->createView(),
        ], null);
    }

    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());
        $this->admin->setFormTabs(['default' => ['groups' => []]]);

        /* @var Cases $cases */
        $cases = $this->admin->getObject($id);
        if (!$cases) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $form = $this->createForm(CasesFormType::class, $cases, [
            'action' => $this->generateUrl('sonata_cases_edit', ['id' => $id]),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $cases->upload();

            $em = $this->getDoctrine()->getManager();
            $em->persist($cases);
            $em->flush();

            return $this->redirectToRoute('sonata_cases_edit', ['id' => $cases->getId()]);
        }

        $formFilter = $this->createForm(CasesFormFilterType::class);
        $listCasesSkins = $this->get('admin.service.cases_list');

        return $this->render($this->admin->getTemplate('edit'), [
            'action' => 'edit',
            'object' => $cases,
            'listCasesSkins' => $listCasesSkins->getList($id),
            'countSkins' => $listCasesSkins->getCountSkins(),
            'form' => $form->createView(),
            'formFilter' => $formFilter->createView(),
        ], null);
    }
}