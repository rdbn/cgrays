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
        // todo: Create table heroes
        $this->addSql("
            CREATE TABLE IF NOT EXISTS heroes (
              id SERIAL PRIMARY KEY,
              name VARCHAR(45) NOT NULL,
              title VARCHAR(45) NOT NULL,
              UNIQUE (name)
            );
        ");

        // todo: Create table type_product
        $this->addSql("
            CREATE TABLE IF NOT EXISTS type_product (
              id SERIAL PRIMARY KEY,
              name VARCHAR(45) NOT NULL,
              title VARCHAR(45) NOT NULL,
              UNIQUE (name)
            );
        ");

        // todo: Create table quality
        $this->addSql("
            CREATE TABLE IF NOT EXISTS quality (
              id SERIAL PRIMARY KEY,
              name VARCHAR(45) NOT NULL,
              title VARCHAR(45) NOT NULL,
              color VARCHAR(15) NOT NULL,
              UNIQUE (name)
            );
        ");

        // todo: Create table rareness
        $this->addSql("
            CREATE TABLE IF NOT EXISTS rarity (
              id SERIAL PRIMARY KEY,
              name VARCHAR(45) NOT NULL,
              title VARCHAR(45) NOT NULL,
              color VARCHAR(45) NOT NULL,
              order_item INTEGER NOT NULL,
              UNIQUE (name)
            );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS heroes_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS type_product_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS quality_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS rarity_id_seq START 1;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS heroes;");
        $this->addSql("DROP TABLE IF EXISTS type_product;");
        $this->addSql("DROP TABLE IF EXISTS quality;");
        $this->addSql("DROP TABLE IF EXISTS rarity;");

        $this->addSql("DROP SEQUENCE IF EXISTS category_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS type_product_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS quality_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS rarity_id_seq;");
    }
}
