<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table user_pick_up_skins_steam
 */
class Version20180305083440 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS user_pick_up_skins_steam(
          id BIGSERIAL PRIMARY KEY,
          cases_domain_id INT NOT NULL,
          user_id INT NOT NULL,
          skins_id INT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE INDEX IF NOT EXISTS IDX_user_pick_up_skins_steam_cases_domain_cases_skins ON user_pick_up_skins_steam (cases_domain_id, user_id, skins_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS user_pick_up_skins_steam;");
    }
}
