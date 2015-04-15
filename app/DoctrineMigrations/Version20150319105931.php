<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150319105931 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE EvaluationInvite (id INT AUTO_INCREMENT NOT NULL, evaluation_id INT DEFAULT NULL, supervisor_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, scheduledDate DATETIME NOT NULL, INDEX IDX_98BEFFF1456C5646 (evaluation_id), INDEX IDX_98BEFFF1156BE243 (supervisor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE EvaluationInvite ADD CONSTRAINT FK_98BEFFF1456C5646 FOREIGN KEY (evaluation_id) REFERENCES Evaluation (id)');
        $this->addSql('ALTER TABLE EvaluationInvite ADD CONSTRAINT FK_98BEFFF1156BE243 FOREIGN KEY (supervisor_id) REFERENCES Supervisor (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE EvaluationInvite');
    }
}
