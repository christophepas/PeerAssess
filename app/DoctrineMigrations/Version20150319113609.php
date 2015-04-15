<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150319113609 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE EvaluationSession ADD invite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE EvaluationSession ADD CONSTRAINT FK_2160CC6EA417747 FOREIGN KEY (invite_id) REFERENCES EvaluationInvite (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2160CC6EA417747 ON EvaluationSession (invite_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE EvaluationSession DROP FOREIGN KEY FK_2160CC6EA417747');
        $this->addSql('DROP INDEX UNIQ_2160CC6EA417747 ON EvaluationSession');
        $this->addSql('ALTER TABLE EvaluationSession DROP invite_id');
    }
}
