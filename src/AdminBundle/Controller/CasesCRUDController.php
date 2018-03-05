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
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\Skins;
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

            $rarities = $request->request->get('cases_skins_rarity');
            $newListSkins = $request->request->get('cases_skins_skins');

            $em = $this->getDoctrine()->getManager();
            if (count($newListSkins) > 0) {
                $skinsIds = array_keys($newListSkins);
                $skinEntities = $em->getRepository(Skins::class)
                    ->findSkinsByIds(implode(",", $skinsIds));

                foreach ($skinEntities as $skin) {
                    /* @var Skins $skin */
                    $rarityId = $skin->getRarity()->getId();
                    $procentRarity = $rarities[$rarityId] == "" ? 0: $rarities[$rarityId];
                    $isDrop = isset($newListSkins[$skin->getId()]['is_drop']) ? true : false;

                    $casesSkins = new CasesSkins();
                    $casesSkins->setSkins($skin);
                    $casesSkins->setCases($cases);
                    $casesSkins->setProcentRarity($procentRarity);
                    $casesSkins->setProcentSkins($isDrop);

                    $em->persist($casesSkins);
                }
            }

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

        $listCasesSkins = $this->get('admin.service.cases_list');
        $listSkins = $listCasesSkins->getList($id);
        $listRarity = $listCasesSkins->getListRarity($id);

        $form = $this->createForm(CasesFormType::class, $cases, [
            'action' => $this->generateUrl('sonata_cases_edit', ['id' => $id]),
        ]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $cases->upload();

            $rarities = $request->request->get('cases_skins_rarity');
            $newListSkins = $request->request->get('cases_skins_skins');

            $em = $this->getDoctrine()->getManager();
            $casesSkins = $cases->getCasesSkins();
            foreach ($casesSkins as $casesSkin) {
                /* @var CasesSkins $casesSkin */
                $skinsId = $casesSkin->getSkins()->getId();
                $rarityId = $casesSkin->getSkins()->getRarity()->getId();

                if (isset($newListSkins[$casesSkin->getSkins()->getId()])) {
                    $procentRarity = $rarities[$rarityId] == "" ? 0: $rarities[$rarityId];
                    $isDrop = isset($newListSkins[$skinsId]['is_drop']) ? true : false;

                    $casesSkin->setProcentRarity($procentRarity);
                    $casesSkin->setProcentSkins($isDrop);
                    unset($newListSkins[$skinsId]);
                } else {
                    $em->remove($casesSkin);
                }
            }

            if (count($newListSkins) > 0) {
                $skinsIds = array_keys($newListSkins);
                $skinEntities = $em->getRepository(Skins::class)
                    ->findSkinsByIds(implode(",", $skinsIds));

                foreach ($skinEntities as $skin) {
                    /* @var Skins $skin */
                    $rarityId = $skin->getRarity()->getId();
                    $procentRarity = $rarities[$rarityId] == "" ? 0: $rarities[$rarityId];
                    $isDrop = isset($newListSkins[$skin->getId()]['is_drop']) ? true : false;

                    $casesSkins = new CasesSkins();
                    $casesSkins->setSkins($skin);
                    $casesSkins->setCases($cases);
                    $casesSkins->setProcentRarity($procentRarity);
                    $casesSkins->setProcentSkins($isDrop);

                    $em->persist($casesSkins);
                }
            }

            $em->persist($cases);
            $em->flush();

            return $this->redirectToRoute('sonata_cases_edit', ['id' => $cases->getId()]);
        }

        $formFilter = $this->createForm(CasesFormFilterType::class);

        return $this->render($this->admin->getTemplate('edit'), [
            'action' => 'edit',
            'object' => $cases,
            'listRarity' => $listRarity,
            'listCasesSkins' => $listSkins,
            'countSkins' => $listCasesSkins->getCountSkins(),
            'form' => $form->createView(),
            'formFilter' => $formFilter->createView(),
        ], null);
    }
}