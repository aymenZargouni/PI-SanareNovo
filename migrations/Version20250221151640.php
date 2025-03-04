<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221151640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE technicien (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_96282C4CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technicien ADD CONSTRAINT FK_96282C4CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE claim ADD technicien_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE2713457256 FOREIGN KEY (technicien_id) REFERENCES technicien (id)');
        $this->addSql('CREATE INDEX IDX_A769DE2713457256 ON claim (technicien_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE2713457256');
        $this->addSql('ALTER TABLE technicien DROP FOREIGN KEY FK_96282C4CA76ED395');
        $this->addSql('DROP TABLE technicien');
        $this->addSql('DROP INDEX IDX_A769DE2713457256 ON claim');
        $this->addSql('ALTER TABLE claim DROP technicien_id');
    }
}
