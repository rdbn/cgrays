<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Create tables for product
 */
class Version20170417184251 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // todo: Create table product
        $this->addSql("
            CREATE TABLE IF NOT EXISTS product (
              id SERIAL PRIMARY KEY,
              class_id INTEGER,
              instance_id INTEGER,
              heroes_id INTEGER NOT NULL REFERENCES heroes (id),
              type_product_id INTEGER NOT NULL REFERENCES type_product (id),
              quality_id INTEGER NOT NULL REFERENCES quality (id),
              rarity_id INTEGER NOT NULL REFERENCES rarity (id),
              name VARCHAR(255) NOT NULL,
              icon_url VARCHAR(42) NOT NULL,
              icon_url_large VARCHAR(42) NOT NULL,
              description TEXT,
              created_at TIME,
              UNIQUE (class_id, instance_id)
            );
        ");

        $this->addSql("
            CREATE TABLE IF NOT EXISTS product_price (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL REFERENCES users (id),
              product_id INTEGER NOT NULL REFERENCES product (id),
              price DECIMAL(11, 2) NOT NULL,
              is_sell BOOLEAN NOT NULL DEFAULT TRUE,
              created_at TIME
            );
        ");

        // todo: Create table basket
        $this->addSql("
            CREATE TABLE IF NOT EXISTS order_product (
              id SERIAL PRIMARY KEY,
              user_id INTEGER NOT NULL REFERENCES users (id),
              product_price_id INTEGER NOT NULL REFERENCES product_price (id),
              count INTEGER NOT NULL DEFAULT 0,
              created_at TIME,
              UNIQUE (user_id, product_price_id)
            );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS product_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS product_price_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS order_product_id_seq START 1;");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS order_product;");
        $this->addSql("DROP TABLE IF EXISTS product_price;");
        $this->addSql("DROP TABLE IF EXISTS product;");

        $this->addSql("DROP SEQUENCE IF EXISTS order_product_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS product_price_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS product_id_seq;");
    }
}
