<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter table cases drop column sort
 */
class Version20180225103624 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases DROP COLUMN sort;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
