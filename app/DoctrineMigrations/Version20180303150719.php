<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table cases_skins change type procent_skins
 */
class Version20180303150719 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins DROP COLUMN procent_skins;");
        $this->addSql("ALTER TABLE cases_skins ADD COLUMN procent_skins BOOLEAN NOT NULL DEFAULT TRUE;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) {}
}
