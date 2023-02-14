<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230214134338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, announcer_id INT NOT NULL, type VARCHAR(20) NOT NULL, departure_location VARCHAR(255) NOT NULL, arrival_location VARCHAR(255) NOT NULL, available_seats INT DEFAULT NULL, departure_time DATETIME NOT NULL, message VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, car_model VARCHAR(255) DEFAULT NULL, car_color VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7656F53B979B1AD6 (company_id), INDEX IDX_7656F53B3EC97830 (announcer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B3EC97830 FOREIGN KEY (announcer_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B979B1AD6');
        $this->addSql('ALTER TABLE trip DROP FOREIGN KEY FK_7656F53B3EC97830');
        $this->addSql('DROP TABLE trip');
    }
}
