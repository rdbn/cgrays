<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180414153459 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE static_page ADD COLUMN cases_domain_id INT DEFAULT 1;");
        $this->addSql("DROP INDEX IF EXISTS idx_static_page_type;");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS idx_static_page_type_domain ON static_page(type_page, cases_domain_id);");
    }

    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE static_page DROP COLUMN cases_domain_id;");
        $this->addSql("DROP INDEX IF EXISTS idx_static_page_type_domain;");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS idx_static_page_type ON static_page(type_page);");
    }
}
