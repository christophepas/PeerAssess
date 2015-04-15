<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325011721 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE EvaluationInvite DROP FOREIGN KEY FK_98BEFFF1156BE243');
        $this->addSql('DROP INDEX IDX_98BEFFF1156BE243 ON EvaluationInvite');
        $this->addSql('ALTER TABLE EvaluationInvite DROP supervisor_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE EvaluationInvite ADD supervisor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE EvaluationInvite ADD CONSTRAINT FK_98BEFFF1156BE243 FOREIGN KEY (supervisor_id) REFERENCES Supervisor (id)');
        $this->addSql('CREATE INDEX IDX_98BEFFF1156BE243 ON EvaluationInvite (supervisor_id)');
    }
}
