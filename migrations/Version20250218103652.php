<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218103652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation ADD num_salle_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A652319F3 FOREIGN KEY (num_salle_id) REFERENCES salle (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_964685A652319F3 ON consultation (num_salle_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A652319F3');
        $this->addSql('DROP INDEX UNIQ_964685A652319F3 ON consultation');
        $this->addSql('ALTER TABLE consultation DROP num_salle_id');
    }
}
