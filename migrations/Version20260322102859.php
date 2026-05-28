<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322102859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Safely drop the unique index if it exists
        $this->addSql("SET @idx = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product' AND INDEX_NAME = 'UNIQ_D34A04ADF9038C4')");
        $this->addSql("SET @sql = IF(@idx > 0, 'DROP INDEX UNIQ_D34A04ADF9038C4 ON product', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Drop product columns if they exist
        foreach (['sku', 'reorder_point', 'reorder_quantity', 'location', 'low_stock_alert_enabled', 'last_cycle_count'] as $col) {
            $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product' AND COLUMN_NAME = '$col')");
            $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE product DROP COLUMN $col', 'SELECT 1')");
            $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');
        }

        // Add is_stored to stock_transaction if missing
        $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_transaction' AND COLUMN_NAME = 'is_stored')");
        $this->addSql("SET @sql = IF(@col = 0, 'ALTER TABLE stock_transaction ADD is_stored TINYINT DEFAULT 0 NOT NULL', 'SELECT 1')");
        $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');

        // Drop stock_transaction columns if they exist
        foreach (['previous_stock', 'new_stock', 'location', 'transfer_to_location', 'added_stock', 'current_stock'] as $col) {
            $this->addSql("SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stock_transaction' AND COLUMN_NAME = '$col')");
            $this->addSql("SET @sql = IF(@col > 0, 'ALTER TABLE stock_transaction DROP COLUMN $col', 'SELECT 1')");
            $this->addSql('PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt');
        }

        $this->addSql("ALTER TABLE stock_transaction CHANGE type type VARCHAR(20) NOT NULL");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD sku VARCHAR(100) DEFAULT NULL, ADD reorder_point INT DEFAULT NULL, ADD reorder_quantity INT DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD low_stock_alert_enabled TINYINT NOT NULL, ADD last_cycle_count DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADF9038C4 ON product (sku)');
        $this->addSql('ALTER TABLE stock_transaction ADD previous_stock INT DEFAULT NULL, ADD new_stock INT DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD transfer_to_location VARCHAR(100) DEFAULT NULL, ADD added_stock INT DEFAULT 0 NOT NULL, ADD current_stock INT DEFAULT 0 NOT NULL, DROP is_stored, CHANGE type type VARCHAR(30) NOT NULL');
    }
}
