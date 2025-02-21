<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221181158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature ADD lettre_motivation LONGTEXT DEFAULT NULL, ADD statut VARCHAR(50) NOT NULL, CHANGE offre_id offre_id INT NOT NULL');
        $this->addSql('ALTER TABLE claim ADD technicien_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE2713457256 FOREIGN KEY (technicien_id) REFERENCES technicien (id)');
        $this->addSql('CREATE INDEX IDX_A769DE2713457256 ON claim (technicien_id)');
        $this->addSql('ALTER TABLE offre CHANGE description description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE technicien ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE technicien ADD CONSTRAINT FK_96282C4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_96282C4CA76ED395 ON technicien (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidature DROP lettre_motivation, DROP statut, CHANGE offre_id offre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE2713457256');
        $this->addSql('DROP INDEX IDX_A769DE2713457256 ON claim');
        $this->addSql('ALTER TABLE claim DROP technicien_id');
        $this->addSql('ALTER TABLE offre CHANGE description description VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE technicien DROP FOREIGN KEY FK_96282C4CA76ED395');
        $this->addSql('DROP INDEX UNIQ_96282C4CA76ED395 ON technicien');
        $this->addSql('ALTER TABLE technicien DROP user_id');
    }
}
