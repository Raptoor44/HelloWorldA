<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307135851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT fk_3d660a3bbf396750');
        $this->addSql('ALTER TABLE tweet ADD id_user INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3B6B3CA4B FOREIGN KEY (id_user) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3D660A3B6B3CA4B ON tweet (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT FK_3D660A3B6B3CA4B');
        $this->addSql('DROP INDEX IDX_3D660A3B6B3CA4B');
        $this->addSql('ALTER TABLE tweet DROP id_user');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT fk_3d660a3bbf396750 FOREIGN KEY (id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
