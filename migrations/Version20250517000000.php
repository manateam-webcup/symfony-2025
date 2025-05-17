<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250517000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Ending entity and relationship to User';
    }

    public function up(Schema $schema): void
    {
        // Create ending table
        $this->addSql('CREATE TABLE ending (
            id INT AUTO_INCREMENT NOT NULL, 
            user_id INT NOT NULL, 
            title VARCHAR(255) NOT NULL, 
            description LONGTEXT NOT NULL, 
            emotion VARCHAR(255) NOT NULL, 
            image1_path VARCHAR(255) DEFAULT NULL, 
            image2_path VARCHAR(255) DEFAULT NULL, 
            image3_path VARCHAR(255) DEFAULT NULL, 
            audio_path VARCHAR(255) DEFAULT NULL, 
            video_path VARCHAR(255) DEFAULT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_1413D44FA76ED395 (user_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        // Add foreign key constraint
        $this->addSql('ALTER TABLE ending ADD CONSTRAINT FK_1413D44FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign key constraint
        $this->addSql('ALTER TABLE ending DROP FOREIGN KEY FK_1413D44FA76ED395');
        
        // Drop ending table
        $this->addSql('DROP TABLE ending');
    }
}