<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327083858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094F3EA110D0');
        $this->addSql('CREATE TABLE cluster (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, auth_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE cluser');
        $this->addSql('DROP INDEX IDX_4FBF094F3EA110D0 ON company');
        $this->addSql('ALTER TABLE company CHANGE cluser_id cluster_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC36A3328 FOREIGN KEY (cluster_id) REFERENCES cluster (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094FC36A3328 ON company (cluster_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC36A3328');
        $this->addSql('CREATE TABLE cluser (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, auth_code VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE cluster');
        $this->addSql('DROP INDEX IDX_4FBF094FC36A3328 ON company');
        $this->addSql('ALTER TABLE company CHANGE cluster_id cluser_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F3EA110D0 FOREIGN KEY (cluser_id) REFERENCES cluser (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4FBF094F3EA110D0 ON company (cluser_id)');
    }
}
