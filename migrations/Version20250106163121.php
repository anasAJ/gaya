<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250106163121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, added_date VARCHAR(255) NOT NULL, added_time VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_category (source_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_456C2F13953C1C61 (source_id), INDEX IDX_456C2F1312469DE2 (category_id), PRIMARY KEY(source_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_product (source_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_9A95BCC5953C1C61 (source_id), INDEX IDX_9A95BCC54584665A (product_id), PRIMARY KEY(source_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE source_category ADD CONSTRAINT FK_456C2F13953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_category ADD CONSTRAINT FK_456C2F1312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_product ADD CONSTRAINT FK_9A95BCC5953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_product ADD CONSTRAINT FK_9A95BCC54584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_category DROP FOREIGN KEY FK_456C2F13953C1C61');
        $this->addSql('ALTER TABLE source_category DROP FOREIGN KEY FK_456C2F1312469DE2');
        $this->addSql('ALTER TABLE source_product DROP FOREIGN KEY FK_9A95BCC5953C1C61');
        $this->addSql('ALTER TABLE source_product DROP FOREIGN KEY FK_9A95BCC54584665A');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP TABLE source_category');
        $this->addSql('DROP TABLE source_product');
    }
}
