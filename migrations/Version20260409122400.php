<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260409122400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unique constraint from creator field in game_character table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify to your needs
        $this->addSql('DROP INDEX UNIQ_41DC7136BC06EA63 ON game_character');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_41DC7136BC06EA63 ON game_character (creator)');
    }
}
