<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update foreign key constraints to SET NULL on delete for user references';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign key constraint for stock
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660B03A8386');
        
        // Add new foreign key constraint with ON DELETE SET NULL
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Revert to original constraints without ON DELETE SET NULL
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660B03A8386');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }
}
