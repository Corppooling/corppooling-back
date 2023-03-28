<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230328075041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD cluster_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC36A3328 FOREIGN KEY (cluster_id) REFERENCES cluster (id)');
        $this->addSql('CREATE INDEX IDX_4FBF094FC36A3328 ON company (cluster_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC36A3328');
        $this->addSql('DROP INDEX IDX_4FBF094FC36A3328 ON company');
        $this->addSql('ALTER TABLE company DROP cluster_id');
    }
}
