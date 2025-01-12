<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105175739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client ADD status_id INT DEFAULT NULL, ADD phase_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404556BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C744045599091188 FOREIGN KEY (phase_id) REFERENCES phase (id)');
        $this->addSql('CREATE INDEX IDX_C74404556BF700BD ON client (status_id)');
        $this->addSql('CREATE INDEX IDX_C744045599091188 ON client (phase_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C74404556BF700BD');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C744045599091188');
        $this->addSql('DROP INDEX IDX_C74404556BF700BD ON client');
        $this->addSql('DROP INDEX IDX_C744045599091188 ON client');
        $this->addSql('ALTER TABLE client DROP status_id, DROP phase_id');
    }
}
