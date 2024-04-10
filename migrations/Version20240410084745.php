<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410084745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE log_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE response_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tweet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE log (id INT NOT NULL, id_user INT DEFAULT NULL, date_creation TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, content VARCHAR(255) NOT NULL, method_libelle VARCHAR(255) NOT NULL, controller_libelle VARCHAR(255) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F3F68C56B3CA4B ON log (id_user)');
        $this->addSql('CREATE TABLE response (id INT NOT NULL, user_account_id INT NOT NULL, tweet_id INT DEFAULT NULL, content VARCHAR(255) NOT NULL, number_likes INT NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB3C0C9956 ON response (user_account_id)');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB1041E39B ON response (tweet_id)');
        $this->addSql('CREATE TABLE tweet (id INT NOT NULL, id_user INT DEFAULT NULL, content VARCHAR(255) NOT NULL, number_likes INT NOT NULL, at_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3D660A3B6B3CA4B ON tweet (id_user)');
        $this->addSql('CREATE TABLE user_account (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, at_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(20) NOT NULL, last_name VARCHAR(20) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user_account (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE log ADD CONSTRAINT FK_8F3F68C56B3CA4B FOREIGN KEY (id_user) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB3C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB1041E39B FOREIGN KEY (tweet_id) REFERENCES tweet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3B6B3CA4B FOREIGN KEY (id_user) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE response_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tweet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_account_id_seq CASCADE');
        $this->addSql('ALTER TABLE log DROP CONSTRAINT FK_8F3F68C56B3CA4B');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB3C0C9956');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB1041E39B');
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT FK_3D660A3B6B3CA4B');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE tweet');
        $this->addSql('DROP TABLE user_account');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
