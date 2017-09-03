<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 16.04.17
 * Time: 23:25
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\News;
use AppBundle\Entity\NewsComment;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\User;
use AppBundle\Entity\Weapon;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNewsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            $news = new News();
            $news->setTitle('5 лучших сюжетных модификаций к Half-Life 2');
            $news->setText('
            В рядах поклонников Half-Life — снова оживление после долгого затишья: на днях бывший сценарист Valve Марк Лейдлоу опубликовал в 
            личном блоге историю, которая, судя по всему, должна была лечь в основу так и не вышедшей Half-Life 3.
            Все имена и названия локаций Лейдлоу изменил, однако фанаты тут же угадали в действующих лицах и Аликс, и Гордона. 
            Сам автор заявил, что его произведение — всего-навсего «творчество по мотивам», но поклонников это не убедило. 
            Они уже было смирились с тем, что никогда не увидят завершение истории, а тут — новое напоминание. Некоторые 
            решили спустить пар на странице DotA 2 в Steam: знаменитая MOBA от Valve в считанные часы получила ворох негативных 
            отзывов. Везде — одни и те же требования к разработчикам: прекратить выпускать шапки и скины для персонажей и оправдать 
            ожидания миллионов игроков — закончить, наконец, Half-Life 3.
            Даже если Valve не отреагирует на эти призывы, сообщество моддеров наверняка не останется в стороне. Более того, на 
            этой неделе инди-разработчик Лора Мичет уже запустила на портале itch.io конкурс, участникам которого предлагают самим 
            сделать третий эпизод Half-Life 2, причем неважно в каком жанре — лишь бы по канонам, описанным на сайте Лейдлоу.
            В связи с этими событиями мы решили вспомнить интересные сюжетные моды к Half-Life, которые создали фанаты. Какие-то из
            них добавляют «потерянные» уровни оригинальной игры, другие показывают, что происходит после окончания Episode Two, 
            третьи позволяют увидеть знакомые события глазами других персонажей.
            ');
            $manager->persist($news);

            for ($comment = 1; $comment <= 5; $comment++) {
                $user = $manager->getRepository(User::class)->findOneBy(['id' => $comment]);

                $newsComment = new NewsComment();
                $newsComment->setNews($news);
                $newsComment->setUser($user);
                $newsComment->setComment('Странно, что не выпустили аналогичную статью по модификациям S.T.A.L.K.E.R.`a, спустя столько времени-то.');

                $manager->persist($newsComment);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}