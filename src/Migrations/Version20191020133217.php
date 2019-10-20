<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191020133217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, birthdate DATE NOT NULL, country VARCHAR(2) DEFAULT NULL, presentation VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, validated BOOLEAN NOT NULL, message VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE TABLE reservation_client (reservation_id INTEGER NOT NULL, client_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, client_id))');
        $this->addSql('CREATE INDEX IDX_8FB54DCEB83297E7 ON reservation_client (reservation_id)');
        $this->addSql('CREATE INDEX IDX_8FB54DCE19EB6921 ON reservation_client (client_id)');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER NOT NULL, comment VARCHAR(255) NOT NULL, date DATETIME NOT NULL, rating INTEGER NOT NULL, accepted BOOLEAN NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9474526CB83297E7 ON comment (reservation_id)');
        $this->addSql('CREATE TABLE unavailable_period (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_B9D87FBB54177093 ON unavailable_period (room_id)');
        $this->addSql('CREATE TABLE owner (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address CLOB DEFAULT NULL, country VARCHAR(2) NOT NULL, telephone VARCHAR(255) NOT NULL, validated BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF60E67CA76ED395 ON owner (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, staff_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email_verified DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E3C61F9 ON user (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D4D57CD ON user (staff_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392A76ED395 ON staff (user_id)');
        $this->addSql('CREATE TABLE region (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, presentation CLOB DEFAULT NULL, country VARCHAR(2) DEFAULT NULL)');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, summary CLOB NOT NULL, description CLOB DEFAULT NULL, capacity INTEGER NOT NULL, superficy INTEGER NOT NULL, price INTEGER NOT NULL, address CLOB DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('CREATE TABLE room_region (room_id INTEGER NOT NULL, region_id INTEGER NOT NULL, PRIMARY KEY(room_id, region_id))');
        $this->addSql('CREATE INDEX IDX_4E2C37B754177093 ON room_region (room_id)');
        $this->addSql('CREATE INDEX IDX_4E2C37B798260155 ON room_region (region_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_client');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE unavailable_period');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE staff');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_region');
    }
}
