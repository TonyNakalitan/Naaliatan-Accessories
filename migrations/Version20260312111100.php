<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260312111100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_character RENAME INDEX uniq_937ab03460e92400 TO UNIQ_41DC713660E92400');
        $this->addSql('ALTER TABLE game_character RENAME INDEX idx_937ab034b03a8386 TO IDX_41DC7136B03A8386');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_character RENAME INDEX idx_41dc7136b03a8386 TO IDX_937AB034B03A8386');
        $this->addSql('ALTER TABLE game_character RENAME INDEX uniq_41dc713660e92400 TO UNIQ_937AB03460E92400');
    }
}
