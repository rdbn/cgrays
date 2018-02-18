<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180214183933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE statistic_cases DROP COLUMN skins_id");
        $this->addSql("ALTER TABLE statistic_cases ALTER COLUMN date_at TYPE DATE;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
