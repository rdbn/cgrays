<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171014164502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS currency(
          id SERIAL PRIMARY KEY,
          name VARCHAR(45) NOT NULL,
          code VARCHAR(10) NOT NULL,
          UNIQUE (code)
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_balance_user(
          id SERIAL PRIMARY KEY,
          currency_id INT NOT NULL,
          cases_domain_id INT NOT NULL,
          user_id INT NOT NULL,
          balance DECIMAL(10, 5) NOT NULL DEFAULT 0,
          create_at TIMESTAMPTZ NOT NULL,
          last_update TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE UNIQUE INDEX IDX_balance_user_currency_cases_domain_user ON cases_balance_user(currency_id, cases_domain_id, user_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS currency;");
        $this->addSql("DROP TABLE IF EXISTS cases_balance_user;");
    }
}
