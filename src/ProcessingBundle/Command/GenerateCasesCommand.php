<?php

namespace ProcessingBundle\Command;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Skins;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCasesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:generate_cases')
            ->addArgument('category_id', InputArgument::OPTIONAL, 'ID category cases.')
            ->addArgument('cases_domain_id', InputArgument::OPTIONAL, 'Cases domain id cases.')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name generate case.')
            ->setDescription('Generate test cases.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categoryId = $input->getArgument('category_id');
        $casesDomainId = $input->getArgument('cases_domain_id');
        $name = $input->getArgument('name');

        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $logger = $container->get('logger');

        $em->beginTransaction();
        try {
            $skins = $em->getRepository(Skins::class)
                ->findBy([], [], 20, 0);

            $casesCategory = $em->getRepository(CasesCategory::class)
                ->findOneBy(['id' => $categoryId]);

            $casesDomain = $em->getRepository(CasesDomain::class)
                ->findOneBy(['id' => $casesDomainId]);

            $cases = new Cases();
            $cases->setCasesCategory($casesCategory);
            $cases->setCasesDomain($casesDomain);
            $cases->setName($name);
            $cases->setPrice(mt_rand(10, 50));
            $cases->setImage('image/300.png');

            $sort = [];
            foreach ($skins as $skin) {
                $count = rand(10, 15);
                $sort[$skin->getId()] = $count;
            }
            $cases->setSort(json_encode($sort));

            $em->persist($cases);
            $em->flush();

            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            $logger->error($e->getMessage());
        }

        $logger->error("End generate");
    }
}
