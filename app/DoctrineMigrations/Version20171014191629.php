<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table cases_skins_drop_user
 */
class Version20171014191629 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_skins_drop_user(
          id SERIAL PRIMARY KEY,
          skins_id INT NOT NULL,
          user_id INT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE INDEX IDX_cases_skins_drop_user_skins_user ON cases_skins_drop_user(skins_id, user_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS cases_skins_drop_user;");
    }
}
