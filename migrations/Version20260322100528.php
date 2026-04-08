<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260322100528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD sku VARCHAR(100) DEFAULT NULL, ADD reorder_point INT DEFAULT NULL, ADD reorder_quantity INT DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD low_stock_alert_enabled TINYINT NOT NULL, ADD last_cycle_count DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADF9038C4 ON product (sku)');
        $this->addSql('ALTER TABLE stock_transaction ADD previous_stock INT DEFAULT NULL, ADD new_stock INT DEFAULT NULL, ADD location VARCHAR(100) DEFAULT NULL, ADD transfer_to_location VARCHAR(100) DEFAULT NULL, CHANGE type type VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_D34A04ADF9038C4 ON product');
        $this->addSql('ALTER TABLE product DROP sku, DROP reorder_point, DROP reorder_quantity, DROP location, DROP low_stock_alert_enabled, DROP last_cycle_count');
        $this->addSql('ALTER TABLE stock_transaction DROP previous_stock, DROP new_stock, DROP location, DROP transfer_to_location, CHANGE type type VARCHAR(20) NOT NULL');
    }
}
