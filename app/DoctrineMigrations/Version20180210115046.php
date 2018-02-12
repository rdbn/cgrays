<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add column cases_domain_id to table cases_skins_drop_user
 */
class Version20180210115046 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins_drop_user ADD COLUMN cases_domain_id INT NOT NULL DEFAULT 1;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins_drop_user DROP COLUMN cases_domain_id;");
    }
}
