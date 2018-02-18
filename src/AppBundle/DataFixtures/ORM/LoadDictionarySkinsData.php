<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesDomain;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDictionarySkinsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var array
     */
    private $rarities = [
        ['id' => '1', 'localized_tag_name' => 'Армейское качество', 'color' => '4b69ff'],
        ['id' => '2', 'localized_tag_name' => 'Ширпотреб', 'color' => 'b0c3d9'],
        ['id' => '3', 'localized_tag_name' => 'Другое', 'color' => 'fff'],
        ['id' => '6', 'localized_tag_name' => 'Промышленное качество', 'color' => 'fff'],
        ['id' => '7', 'localized_tag_name' => 'базового класса', 'color' => '4b69ff'],
        ['id' => '8', 'localized_tag_name' => 'Засекреченное', 'color' => '4b69ff'],
        ['id' => '9', 'localized_tag_name' => 'высшего класса', 'color' => '4b69ff'],
        ['id' => '10', 'localized_tag_name' => 'Тайное', 'color' => '4b69ff'],
        ['id' => '11', 'localized_tag_name' => 'Запрещенное', 'color' => '4b69ff'],
        ['id' => '12', 'localized_tag_name' => 'экзотичного вида', 'color' => '4b69ff'],
        ['id' => '13', 'localized_tag_name' => 'примечательного типа', 'color' => '4b69ff'],
        ['id' => '14', 'localized_tag_name' => 'экстраординарного типа', 'color' => '4b69ff'],
        ['id' => '15', 'localized_tag_name' => 'Контрабанда', 'color' => '4b69ff'],
    ];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $dbal = $this->container->get('doctrine.dbal.default_connection');
        foreach ($this->rarities as $rarity) {
            $dbal->insert('rarity', $rarity);

            $name = uniqid();
            $dbal->insert('quality', ['localized_tag_name' => $name]);
            $dbal->insert('item_set', ['localized_tag_name' => $name]);
            $dbal->insert('weapon', ['localized_tag_name' => $name]);
            $dbal->insert('decor', ['localized_tag_name' => $name]);
            $dbal->insert('type_skins', ['localized_tag_name' => $name]);
        }

        $casesDomain = new CasesDomain();
        $casesDomain->setDomain('http://casesopen.loc');
        $casesDomain->setSteamApiKey('72375542F1B6D85628155E5820CB5FA4');
        $casesDomain->setUuid('83e8fb6b-908f-4adf-bc80-a4392dd04d3a');

        $manager->persist($casesDomain);

        for ($i = 0; $i < 3; $i++) {
            $casesDomain = new CasesDomain();
            $casesDomain->setDomain('http://test' . uniqid() . '.ru');
            $casesDomain->setSteamApiKey(uniqid());

            $manager->persist($casesDomain);

            $casesCategory = new CasesCategory();
            $casesCategory->setName('Test' . uniqid());

            $manager->persist($casesCategory);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}