<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create tables for product_dota
 */
class Version20170417184251 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE IF NOT EXISTS skins (
              id SERIAL PRIMARY KEY,
              type_skins_id INTEGER NOT NULL,
              quality_id INTEGER NOT NULL,
              rarity_id INTEGER NOT NULL,
              decor_id INTEGER NOT NULL,
              item_set_id INTEGER NOT NULL,
              weapon_id INTEGER NOT NULL,
              name VARCHAR(255) NOT NULL,
              icon_url VARCHAR(42) NOT NULL,
              icon_url_large VARCHAR(42) NOT NULL,
              description TEXT,
              created_at TIME
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS skins_price (
              id SERIAL PRIMARY KEY,
              class_id BIGINT NOT NULL,
              instance_id BIGINT NOT NULL,
              asset_id BIGINT NOT NULL,
              user_id INTEGER NOT NULL,
              skins_id INTEGER NOT NULL,
              price DECIMAL(11, 2) NOT NULL,
              is_sell BOOLEAN NOT NULL DEFAULT TRUE,
              is_remove BOOLEAN NOT NULL DEFAULT FALSE,
              created_at TIMESTAMPTZ NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS skins_trade (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL,
              skins_price_id INTEGER NOT NULL,
              status VARCHAR(45) NOT NULL,
              created_at TIMESTAMPTZ
            );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS skins_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS skins_price_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS skins_trade_id_seq START 1;");

        $this->addSql('CREATE UNIQUE INDEX IDX_skins_type_skins_quality_rarity_decor_item_set_weapon ON skins (type_skins_id, quality_id, rarity_id, decor_id, item_set_id, weapon_id);');
        $this->addSql('CREATE UNIQUE INDEX IDX_skins_price_class_instance_asset_user_skin ON skins_price (class_id, instance_id, asset_id, user_id, skins_id);');
        $this->addSql('CREATE UNIQUE INDEX IDX_skinss_order_user_skins_price ON skins_trade (user_id, skins_price_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX IDX_skins_type_skins_quality_rarity_decor_item_set_weapon;');
        $this->addSql('DROP INDEX IDX_skins_price_class_instance_asset_user_skin;');
        $this->addSql('DROP INDEX IDX_skinss_order_user_skins_price;');

        $this->addSql("DROP TABLE IF EXISTS skins;");
        $this->addSql("DROP TABLE IF EXISTS skins_price;");
        $this->addSql("DROP TABLE IF EXISTS skins_trade;");

        $this->addSql("DROP SEQUENCE IF EXISTS skins_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS skins_price_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS skins_trade_id_seq;");
    }
}
