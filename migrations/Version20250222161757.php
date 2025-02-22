<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222161757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE predictive ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE predictive ADD CONSTRAINT FK_3AEB1F58953C1C61 FOREIGN KEY (source_id) REFERENCES source (id)');
        $this->addSql('CREATE INDEX IDX_3AEB1F58953C1C61 ON predictive (source_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE predictive DROP FOREIGN KEY FK_3AEB1F58953C1C61');
        $this->addSql('DROP INDEX IDX_3AEB1F58953C1C61 ON predictive');
        $this->addSql('ALTER TABLE predictive DROP source_id');
    }
}
