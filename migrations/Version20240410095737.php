<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410095737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(191) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE ext_translations (id SERIAL NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('CREATE INDEX general_translations_lookup_idx ON ext_translations (object_class, foreign_key)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
        $this->addSql('ALTER TABLE log DROP deleted_at');
        $this->addSql('ALTER TABLE response DROP deleted_at');
        $this->addSql('ALTER TABLE tweet DROP deleted_at');
        $this->addSql('ALTER TABLE user_account ADD created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE user_account ADD updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE user_account ALTER deleted_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN user_account.created IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_account.updated IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_account.deleted_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('ALTER TABLE user_account DROP created');
        $this->addSql('ALTER TABLE user_account DROP updated');
        $this->addSql('ALTER TABLE user_account ALTER deleted_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN user_account.deleted_at IS NULL');
        $this->addSql('ALTER TABLE log ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE response ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE tweet ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
