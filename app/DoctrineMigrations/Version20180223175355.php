<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table cases add column promotion_price
 */
class Version20180223175355 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases ADD COLUMN promotion_price NUMERIC(7, 2) NOT NULL DEFAULT 0;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases DROP COLUMN promotion_price;");
    }
}
