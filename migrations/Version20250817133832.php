<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817133832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__fruits AS SELECT id, name, quantity_in_gram FROM fruits');
        $this->addSql('DROP TABLE fruits');
        $this->addSql('CREATE TABLE fruits (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity_in_gram INTEGER NOT NULL)');
        $this->addSql('INSERT INTO fruits (id, name, quantity_in_gram) SELECT id, name, quantity_in_gram FROM __temp__fruits');
        $this->addSql('DROP TABLE __temp__fruits');
        $this->addSql('CREATE TEMPORARY TABLE __temp__vegetables AS SELECT id, name, quantity_as_gram FROM vegetables');
        $this->addSql('DROP TABLE vegetables');
        $this->addSql('CREATE TABLE vegetables (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity_in_gram INTEGER NOT NULL)');
        $this->addSql('INSERT INTO vegetables (id, name, quantity_in_gram) SELECT id, name, quantity_as_gram FROM __temp__vegetables');
        $this->addSql('DROP TABLE __temp__vegetables');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__fruits AS SELECT id, name, quantity_in_gram FROM fruits');
        $this->addSql('DROP TABLE fruits');
        $this->addSql('CREATE TABLE fruits (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, quantity_in_gram INTEGER NOT NULL)');
        $this->addSql('INSERT INTO fruits (id, name, quantity_in_gram) SELECT id, name, quantity_in_gram FROM __temp__fruits');
        $this->addSql('DROP TABLE __temp__fruits');
        $this->addSql('CREATE TEMPORARY TABLE __temp__vegetables AS SELECT id, name, quantity_in_gram FROM vegetables');
        $this->addSql('DROP TABLE vegetables');
        $this->addSql('CREATE TABLE vegetables (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, quantity_as_gram INTEGER NOT NULL)');
        $this->addSql('INSERT INTO vegetables (id, name, quantity_as_gram) SELECT id, name, quantity_in_gram FROM __temp__vegetables');
        $this->addSql('DROP TABLE __temp__vegetables');
    }
}
