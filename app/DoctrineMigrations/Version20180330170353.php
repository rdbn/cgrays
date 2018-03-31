<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create tables PUBG
 */
class Version20180330170353 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS skins_pubg(
          id BIGSERIAL PRIMARY KEY,
          rarity_id INT NOT NULL,
          name VARCHAR(255) NOT NULL,
          image VARCHAR(255) NOT NULL,
          steam_price DECIMAL(21, 2) NOT NULL
        );
        ");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_skins_pubg_rarity_name ON skins_pubg (rarity_id, name);");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS rarity_pubg(
          id BIGSERIAL PRIMARY KEY,
          localized_tag_name VARCHAR(255) NOT NULL
        );
        ");
        $this->addSql("CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_rarity_pubg_localized_tag_name ON rarity_pubg (localized_tag_name);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS skins_pubg;");
        $this->addSql("DROP TABLE IF EXISTS rarity_pubg;");
    }
}
