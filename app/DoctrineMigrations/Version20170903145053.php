<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170903145053 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE IF NOT EXISTS news(
          id SERIAL PRIMARY KEY,
          title VARCHAR(255) NOT NULL,
          text TEXT NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS news_comment(
          id SERIAL PRIMARY KEY,
          news_id INT NOT NULL,
          user_id INT NOT NULL,
          comment VARCHAR(255) NOT NULL,
          created_at TIMESTAMPTZ NOT NULL
        );
        ");

        $this->addSql("
        CREATE TABLE IF NOT EXISTS news_like(
          news_id INT NOT NULL,
          user_id INT NOT NULL,
          PRIMARY KEY (news_id, user_id)
        );
        ");

        $this->addSql("CREATE SEQUENCE IF NOT EXISTS news_id_seq START 1;");
        $this->addSql("CREATE SEQUENCE IF NOT EXISTS news_comment_id_seq START 1;");

        $this->addSql('CREATE INDEX IDX_news_title ON news (title);');
        $this->addSql('CREATE INDEX IDX_news_comment_news ON news_comment (news_id, user_id);');
        $this->addSql('CREATE UNIQUE INDEX IDX_news_like_news_user ON news_like (news_id, user_id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX IDX_news_comment_news;');
        $this->addSql('DROP INDEX IDX_news_title;');
        $this->addSql('DROP INDEX IDX_news_like_news_user;');

        $this->addSql('DROP TABLE IF EXISTS news;');
        $this->addSql('DROP TABLE IF EXISTS news_comment;');
        $this->addSql('DROP TABLE IF EXISTS news_like;');

        $this->addSql("DROP SEQUENCE IF EXISTS news_id_seq;");
        $this->addSql("DROP SEQUENCE IF EXISTS news_comment_id_seq;");
    }
}
