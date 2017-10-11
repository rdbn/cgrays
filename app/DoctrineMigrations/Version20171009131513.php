<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171009131513 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE skins ADD COLUMN steam_price DECIMAL(10, 5) NOT NULL DEFAULT 0;");
        $this->addSql("ALTER TABLE skins DROP COLUMN weapon_id;");
        $this->addSql("ALTER TABLE skins DROP COLUMN created_at;");

        $this->addSql("ALTER TABLE quality DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE decor DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE type_skins DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE item_set DROP COLUMN internal_name;");
        $this->addSql("ALTER TABLE rarity DROP COLUMN internal_name;");

        $this->addSql("DROP TABLE IF EXISTS weapon;");
        $this->addSql('CREATE UNIQUE INDEX IDX_skins_type_skins_quality_rarity_decor_item_set ON skins (type_skins_id, quality_id, rarity_id, decor_id, item_set_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE skins DROP COLUMN steam_price;");
    }
}
