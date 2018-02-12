<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table statistic_cases
 */
class Version20180211161838 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS statistic_cases(
          id BIGSERIAL NOT NULL PRIMARY KEY,
          date_at TIMESTAMPTZ NOT NULL,
          user_id INT NOT NULL,
          cases_id INT NOT NULL,
          cases_domain_id INT NOT NULL,
          cases_category_id INT NOT NULL,
          skins_id INT NOT NULL DEFAULT 0,
          hit_cases INT NOT NULL DEFAULT 0,
          open_cases INT NOT NULL DEFAULT 0,
          pick_up_skins INT NOT NULL DEFAULT 0,
          sell_skins NUMERIC(20, 5) DEFAULT 0 NOT NULL
        );
        ");

        $this->addSql("
        CREATE UNIQUE INDEX IF NOT EXISTS
          UNIQ_statistic_cases_date_user_domain_category
        ON
          statistic_cases(date_at, user_id, cases_id, cases_domain_id, cases_category_id);
        ");

        $this->addSql("CREATE INDEX IF NOT EXISTS IDX_statistic_cases_skins ON statistic_cases(skins_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS statistic_cases;");
    }
}
