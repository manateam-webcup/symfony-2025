<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add moderation fields to Ending entity
 */
final class Version20250520000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add moderation fields to Ending entity';
    }

    public function up(Schema $schema): void
    {
        // Add moderation fields to ending table
        $this->addSql('ALTER TABLE ending ADD moderation_reason VARCHAR(255) DEFAULT NULL, ADD approved_by VARCHAR(255) DEFAULT NULL, ADD moderation_score DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove moderation fields from ending table
        $this->addSql('ALTER TABLE ending DROP moderation_reason, DROP approved_by, DROP moderation_score');
    }
}