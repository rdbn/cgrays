<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table cases_skins_drop_user_pubg
 */
class Version20180402082318 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_skins_drop_user_pubg(
          id SERIAL PRIMARY KEY,
          cases_domain_id INTEGER NOT NULL,
          cases_id INTEGER NOT NULL,
          skins_id INTEGER NOT NULL,
          user_id  INTEGER NOT NULL,
          created_at TIMESTAMP WITH TIME ZONE NOT NULL
        );
        ");
        $this->addSql("
        CREATE INDEX idx_cases_skins_pubg_drop_user_skins_user_cases_domain_cases 
          ON cases_skins_drop_user_pubg (skins_id, user_id, cases_domain_id, cases_id);
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS cases_skins_drop_user_pubg;");
    }
}
