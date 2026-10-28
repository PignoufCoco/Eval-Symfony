<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261028152048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre ADD oeuvre_recouverte_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEEAFC6390 FOREIGN KEY (oeuvre_recouverte_id) REFERENCES oeuvre (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35FE2EFEEAFC6390 ON oeuvre (oeuvre_recouverte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEEAFC6390');
        $this->addSql('DROP INDEX UNIQ_35FE2EFEEAFC6390 ON oeuvre');
        $this->addSql('ALTER TABLE oeuvre DROP oeuvre_recouverte_id');
    }
}
