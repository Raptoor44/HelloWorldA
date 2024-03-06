<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306112336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet DROP at_created');
        $this->addSql('ALTER TABLE user_account ADD email VARCHAR(180) NOT NULL');
        $this->addSql('ALTER TABLE user_account ADD roles JSON NOT NULL');
        $this->addSql('ALTER TABLE user_account ADD password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user_account (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tweet ADD at_created VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('ALTER TABLE user_account DROP email');
        $this->addSql('ALTER TABLE user_account DROP roles');
        $this->addSql('ALTER TABLE user_account DROP password');
    }
}
