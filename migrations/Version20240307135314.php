<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307135314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT fk_3d660a3ba76ed395');
        $this->addSql('DROP INDEX idx_3d660a3ba76ed395');
        $this->addSql('ALTER TABLE tweet DROP user_id');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3BBF396750 FOREIGN KEY (id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT FK_3D660A3BBF396750');
        $this->addSql('ALTER TABLE tweet ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT fk_3d660a3ba76ed395 FOREIGN KEY (user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3d660a3ba76ed395 ON tweet (user_id)');
    }
}
