<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407084932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE log_method_id_seq CASCADE');
        $this->addSql('DROP TABLE log_method');
        $this->addSql('ALTER TABLE log ADD id_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE log ADD method_libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE log ADD controller_libelle VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE log DROP id_controller');
        $this->addSql('ALTER TABLE log DROP id_route');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C579F37AE5 FOREIGN KEY (id_user_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F3F68C579F37AE5 ON log (id_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE log_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log_method (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C579F37AE5');
        $this->addSql('DROP INDEX IDX_8F3F68C579F37AE5');
        $this->addSql('ALTER TABLE log ADD id_controller INT NOT NULL');
        $this->addSql('ALTER TABLE log ADD id_route INT NOT NULL');
        $this->addSql('ALTER TABLE log DROP id_user_id');
        $this->addSql('ALTER TABLE log DROP method_libelle');
        $this->addSql('ALTER TABLE log DROP controller_libelle');
    }
}
