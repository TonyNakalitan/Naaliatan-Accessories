<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260626112038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add missing provider, is_verified, and verification_token columns to user table';
    }

    public function up(Schema $schema): void
    {
        // Add provider column only if it doesn't exist
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'provider')");
        $this->addSql("SET @sql = IF(@col = 0, 'ALTER TABLE user ADD provider VARCHAR(50) DEFAULT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Add is_verified column only if it doesn't exist
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'is_verified')");
        $this->addSql("SET @sql = IF(@col = 0, 'ALTER TABLE user ADD is_verified TINYINT NOT NULL DEFAULT 0', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Add verification_token column only if it doesn't exist
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'verification_token')");
        $this->addSql("SET @sql = IF(@col = 0, 'ALTER TABLE user ADD verification_token VARCHAR(255) DEFAULT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');
    }

    public function down(Schema $schema): void
    {
        // Remove provider column only if it exists
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'provider')");
        $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE user DROP provider', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Remove is_verified column only if it exists
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'is_verified')");
        $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE user DROP is_verified', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Remove verification_token column only if it exists
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'verification_token')");
        $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE user DROP verification_token', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');
    }
}
