<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table user add column is_not_check_online
 */
class Version20171114080840 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE users ADD COLUMN is_not_check_online BOOLEAN NOT NULL DEFAULT FALSE;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE users DROP COLUMN is_not_check_online;");
    }
}
