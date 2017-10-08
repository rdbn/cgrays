<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add Tables for cases
 */
class Version20170910162533 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_domain(
          id SERIAL PRIMARY KEY,
          domain VARCHAR(255) NOT NULL,
          uuid VARCHAR(36) NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_category(
          id SERIAL PRIMARY KEY,
          name VARCHAR(45) NOT NULL
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases(
          id SERIAL PRIMARY KEY,
          cases_domain_id INT NOT NULL,
          cases_category_id INT NOT NULL,
          name VARCHAR(45) NOT NULL,
          price DECIMAL(11, 2) NOT NULL,
          image VARCHAR(255) NOT NULL,
          sort TEXT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS cases_skins(
          id SERIAL PRIMARY KEY,
          cases_id INT NOT NULL,
          skins_id INT NOT NULL,
          count SMALLINT NOT NULL DEFAULT 0,
          count_drop SMALLINT NOT NULL DEFAULT 0,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS cases_domain_id_seq;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS category_cases_id_seq;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS cases_id_seq;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS cases_skins_id_seq;");

        $this->addSql("CREATE INDEX IDX_cases_domain_uuid ON cases_domain (uuid);");
        $this->addSql("CREATE UNIQUE INDEX IDX_cases_domain_domain ON cases_domain (domain);");
        $this->addSql("CREATE UNIQUE INDEX IDX_cases_category_name ON cases_category (name);");
        $this->addSql("CREATE UNIQUE INDEX IDX_cases_name_cases_category_cases_domain ON cases (name, cases_category_id, cases_domain_id);");
        $this->addSql("CREATE UNIQUE INDEX IDX_cases_skins_cases_skins ON cases_skins (cases_id, skins_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_domain_uuid;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_domain_domain;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_category_name;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_name;");
        $this->addSql("DROP INDEX IF EXISTS IDX_cases_skins_cases_skins;");

        $this->addSql("DROP TABLE IF EXISTS cases_domain;");
        $this->addSql("DROP TABLE IF EXISTS category_cases;");
        $this->addSql("DROP TABLE IF EXISTS cases_skins;");
        $this->addSql("DROP TABLE IF EXISTS cases;");

        $this->addSql("DROP SEQUENCE IF EXISTS cases_domain_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS category_cases_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS cases_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS cases_skins_id_seq;");
    }
}
