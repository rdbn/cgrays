<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403201017 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS user_pick_up_skins_steam_pubg(
          id BIGSERIAL PRIMARY KEY,
          cases_domain_id INTEGER NOT NULL,
          user_id INTEGER NOT NULL,
          skins_id INTEGER NOT NULL,
          created_at TIMESTAMP WITH TIME ZONE NOT NULL
        );
        ");
        $this->addSql("
        CREATE INDEX IF NOT EXISTS idx_user_pick_up_skins_steam_pubg_cases_domain_cases_skins
          ON user_pick_up_skins_steam_pubg (cases_domain_id, user_id, skins_id);
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS user_pick_up_skins_steam_pubg;");
    }
}
