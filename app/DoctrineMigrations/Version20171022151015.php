<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table user drop column balance
 */
class Version20171022151015 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE users DROP COLUMN balance;");
        $this->addSql("ALTER TABLE payment DROP COLUMN payment_system_id;");
        $this->addSql("ALTER TABLE payment ADD COLUMN currency_id INT NOT NULL DEFAULT 1;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE users ADD COLUMN balance DECIMAL(7, 2) NOT NULL DEFAULT 0;");
        $this->addSql("ALTER TABLE payment ADD COLUMN payment_system_id INT NOT NULL DEFAULT 1;");
        $this->addSql("ALTER TABLE payment DROP COLUMN currency_id;");
    }
}
