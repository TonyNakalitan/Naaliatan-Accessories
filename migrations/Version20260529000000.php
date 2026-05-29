<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260529000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change game_character.image from varchar(255) to longtext to store base64 data URIs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_character CHANGE image image LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE game_character CHANGE image image VARCHAR(255) DEFAULT NULL');
    }
}
