<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250123152401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE production (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, client_id INT DEFAULT NULL, INDEX IDX_D3EDB1E0A76ED395 (user_id), INDEX IDX_D3EDB1E019EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_product (production_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_C65F8970ECC6147F (production_id), INDEX IDX_C65F89704584665A (product_id), PRIMARY KEY(production_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE production_product ADD CONSTRAINT FK_C65F8970ECC6147F FOREIGN KEY (production_id) REFERENCES production (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE production_product ADD CONSTRAINT FK_C65F89704584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E0A76ED395');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E019EB6921');
        $this->addSql('ALTER TABLE production_product DROP FOREIGN KEY FK_C65F8970ECC6147F');
        $this->addSql('ALTER TABLE production_product DROP FOREIGN KEY FK_C65F89704584665A');
        $this->addSql('DROP TABLE production');
        $this->addSql('DROP TABLE production_product');
    }
}
