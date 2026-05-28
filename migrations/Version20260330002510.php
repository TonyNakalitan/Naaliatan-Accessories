<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330002510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Safely CHANGE is_verified only if it exists
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'is_verified')");
        $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE user CHANGE is_verified is_verified TINYINT NOT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Safely CHANGE provider only if it exists
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'provider')");
        $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE user CHANGE provider provider VARCHAR(50) DEFAULT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Rename google_id to verification_token only if google_id exists and verification_token does not
        $this->addSql("SET @has_google = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'google_id')");
        $this->addSql("SET @has_token  = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user' AND COLUMN_NAME = 'verification_token')");
        $this->addSql("SET @sql = IF(@has_google > 0 AND @has_token = 0, 'ALTER TABLE user CHANGE google_id verification_token VARCHAR(255) DEFAULT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` CHANGE provider provider VARCHAR(50) DEFAULT \'local\', CHANGE is_verified is_verified TINYINT DEFAULT 0 NOT NULL, CHANGE verification_token google_id VARCHAR(255) DEFAULT NULL');
    }
}
