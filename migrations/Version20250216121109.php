<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216121109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, dossiermedicale_id INT DEFAULT NULL, nom_service_id INT DEFAULT NULL, date DATE NOT NULL, motif VARCHAR(255) NOT NULL, typeconsultation VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_964685A6F4DF715A (dossiermedicale_id), INDEX IDX_964685A6EC132991 (nom_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dossiermedicale (id INT AUTO_INCREMENT NOT NULL, imc DOUBLE PRECISION NOT NULL, date DATE NOT NULL, observations VARCHAR(255) NOT NULL, ordonnance VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle (id INT AUTO_INCREMENT NOT NULL, service_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_4E977E5CED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, chef_service VARCHAR(255) NOT NULL, nbr_salle INT NOT NULL, capacite INT NOT NULL, etat TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6F4DF715A FOREIGN KEY (dossiermedicale_id) REFERENCES dossiermedicale (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6EC132991 FOREIGN KEY (nom_service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE salle ADD CONSTRAINT FK_4E977E5CED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6F4DF715A');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6EC132991');
        $this->addSql('ALTER TABLE salle DROP FOREIGN KEY FK_4E977E5CED5CA9E6');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE dossiermedicale');
        $this->addSql('DROP TABLE salle');
        $this->addSql('DROP TABLE service');
    }
}
