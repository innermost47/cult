<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719123012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE adfi (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, zip VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, site VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F7A45358C FOREIGN KEY (groupe_id) REFERENCES `group` (id)');
        // $this->addSql('CREATE INDEX IDX_C53D045F7A45358C ON image (groupe_id)');
        // $this->addSql('ALTER TABLE user ADD adfi_id INT NOT NULL');
        // $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EA94F325 FOREIGN KEY (adfi_id) REFERENCES adfi (id)');
        // $this->addSql('CREATE INDEX IDX_8D93D649EA94F325 ON user (adfi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EA94F325');
        $this->addSql('DROP TABLE adfi');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F7A45358C');
        $this->addSql('DROP INDEX IDX_C53D045F7A45358C ON image');
        $this->addSql('DROP INDEX IDX_8D93D649EA94F325 ON user');
        $this->addSql('ALTER TABLE user DROP adfi_id');
    }
}
