<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307092329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE response ADD user_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE response ADD tweet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB3C0C9956 FOREIGN KEY (user_account_id) REFERENCES user_account (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB1041E39B FOREIGN KEY (tweet_id) REFERENCES tweet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB3C0C9956 ON response (user_account_id)');
        $this->addSql('CREATE INDEX IDX_3E7B0BFB1041E39B ON response (tweet_id)');
        $this->addSql('ALTER TABLE tweet DROP CONSTRAINT fk_3d660a3bfbf32840');
        $this->addSql('DROP INDEX idx_3d660a3bfbf32840');
        $this->addSql('ALTER TABLE tweet DROP response_id');
        $this->addSql('ALTER TABLE user_account DROP CONSTRAINT fk_253b48ae91560f9d');
        $this->addSql('DROP INDEX idx_253b48ae91560f9d');
        $this->addSql('ALTER TABLE user_account DROP responses_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_account ADD responses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_account ADD CONSTRAINT fk_253b48ae91560f9d FOREIGN KEY (responses_id) REFERENCES response (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_253b48ae91560f9d ON user_account (responses_id)');
        $this->addSql('ALTER TABLE tweet ADD response_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT fk_3d660a3bfbf32840 FOREIGN KEY (response_id) REFERENCES response (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3d660a3bfbf32840 ON tweet (response_id)');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB3C0C9956');
        $this->addSql('ALTER TABLE response DROP CONSTRAINT FK_3E7B0BFB1041E39B');
        $this->addSql('DROP INDEX IDX_3E7B0BFB3C0C9956');
        $this->addSql('DROP INDEX IDX_3E7B0BFB1041E39B');
        $this->addSql('ALTER TABLE response DROP user_account_id');
        $this->addSql('ALTER TABLE response DROP tweet_id');
    }
}
