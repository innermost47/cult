<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712125436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE groupe_id groupe_id INT DEFAULT NULL, CHANGE praticien_id praticien_id INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F7A45358C FOREIGN KEY (groupe_id) REFERENCES `group` (id)');
        // $this->addSql('CREATE INDEX IDX_C53D045F7A45358C ON image (groupe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments CHANGE groupe_id groupe_id INT NOT NULL, CHANGE praticien_id praticien_id INT NOT NULL');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F7A45358C');
        $this->addSql('DROP INDEX IDX_C53D045F7A45358C ON image');
    }
}
