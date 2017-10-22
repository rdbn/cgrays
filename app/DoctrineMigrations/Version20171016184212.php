<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create table balance_user
 */
class Version20171016184212 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
        CREATE TABLE IF NOT EXISTS balance_user(
          id SERIAL PRIMARY KEY,
          currency_id INT NOT NULL,
          user_id INT NOT NULL,
          balance DECIMAL(10, 5) NOT NULL DEFAULT 0,
          created_at TIMESTAMPTZ NOT NULL,
          last_update TIMESTAMPTZ NOT NULL
        );
        ');

        $this->addSql('CREATE UNIQUE INDEX idx_balance_user_currency_user ON balance_user(currency_id, user_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS balance_user;');
    }
}
