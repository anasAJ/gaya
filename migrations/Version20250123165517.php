<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123165517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E0532E3652');
        $this->addSql('DROP INDEX IDX_D3EDB1E0532E3652 ON production');
        $this->addSql('ALTER TABLE production DROP producti_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production ADD producti_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E0532E3652 FOREIGN KEY (producti_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E0532E3652 ON production (producti_id)');
    }
}
