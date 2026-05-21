<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260521043930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, reference_number VARCHAR(100) NOT NULL, method VARCHAR(50) NOT NULL, amount NUMERIC(10, 2) NOT NULL, status VARCHAR(30) NOT NULL, notes LONGTEXT DEFAULT NULL, reviewed_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, order_id INT NOT NULL, paid_by_id INT DEFAULT NULL, reviewed_by_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6D28840D8BF1AE50 (reference_number), INDEX IDX_6D28840D8D9F6D38 (order_id), INDEX IDX_6D28840D7F9BC654 (paid_by_id), INDEX IDX_6D28840DFC6B21F1 (reviewed_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D7F9BC654 FOREIGN KEY (paid_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D8D9F6D38');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D7F9BC654');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DFC6B21F1');
        $this->addSql('DROP TABLE payment');
    }
}
