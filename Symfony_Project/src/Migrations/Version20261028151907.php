<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028151907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artiste (id INT AUTO_INCREMENT NOT NULL, pseudonyme VARCHAR(50) NOT NULL, nom_reel VARCHAR(50) NOT NULL, bio VARCHAR(255) DEFAULT NULL, nationalite VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE artiste_oeuvre (artiste_id INT NOT NULL, oeuvre_id INT NOT NULL, INDEX IDX_6F8FD8FF21D25844 (artiste_id), INDEX IDX_6F8FD8FF88194DE8 (oeuvre_id), PRIMARY KEY (artiste_id, oeuvre_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE benevole (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, adresse VARCHAR(50) NOT NULL, quartier VARCHAR(50) NOT NULL, latitude NUMERIC(10, 7) NOT NULL, longitude NUMERIC(10, 7) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oeuvre (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(50) NOT NULL, date_creation DATE NOT NULL, description LONGTEXT DEFAULT NULL, date_destruction DATE NOT NULL, lieux_id INT NOT NULL, INDEX IDX_35FE2EFEA2C806AC (lieux_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oeuvre_technique (oeuvre_id INT NOT NULL, technique_id INT NOT NULL, INDEX IDX_CEAB50CA88194DE8 (oeuvre_id), INDEX IDX_CEAB50CA1F8ACB26 (technique_id), PRIMARY KEY (oeuvre_id, technique_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo (id INT AUTO_INCREMENT NOT NULL, date_prise_vue DATETIME NOT NULL, nom_fichier VARCHAR(50) NOT NULL, etat_oeuvre VARCHAR(50) NOT NULL, oeuvre_id INT NOT NULL, benevole_id INT NOT NULL, INDEX IDX_14B7841888194DE8 (oeuvre_id), INDEX IDX_14B78418E77B7C09 (benevole_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE technique (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE artiste_oeuvre ADD CONSTRAINT FK_6F8FD8FF21D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste_oeuvre ADD CONSTRAINT FK_6F8FD8FF88194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEA2C806AC FOREIGN KEY (lieux_id) REFERENCES lieu (id)');
        $this->addSql('ALTER TABLE oeuvre_technique ADD CONSTRAINT FK_CEAB50CA88194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oeuvre_technique ADD CONSTRAINT FK_CEAB50CA1F8ACB26 FOREIGN KEY (technique_id) REFERENCES technique (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841888194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id)');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B78418E77B7C09 FOREIGN KEY (benevole_id) REFERENCES benevole (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste_oeuvre DROP FOREIGN KEY FK_6F8FD8FF21D25844');
        $this->addSql('ALTER TABLE artiste_oeuvre DROP FOREIGN KEY FK_6F8FD8FF88194DE8');
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEA2C806AC');
        $this->addSql('ALTER TABLE oeuvre_technique DROP FOREIGN KEY FK_CEAB50CA88194DE8');
        $this->addSql('ALTER TABLE oeuvre_technique DROP FOREIGN KEY FK_CEAB50CA1F8ACB26');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841888194DE8');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B78418E77B7C09');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE artiste_oeuvre');
        $this->addSql('DROP TABLE benevole');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE oeuvre');
        $this->addSql('DROP TABLE oeuvre_technique');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE technique');
    }
}
