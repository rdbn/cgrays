<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add table static_page
 */
class Version20170906071945 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS static_page(
          id SERIAL PRIMARY KEY,
          type_page VARCHAR(42) NOT NULL,
          text TEXT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS static_page_id_seq;");
        $this->addSql("CREATE UNIQUE INDEX IDX_static_page_type ON static_page(type_page);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP INDEX IF EXISTS IDX_static_page_type;");
        $this->addSql("DROP TABLE IF EXISTS static_page;");
        $this->addSql("DROP SEQUENCE IF EXISTS static_page_id_seq;");
    }
}
