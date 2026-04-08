<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260329130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update game_character foreign key constraint to SET NULL on delete';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign key constraint for game_character
        $this->addSql('ALTER TABLE game_character DROP FOREIGN KEY FK_937AB034B03A8386');
        
        // Add new foreign key constraint with ON DELETE SET NULL
        $this->addSql('ALTER TABLE game_character ADD CONSTRAINT FK_937AB034B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Revert to original constraint without ON DELETE SET NULL
        $this->addSql('ALTER TABLE game_character DROP FOREIGN KEY FK_937AB034B03A8386');
        $this->addSql('ALTER TABLE game_character ADD CONSTRAINT FK_937AB034B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
    }
}
