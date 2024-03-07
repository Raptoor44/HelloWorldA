<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307091332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE response_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE response (id INT NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE tweet ADD response_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3BFBF32840 FOREIGN KEY (response_id) REFERENCES response (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3D660A3BFBF32840 ON tweet (response_id)');
        $this->addSql('ALTER TABLE user_account ADD responses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_account ADD CONSTRAINT FK_253B48AE91560F9D FOREIGN KEY (responses_id) REFERENCES response (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_253B48AE91560F9D ON user_account (responses_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT FK_3D660A3BFBF32840');
        $this->addSql('ALTER TABLE user_account DROP CONSTRAINT FK_253B48AE91560F9D');
        $this->addSql('DROP SEQUENCE response_id_seq CASCADE');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP INDEX IDX_253B48AE91560F9D');
        $this->addSql('ALTER TABLE user_account DROP responses_id');
        $this->addSql('DROP INDEX IDX_3D660A3BFBF32840');
        $this->addSql('ALTER TABLE tweet DROP response_id');
    }
}
