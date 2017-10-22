<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Alter tables skins, quality, decor, type_skins, item_set, rarity, weapon
 */
class Version20171009131513 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE skins ADD COLUMN steam_price DECIMAL(10, 5) NOT NULL DEFAULT 0;");
        $this->addSql("ALTER TABLE skins DROP COLUMN created_at;");

        $this->addSql("ALTER TABLE quality DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE decor DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE type_skins DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE item_set DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE rarity DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE weapon DROP COLUMN internal_name;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE skins DROP COLUMN steam_price;");
    }
}
