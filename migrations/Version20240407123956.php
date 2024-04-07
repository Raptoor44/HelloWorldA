<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407123956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE log DROP CONSTRAINT fk_8f3f68c579f37ae5');
        $this->addSql('DROP INDEX idx_8f3f68c579f37ae5');
        $this->addSql('ALTER TABLE log RENAME COLUMN id_user_id TO id_user');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C56B3CA4B FOREIGN KEY (id_user) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F3F68C56B3CA4B ON log (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C56B3CA4B');
        $this->addSql('DROP INDEX IDX_8F3F68C56B3CA4B');
        $this->addSql('ALTER TABLE log RENAME COLUMN id_user TO id_user_id');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT fk_8f3f68c579f37ae5 FOREIGN KEY (id_user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8f3f68c579f37ae5 ON log (id_user_id)');
    }
}
