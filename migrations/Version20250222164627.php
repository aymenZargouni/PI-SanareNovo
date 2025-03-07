<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222164627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ADD dossiermedical_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EBDAFFF6DA FOREIGN KEY (dossiermedical_id) REFERENCES dossiermedicale (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1ADAD7EBDAFFF6DA ON patient (dossiermedical_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EBDAFFF6DA');
        $this->addSql('DROP INDEX UNIQ_1ADAD7EBDAFFF6DA ON patient');
        $this->addSql('ALTER TABLE patient DROP dossiermedical_id');
    }
}
