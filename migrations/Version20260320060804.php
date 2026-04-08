<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320060804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY `FK_FD06F647A76ED395`');
        $this->addSql('ALTER TABLE activity_log CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT FK_FD06F647A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE stock_transaction DROP FOREIGN KEY `FK_ADF9A3E5A76ED395`');
        $this->addSql('ALTER TABLE stock_transaction CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stock_transaction ADD CONSTRAINT FK_ADF9A3E5A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_log DROP FOREIGN KEY FK_FD06F647A76ED395');
        $this->addSql('ALTER TABLE activity_log CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE activity_log ADD CONSTRAINT `FK_FD06F647A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE stock_transaction DROP FOREIGN KEY FK_ADF9A3E5A76ED395');
        $this->addSql('ALTER TABLE stock_transaction CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock_transaction ADD CONSTRAINT `FK_ADF9A3E5A76ED395` FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
