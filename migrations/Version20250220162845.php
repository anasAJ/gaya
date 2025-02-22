<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220162845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE predictive ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE predictive ADD CONSTRAINT FK_3AEB1F58296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_3AEB1F58296CD8AE ON predictive (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE predictive DROP FOREIGN KEY FK_3AEB1F58296CD8AE');
        $this->addSql('DROP INDEX IDX_3AEB1F58296CD8AE ON predictive');
        $this->addSql('ALTER TABLE predictive DROP team_id');
    }
}
