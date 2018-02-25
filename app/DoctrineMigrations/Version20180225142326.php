<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table
 */
class Version20180225142326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("DROP INDEX IF EXISTS idx_cases_skins_drop_user_skins_user;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_skins_drop_user_skins_user_cases_domain;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_skins_drop_user_skins_user_cases_domain_cases;");

        $this->addSql("ALTER TABLE cases_skins_drop_user ADD COLUMN cases_id INT NOT NULL DEFAULT 1;");

        $this->addSql("
        CREATE INDEX IF NOT EXISTS IDX_cases_skins_drop_user_skins_user_cases_domain_cases
          ON cases_skins_drop_user(skins_id, user_id, cases_domain_id, cases_id);
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP INDEX IF EXISTS idx_cases_skins_drop_user_skins_user;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_skins_drop_user_skins_user_cases_domain;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_skins_drop_user_skins_user_cases_domain_cases;");

        $this->addSql("ALTER TABLE cases_skins_drop_user DROP COLUMN cases_id;");
    }
}
