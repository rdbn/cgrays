<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table bot_live_drop
 */
class Version20180225111009 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS bot_live_drop(
          id SERIAL NOT NULL PRIMARY KEY,
          cases_domain_id INT NOT NULL,
          user_id INT NOT NULL,
          hour_from SMALLINT NOT NULL,
          hour_to SMALLINT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE INDEX IF NOT EXISTS IDX_bot_live_drop_user ON bot_live_drop(cases_domain_id, user_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS bot_live_drop;");
    }
}
