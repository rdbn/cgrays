<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Change type column payment_information (varchar -> text)
 */
class Version20180205213948 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE payment ALTER COLUMN payment_information TYPE TEXT;");
        $this->addSql("CREATE INDEX IF NOT EXISTS IDX_payment_created_user_currency ON payment(created_at, user_id, currency_id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE payment ALTER COLUMN payment_information TYPE VARCHAR(255);");
        $this->addSql("DROP INDEX IF EXISTS IDX_payment_created_user_currency;");
    }
}
