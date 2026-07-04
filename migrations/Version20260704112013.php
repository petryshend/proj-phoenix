<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260704112013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user table and per-user ownership of todos (existing ownerless todos are dropped)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        // Existing todos have no owner; drop them and rebuild the table with a required owner_id.
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, done BOOLEAN NOT NULL, created_at DATETIME NOT NULL, done_at DATETIME DEFAULT NULL, owner_id INTEGER NOT NULL, CONSTRAINT FK_5A0EB6A07E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5A0EB6A07E3C61F9 ON todo (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TEMPORARY TABLE __temp__todo AS SELECT id, title, done, created_at, done_at FROM todo');
        $this->addSql('DROP TABLE todo');
        $this->addSql('CREATE TABLE todo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, done BOOLEAN NOT NULL, created_at DATETIME DEFAULT \'2000-01-01 00:00:00\' NOT NULL, done_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO todo (id, title, done, created_at, done_at) SELECT id, title, done, created_at, done_at FROM __temp__todo');
        $this->addSql('DROP TABLE __temp__todo');
    }
}
