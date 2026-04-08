<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260322000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create stock table for inventory management';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE stock (
            id INT AUTO_INCREMENT NOT NULL,
            product_id INT NOT NULL,
            created_by_id INT DEFAULT NULL,
            quantity INT NOT NULL,
            notes LONGTEXT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_4B3656604584665A (product_id),
            INDEX IDX_4B365660B03A8386 (created_by_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE stock');
    }
}
