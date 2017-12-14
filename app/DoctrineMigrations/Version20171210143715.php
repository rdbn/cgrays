<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add column cases_domain_id for table cases_skins_pick_up_user
 */
class Version20171210143715 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE cases_skins_pick_up_user ADD COLUMN cases_domain_id INT NOT NULL DEFAULT 0;');
        $this->addSql("DROP INDEX idx_cases_skins_pick_up_user_skins_user;");
        $this->addSql("CREATE INDEX idx_cases_skins_pick_up_user_skins_user_cases_domain ON cases_skins_pick_up_user (skins_id, user_id, cases_domain_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_skins_pick_up_user DROP COLUMN cases_domain_id;");
    }
}
