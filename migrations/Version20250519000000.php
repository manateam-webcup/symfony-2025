<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250519000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add likes and dislikes to Ending entity and create Comment entity';
    }

    public function up(Schema $schema): void
    {
        // Add likes and dislikes columns to ending table
        $this->addSql('ALTER TABLE ending ADD likes INT NOT NULL DEFAULT 0, ADD dislikes INT NOT NULL DEFAULT 0');

        // Create comment table
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ending_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CA76ED395 (user_id), INDEX IDX_9474526C5E7AA58C (ending_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5E7AA58C FOREIGN KEY (ending_id) REFERENCES ending (id)');

        // Create user_ending_interaction table
        $this->addSql('CREATE TABLE user_ending_interaction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ending_id INT NOT NULL, type VARCHAR(10) NOT NULL, INDEX IDX_F2F7B4F8A76ED395 (user_id), INDEX IDX_F2F7B4F85E7AA58C (ending_id), UNIQUE INDEX user_ending_unique (user_id, ending_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_ending_interaction ADD CONSTRAINT FK_F2F7B4F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_ending_interaction ADD CONSTRAINT FK_F2F7B4F85E7AA58C FOREIGN KEY (ending_id) REFERENCES ending (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraints first
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5E7AA58C');
        $this->addSql('ALTER TABLE user_ending_interaction DROP FOREIGN KEY FK_F2F7B4F8A76ED395');
        $this->addSql('ALTER TABLE user_ending_interaction DROP FOREIGN KEY FK_F2F7B4F85E7AA58C');

        // Drop tables
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE user_ending_interaction');

        // Remove likes and dislikes columns from ending table
        $this->addSql('ALTER TABLE ending DROP likes, DROP dislikes');
    }
}
