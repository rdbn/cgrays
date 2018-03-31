<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table cases_skins_pubg
 */
class Version20180331171355 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_skins_pubg(
          id SERIAL PRIMARY KEY,
          cases_id INT NOT NULL,
          skins_id INT NOT NULL,
          procent_rarity SMALLINT NOT NULL DEFAULT 0,
          is_drop BOOLEAN NOT NULL DEFAULT TRUE,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_cases_skins_pubg_cases_skins ON cases_skins_pubg (cases_id, skins_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS cases_skins_pubg;");
    }
}
