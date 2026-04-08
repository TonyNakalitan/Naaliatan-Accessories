<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329132000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update order foreign key constraint to SET NULL on delete and make customer_id nullable';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign key constraint for order
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        
        // Make customer_id nullable
        $this->addSql('ALTER TABLE `order` MODIFY customer_id INT DEFAULT NULL');
        
        // Add new foreign key constraint with ON DELETE SET NULL
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Revert to original constraint
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE `order` MODIFY customer_id INT NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
    }
}
