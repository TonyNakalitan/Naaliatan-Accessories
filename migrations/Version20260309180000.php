<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix character table name (character is reserved keyword)
 */
final class Version20260309180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename character table to game_character to avoid MySQL reserved keyword conflict';
    }

    public function up(Schema $schema): void
    {
        // Drop foreign keys first
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD1136BE75');
        
        // Rename the table
        $this->addSql('RENAME TABLE `character` TO `game_character`');
        
        // Recreate foreign keys with new table name
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD1136BE75 FOREIGN KEY (character_id) REFERENCES game_character (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys first
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD1136BE75');
        
        // Rename the table back
        $this->addSql('RENAME TABLE `game_character` TO `character`');
        
        // Recreate foreign keys with original table name
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD1136BE75 FOREIGN KEY (character_id) REFERENCES `character` (id)');
    }
}
