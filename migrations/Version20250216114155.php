<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216114155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_category (blog_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_72113DE6DAE07E97 (blog_id), INDEX IDX_72113DE612469DE2 (category_id), PRIMARY KEY(blog_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE claim (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, reclamation VARCHAR(255) NOT NULL, INDEX IDX_A769DE27517FE9FE (equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, date_achat DATE DEFAULT NULL, prix NUMERIC(10, 2) NOT NULL, date_reparation DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historique (id INT AUTO_INCREMENT NOT NULL, equipment_id INT NOT NULL, date_reparation DATETIME NOT NULL, rapport_detaille LONGTEXT DEFAULT NULL, INDEX IDX_EDBFD5EC517FE9FE (equipment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, blog_id INT DEFAULT NULL, score INT NOT NULL, INDEX IDX_D8892622DAE07E97 (blog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_category ADD CONSTRAINT FK_72113DE6DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_category ADD CONSTRAINT FK_72113DE612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE27517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE historique ADD CONSTRAINT FK_EDBFD5EC517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP slug');
        $this->addSql('ALTER TABLE category DROP slug');
        $this->addSql('ALTER TABLE comment ADD blog_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP is_approved');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('CREATE INDEX IDX_9474526CDAE07E97 ON comment (blog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_category DROP FOREIGN KEY FK_72113DE6DAE07E97');
        $this->addSql('ALTER TABLE blog_category DROP FOREIGN KEY FK_72113DE612469DE2');
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE27517FE9FE');
        $this->addSql('ALTER TABLE historique DROP FOREIGN KEY FK_EDBFD5EC517FE9FE');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622DAE07E97');
        $this->addSql('DROP TABLE blog_category');
        $this->addSql('DROP TABLE claim');
        $this->addSql('DROP TABLE equipment');
        $this->addSql('DROP TABLE historique');
        $this->addSql('DROP TABLE rating');
        $this->addSql('ALTER TABLE blog ADD slug VARCHAR(255) NOT NULL, DROP updated_at, DROP created_at');
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CDAE07E97');
        $this->addSql('DROP INDEX IDX_9474526CDAE07E97 ON comment');
        $this->addSql('ALTER TABLE comment ADD is_approved TINYINT(1) NOT NULL, DROP blog_id, DROP created_at');
    }
}
