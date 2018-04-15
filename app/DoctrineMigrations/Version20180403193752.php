<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table cases_skins_pick_up_user_pubg
 */
class Version20180403193752 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_skins_pick_up_user_pubg(
          id SERIAL PRIMARY KEY,
          skins_id INTEGER NOT NULL,
          user_id INTEGER NOT NULL,
          created_at TIMESTAMP WITH TIME ZONE NOT NULL,
          cases_domain_id INTEGER NOT NULL,
          is_remove BOOLEAN NOT NULL DEFAULT FALSE
        );
        ");
        $this->addSql("
        CREATE INDEX IF NOT EXISTS idx_cases_skins_pick_up_user_pubg_skins_user_cases_domain
          ON cases_skins_pick_up_user_pubg (skins_id, user_id, cases_domain_id);
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS cases_skins_pick_up_user_pubg;");
    }
}
