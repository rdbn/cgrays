<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table change name column cases_skins
 */
class Version20180203111846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins RENAME count TO procent_rarity;");
        $this->addSql("ALTER TABLE cases_skins RENAME count_drop TO procent_skins;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins RENAME procent_rarity TO count;");
        $this->addSql("ALTER TABLE cases_skins RENAME procent_skins TO count_drop;");
    }
}
