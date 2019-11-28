<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191128211730 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, image_id INTEGER DEFAULT NULL, firstname VARCHAR(255) NOT NULL COLLATE BINARY, lastname VARCHAR(255) NOT NULL COLLATE BINARY, telephone VARCHAR(255) NOT NULL COLLATE BINARY, address VARCHAR(255) DEFAULT NULL COLLATE BINARY, birthdate DATE NOT NULL, country VARCHAR(2) DEFAULT NULL COLLATE BINARY, presentation VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C74404553DA5256D FOREIGN KEY (image_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO client (id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation) SELECT id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('CREATE INDEX IDX_C74404553DA5256D ON client (image_id)');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921');
        $this->addSql('DROP INDEX IDX_42C8495554177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, client_id, room_id, start, until, validated, message FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, validated BOOLEAN NOT NULL, message VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_42C8495519EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, client_id, room_id, start, until, validated, message) SELECT id, client_id, room_id, start, until, validated, message FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('DROP INDEX IDX_8FB54DCEB83297E7');
        $this->addSql('DROP INDEX IDX_8FB54DCE19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation_client AS SELECT reservation_id, client_id FROM reservation_client');
        $this->addSql('DROP TABLE reservation_client');
        $this->addSql('CREATE TABLE reservation_client (reservation_id INTEGER NOT NULL, client_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, client_id), CONSTRAINT FK_8FB54DCEB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8FB54DCE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation_client (reservation_id, client_id) SELECT reservation_id, client_id FROM __temp__reservation_client');
        $this->addSql('DROP TABLE __temp__reservation_client');
        $this->addSql('CREATE INDEX IDX_8FB54DCEB83297E7 ON reservation_client (reservation_id)');
        $this->addSql('CREATE INDEX IDX_8FB54DCE19EB6921 ON reservation_client (client_id)');
        $this->addSql('DROP INDEX IDX_9474526C54177093');
        $this->addSql('DROP INDEX IDX_9474526CB83297E7');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, reservation_id, room_id, comment, date, rating, accepted FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER NOT NULL, room_id INTEGER NOT NULL, comment VARCHAR(255) NOT NULL COLLATE BINARY, date DATETIME NOT NULL, rating INTEGER NOT NULL, accepted BOOLEAN NOT NULL, CONSTRAINT FK_9474526CB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526C54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO comment (id, reservation_id, room_id, comment, date, rating, accepted) SELECT id, reservation_id, room_id, comment, date, rating, accepted FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526C54177093 ON comment (room_id)');
        $this->addSql('CREATE INDEX IDX_9474526CB83297E7 ON comment (reservation_id)');
        $this->addSql('DROP INDEX IDX_B9D87FBB54177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unavailable_period AS SELECT id, room_id, start, until, description FROM unavailable_period');
        $this->addSql('DROP TABLE unavailable_period');
        $this->addSql('CREATE TABLE unavailable_period (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_B9D87FBB54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO unavailable_period (id, room_id, start, until, description) SELECT id, room_id, start, until, description FROM __temp__unavailable_period');
        $this->addSql('DROP TABLE __temp__unavailable_period');
        $this->addSql('CREATE INDEX IDX_B9D87FBB54177093 ON unavailable_period (room_id)');
        $this->addSql('DROP INDEX UNIQ_CF60E67CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__owner AS SELECT id, user_id, firstname, lastname, address, country, telephone, validated, birthdate FROM owner');
        $this->addSql('DROP TABLE owner');
        $this->addSql('CREATE TABLE owner (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL COLLATE BINARY, lastname VARCHAR(255) NOT NULL COLLATE BINARY, address CLOB DEFAULT NULL COLLATE BINARY, country VARCHAR(2) NOT NULL COLLATE BINARY, telephone VARCHAR(255) NOT NULL COLLATE BINARY, validated BOOLEAN NOT NULL, birthdate DATETIME NOT NULL, CONSTRAINT FK_CF60E67CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO owner (id, user_id, firstname, lastname, address, country, telephone, validated, birthdate) SELECT id, user_id, firstname, lastname, address, country, telephone, validated, birthdate FROM __temp__owner');
        $this->addSql('DROP TABLE __temp__owner');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF60E67CA76ED395 ON owner (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D6497E3C61F9');
        $this->addSql('DROP INDEX UNIQ_8D93D649D4D57CD');
        $this->addSql('DROP INDEX UNIQ_8D93D64919EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, owner_id, staff_id, client_id, email, roles, password, email_verified FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, staff_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL COLLATE BINARY, roles CLOB NOT NULL COLLATE BINARY --(DC2Type:json)
        , password VARCHAR(255) NOT NULL COLLATE BINARY, email_verified DATETIME DEFAULT NULL, CONSTRAINT FK_8D93D6497E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8D93D649D4D57CD FOREIGN KEY (staff_id) REFERENCES staff (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8D93D64919EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user (id, owner_id, staff_id, client_id, email, roles, password, email_verified) SELECT id, owner_id, staff_id, client_id, email, roles, password, email_verified FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E3C61F9 ON user (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D4D57CD ON user (staff_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
        $this->addSql('DROP INDEX UNIQ_426EF392A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT id, user_id, firstname, lastname, title FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL COLLATE BINARY, lastname VARCHAR(255) NOT NULL COLLATE BINARY, title VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_426EF392A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO staff (id, user_id, firstname, lastname, title) SELECT id, user_id, firstname, lastname, title FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392A76ED395 ON staff (user_id)');
        $this->addSql('DROP INDEX UNIQ_F62F1763DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__region AS SELECT id, image_id, name, presentation, country FROM region');
        $this->addSql('DROP TABLE region');
        $this->addSql('CREATE TABLE region (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, presentation CLOB DEFAULT NULL COLLATE BINARY, country VARCHAR(2) DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_F62F1763DA5256D FOREIGN KEY (image_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO region (id, image_id, name, presentation, country) SELECT id, image_id, name, presentation, country FROM __temp__region');
        $this->addSql('DROP TABLE __temp__region');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F1763DA5256D ON region (image_id)');
        $this->addSql('DROP INDEX IDX_729F519B7E3C61F9');
        $this->addSql('DROP INDEX UNIQ_729F519B3DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room AS SELECT id, owner_id, image_id, summary, description, capacity, superficy, price, address FROM room');
        $this->addSql('DROP TABLE room');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, image_id INTEGER DEFAULT NULL, summary CLOB NOT NULL COLLATE BINARY, description CLOB DEFAULT NULL COLLATE BINARY, capacity INTEGER NOT NULL, superficy INTEGER NOT NULL, price INTEGER NOT NULL, address CLOB DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_729F519B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_729F519B3DA5256D FOREIGN KEY (image_id) REFERENCES document (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO room (id, owner_id, image_id, summary, description, capacity, superficy, price, address) SELECT id, owner_id, image_id, summary, description, capacity, superficy, price, address FROM __temp__room');
        $this->addSql('DROP TABLE __temp__room');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B3DA5256D ON room (image_id)');
        $this->addSql('DROP INDEX IDX_4E2C37B754177093');
        $this->addSql('DROP INDEX IDX_4E2C37B798260155');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room_region AS SELECT room_id, region_id FROM room_region');
        $this->addSql('DROP TABLE room_region');
        $this->addSql('CREATE TABLE room_region (room_id INTEGER NOT NULL, region_id INTEGER NOT NULL, PRIMARY KEY(room_id, region_id), CONSTRAINT FK_4E2C37B754177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4E2C37B798260155 FOREIGN KEY (region_id) REFERENCES region (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO room_region (room_id, region_id) SELECT room_id, region_id FROM __temp__room_region');
        $this->addSql('DROP TABLE __temp__room_region');
        $this->addSql('CREATE INDEX IDX_4E2C37B754177093 ON room_region (room_id)');
        $this->addSql('CREATE INDEX IDX_4E2C37B798260155 ON room_region (region_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX UNIQ_C7440455A76ED395');
        $this->addSql('DROP INDEX IDX_C74404553DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__client AS SELECT id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation FROM client');
        $this->addSql('DROP TABLE client');
        $this->addSql('CREATE TABLE client (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, birthdate DATE NOT NULL, country VARCHAR(2) DEFAULT NULL, presentation VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO client (id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation) SELECT id, user_id, firstname, lastname, telephone, address, birthdate, country, presentation FROM __temp__client');
        $this->addSql('DROP TABLE __temp__client');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C7440455A76ED395 ON client (user_id)');
        $this->addSql('DROP INDEX IDX_9474526CB83297E7');
        $this->addSql('DROP INDEX IDX_9474526C54177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__comment AS SELECT id, reservation_id, room_id, comment, date, rating, accepted FROM comment');
        $this->addSql('DROP TABLE comment');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER NOT NULL, room_id INTEGER NOT NULL, comment VARCHAR(255) NOT NULL, date DATETIME NOT NULL, rating INTEGER NOT NULL, accepted BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO comment (id, reservation_id, room_id, comment, date, rating, accepted) SELECT id, reservation_id, room_id, comment, date, rating, accepted FROM __temp__comment');
        $this->addSql('DROP TABLE __temp__comment');
        $this->addSql('CREATE INDEX IDX_9474526CB83297E7 ON comment (reservation_id)');
        $this->addSql('CREATE INDEX IDX_9474526C54177093 ON comment (room_id)');
        $this->addSql('DROP INDEX UNIQ_CF60E67CA76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__owner AS SELECT id, user_id, firstname, lastname, address, country, telephone, validated, birthdate FROM owner');
        $this->addSql('DROP TABLE owner');
        $this->addSql('CREATE TABLE owner (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, address CLOB DEFAULT NULL, country VARCHAR(2) NOT NULL, telephone VARCHAR(255) NOT NULL, validated BOOLEAN NOT NULL, birthdate DATETIME NOT NULL)');
        $this->addSql('INSERT INTO owner (id, user_id, firstname, lastname, address, country, telephone, validated, birthdate) SELECT id, user_id, firstname, lastname, address, country, telephone, validated, birthdate FROM __temp__owner');
        $this->addSql('DROP TABLE __temp__owner');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF60E67CA76ED395 ON owner (user_id)');
        $this->addSql('DROP INDEX UNIQ_F62F1763DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__region AS SELECT id, image_id, name, presentation, country FROM region');
        $this->addSql('DROP TABLE region');
        $this->addSql('CREATE TABLE region (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, image_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, presentation CLOB DEFAULT NULL, country VARCHAR(2) DEFAULT NULL)');
        $this->addSql('INSERT INTO region (id, image_id, name, presentation, country) SELECT id, image_id, name, presentation, country FROM __temp__region');
        $this->addSql('DROP TABLE __temp__region');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F1763DA5256D ON region (image_id)');
        $this->addSql('DROP INDEX IDX_42C8495519EB6921');
        $this->addSql('DROP INDEX IDX_42C8495554177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, client_id, room_id, start, until, validated, message FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_id INTEGER NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, validated BOOLEAN NOT NULL, message VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO reservation (id, client_id, room_id, start, until, validated, message) SELECT id, client_id, room_id, start, until, validated, message FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C8495519EB6921 ON reservation (client_id)');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('DROP INDEX IDX_8FB54DCEB83297E7');
        $this->addSql('DROP INDEX IDX_8FB54DCE19EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation_client AS SELECT reservation_id, client_id FROM reservation_client');
        $this->addSql('DROP TABLE reservation_client');
        $this->addSql('CREATE TABLE reservation_client (reservation_id INTEGER NOT NULL, client_id INTEGER NOT NULL, PRIMARY KEY(reservation_id, client_id))');
        $this->addSql('INSERT INTO reservation_client (reservation_id, client_id) SELECT reservation_id, client_id FROM __temp__reservation_client');
        $this->addSql('DROP TABLE __temp__reservation_client');
        $this->addSql('CREATE INDEX IDX_8FB54DCEB83297E7 ON reservation_client (reservation_id)');
        $this->addSql('CREATE INDEX IDX_8FB54DCE19EB6921 ON reservation_client (client_id)');
        $this->addSql('DROP INDEX IDX_729F519B7E3C61F9');
        $this->addSql('DROP INDEX UNIQ_729F519B3DA5256D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room AS SELECT id, owner_id, image_id, summary, description, capacity, superficy, price, address FROM room');
        $this->addSql('DROP TABLE room');
        $this->addSql('CREATE TABLE room (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, image_id INTEGER DEFAULT NULL, summary CLOB NOT NULL, description CLOB DEFAULT NULL, capacity INTEGER NOT NULL, superficy INTEGER NOT NULL, price INTEGER NOT NULL, address CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO room (id, owner_id, image_id, summary, description, capacity, superficy, price, address) SELECT id, owner_id, image_id, summary, description, capacity, superficy, price, address FROM __temp__room');
        $this->addSql('DROP TABLE __temp__room');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B3DA5256D ON room (image_id)');
        $this->addSql('DROP INDEX IDX_4E2C37B754177093');
        $this->addSql('DROP INDEX IDX_4E2C37B798260155');
        $this->addSql('CREATE TEMPORARY TABLE __temp__room_region AS SELECT room_id, region_id FROM room_region');
        $this->addSql('DROP TABLE room_region');
        $this->addSql('CREATE TABLE room_region (room_id INTEGER NOT NULL, region_id INTEGER NOT NULL, PRIMARY KEY(room_id, region_id))');
        $this->addSql('INSERT INTO room_region (room_id, region_id) SELECT room_id, region_id FROM __temp__room_region');
        $this->addSql('DROP TABLE __temp__room_region');
        $this->addSql('CREATE INDEX IDX_4E2C37B754177093 ON room_region (room_id)');
        $this->addSql('CREATE INDEX IDX_4E2C37B798260155 ON room_region (region_id)');
        $this->addSql('DROP INDEX UNIQ_426EF392A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__staff AS SELECT id, user_id, firstname, lastname, title FROM staff');
        $this->addSql('DROP TABLE staff');
        $this->addSql('CREATE TABLE staff (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO staff (id, user_id, firstname, lastname, title) SELECT id, user_id, firstname, lastname, title FROM __temp__staff');
        $this->addSql('DROP TABLE __temp__staff');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_426EF392A76ED395 ON staff (user_id)');
        $this->addSql('DROP INDEX IDX_B9D87FBB54177093');
        $this->addSql('CREATE TEMPORARY TABLE __temp__unavailable_period AS SELECT id, room_id, start, until, description FROM unavailable_period');
        $this->addSql('DROP TABLE unavailable_period');
        $this->addSql('CREATE TABLE unavailable_period (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, room_id INTEGER NOT NULL, start DATETIME NOT NULL, until DATETIME NOT NULL, description VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO unavailable_period (id, room_id, start, until, description) SELECT id, room_id, start, until, description FROM __temp__unavailable_period');
        $this->addSql('DROP TABLE __temp__unavailable_period');
        $this->addSql('CREATE INDEX IDX_B9D87FBB54177093 ON unavailable_period (room_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D6497E3C61F9');
        $this->addSql('DROP INDEX UNIQ_8D93D649D4D57CD');
        $this->addSql('DROP INDEX UNIQ_8D93D64919EB6921');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, owner_id, staff_id, client_id, email, roles, password, email_verified FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER DEFAULT NULL, staff_id INTEGER DEFAULT NULL, client_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, email_verified DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, owner_id, staff_id, client_id, email, roles, password, email_verified) SELECT id, owner_id, staff_id, client_id, email, roles, password, email_verified FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497E3C61F9 ON user (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D4D57CD ON user (staff_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64919EB6921 ON user (client_id)');
    }
}
