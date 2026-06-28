<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260628121432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE todo ADD COLUMN created_at DATETIME NOT NULL DEFAULT '2000-01-01 00:00:00'");
        $this->addSql('ALTER TABLE todo ADD COLUMN done_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, title, done FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, done BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO todo (id, title, done) SELECT id, title, done FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
    }
}
