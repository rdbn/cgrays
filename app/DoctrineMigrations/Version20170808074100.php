<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170808074100 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
        CREATE TABLE IF NOT EXISTS payment (
          id SERIAL PRIMARY KEY,
          user_id INTEGER NOT NULL,
          payment_system_id INTEGER NOT NULL,
          payment_information VARCHAR(255) NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ');

        $this->addSql('
        CREATE TABLE IF NOT EXISTS payment_system (
          id  SERIAL PRIMARY KEY,
          name VARCHAR(120) NOT NULL
        );
        ');

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS payment_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS payment_system_id_seq START 1;");

        $this->addSql('CREATE INDEX IDX_payment_user_payment_system ON payment (user_id, payment_system_id);');
        $this->addSql('CREATE UNIQUE INDEX IDX_payment_system_name ON payment_system (name);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS payment;');
        $this->addSql('DROP TABLE IF EXISTS payment_system;');

        $this->addSql('DROP INDEX IDX_payment_user_payment_system;');
        $this->addSql('DROP INDEX IDX_payment_system_name;');

        $this->addSql("DROP SEQUENCE IF EXISTS payment_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS payment_system_id_seq;");
    }
}
