<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150309105024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Grade (id INT AUTO_INCREMENT NOT NULL, correction_id INT DEFAULT NULL, score INT NOT NULL, markingSchemeGrade_id INT DEFAULT NULL, INDEX IDX_989B813094AE086B (correction_id), INDEX IDX_989B8130B37839B3 (markingSchemeGrade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Grade ADD CONSTRAINT FK_989B813094AE086B FOREIGN KEY (correction_id) REFERENCES Correction (id)');
        $this->addSql('ALTER TABLE Grade ADD CONSTRAINT FK_989B8130B37839B3 FOREIGN KEY (markingSchemeGrade_id) REFERENCES MarkingSchemeSectionGrade (id)');
        $this->addSql('ALTER TABLE Correction DROP result');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE Grade');
        $this->addSql('ALTER TABLE Correction ADD result LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
