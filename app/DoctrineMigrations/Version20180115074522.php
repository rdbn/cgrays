<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add column steam_api_key for table cases_domain
 */
class Version20180115074522 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_domain ADD COLUMN steam_api_key VARCHAR(255) NOT NULL DEFAULT '';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE cases_domain DROP COLUMN steam_api_key;");
    }
}
