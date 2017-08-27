<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Created tables users
 */
class Version20170404073101 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // todo: Create table users
        $this->addSql("
            CREATE TABLE IF NOT EXISTS users (
              id SERIAL PRIMARY KEY,
              steam_id BIGINT NOT NULL,
              username VARCHAR(255) NOT NULL,
              salt VARCHAR(32) NOT NULL,
              password VARCHAR(88) NOT NULL,
              balance DECIMAL(7, 2) NOT NULL DEFAULT 0,
              avatar VARCHAR(255) DEFAULT NULL,
              is_online BOOLEAN NOT NULL DEFAULT FALSE,
              last_online TIMESTAMPTZ NOT NULL,
              is_sell BOOLEAN NOT NULL DEFAULT TRUE,
              created_at TIMESTAMPTZ NOT NULL,
              UNIQUE (steam_id)
            );
        ");

        // todo: Create table roles
        $this->addSql("
            CREATE TABLE IF NOT EXISTS roles (
              id SERIAL PRIMARY KEY,
              role VARCHAR(45) NOT NULL,
              created_at TIMESTAMPTZ NOT NULL,
              UNIQUE (role)
            );
        ");

        // todo: Create table users_roles
        $this->addSql("
            CREATE TABLE IF NOT EXISTS users_roles (
              user_id INTEGER NOT NULL REFERENCES users (id),
              role_id INTEGER NOT NULL REFERENCES roles (id),
              PRIMARY KEY (user_id, role_id)
            );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS users_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS roles_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS category_id_seq START 1;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TABLE IF EXISTS users_roles;");
        $this->addSql("DROP TABLE IF EXISTS users;");
        $this->addSql("DROP TABLE IF EXISTS roles;");

        $this->addSql("DROP SEQUENCE IF EXISTS roles_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS users_id_seq;");
    }
}
