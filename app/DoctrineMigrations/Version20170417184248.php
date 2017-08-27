<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create tables dictionary for product
 */
class Version20170417184248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE IF NOT EXISTS type_skins (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS rarity (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL,
              color VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS decor (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS item_set (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS weapon (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS quality (
              id SERIAL PRIMARY KEY,
              internal_name VARCHAR(45) NOT NULL,
              localized_tag_name VARCHAR(45) NOT NULL
            );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS type_product_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS quality_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS rarity_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS decor_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS item_set_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS weapon_id_seq START 1;");

        $this->addSql('CREATE UNIQUE INDEX IDX_type_skins_localized_tag_name ON type_skins (localized_tag_name);');
        $this->addSql('CREATE UNIQUE INDEX IDX_quality_localized_tag_name ON quality (localized_tag_name);');
        $this->addSql('CREATE UNIQUE INDEX IDX_rarity_localized_tag_name ON rarity (localized_tag_name);');
        $this->addSql('CREATE UNIQUE INDEX IDX_decor_localized_tag_name ON decor (localized_tag_name);');
        $this->addSql('CREATE UNIQUE INDEX IDX_item_set_localized_tag_name ON item_set (localized_tag_name);');
        $this->addSql('CREATE UNIQUE INDEX IDX_weapon_localized_tag_name ON weapon (localized_tag_name);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX IDX_type_skins_localized_tag_name;');
        $this->addSql('DROP INDEX IDX_quality_localized_tag_name;');
        $this->addSql('DROP INDEX IDX_rarity_localized_tag_name;');
        $this->addSql('DROP INDEX IDX_decor_localized_tag_name;');
        $this->addSql('DROP INDEX IDX_item_set_localized_tag_name;');
        $this->addSql('DROP INDEX IDX_weapon_localized_tag_name;');

        $this->addSql("DROP TABLE IF EXISTS type_skins;");
        $this->addSql("DROP TABLE IF EXISTS quality;");
        $this->addSql("DROP TABLE IF EXISTS rarity;");
        $this->addSql("DROP TABLE IF EXISTS decor;");
        $this->addSql("DROP TABLE IF EXISTS item_set;");
        $this->addSql("DROP TABLE IF EXISTS weapon;");

        $this->addSql("DROP SEQUENCE IF EXISTS type_product_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS quality_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS rarity_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS decor_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS item_set_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS weapon_id_seq;");
    }
}
