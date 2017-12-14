<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add table cases_static_page
 */
class Version20171212075740 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_static_page(
          id SERIAL PRIMARY KEY,
          cases_domain_id INT NOT NULL,
          type_page VARCHAR(42) NOT NULL,
          text TEXT NOT NULL,
          created_at TIMESTAMP WITH TIME ZONE NOT NULL
        );
        ");

        $this->addSql("CREATE UNIQUE INDEX IDX_cases_static_page_type_domain ON cases_static_page (type_page, cases_domain_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS cases_static_page;");
    }
}
