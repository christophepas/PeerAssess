<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150329012341 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Correction CHANGE evaluationSessionGiver_id evaluationSessionGiver_id INT NOT NULL, CHANGE evaluationSessionReceiver_id evaluationSessionReceiver_id INT NOT NULL');
        $this->addSql('ALTER TABLE EvaluationSession CHANGE evaluation_id evaluation_id INT NOT NULL, CHANGE candidate_id candidate_id INT NOT NULL');
        $this->addSql('ALTER TABLE Evaluation CHANGE supervisor_id supervisor_id INT NOT NULL, CHANGE test_id test_id INT NOT NULL');
        $this->addSql('ALTER TABLE EvaluationInvite CHANGE evaluation_id evaluation_id INT NOT NULL');
        $this->addSql('ALTER TABLE Grade CHANGE correction_id correction_id INT NOT NULL, CHANGE markingSchemeGrade_id markingSchemeGrade_id INT NOT NULL');
        $this->addSql('ALTER TABLE MarkingScheme CHANGE test_id test_id INT NOT NULL');
        $this->addSql('ALTER TABLE MarkingSchemeSection CHANGE markingScheme_id markingScheme_id INT NOT NULL');
        $this->addSql('ALTER TABLE MarkingSchemeSectionGrade CHANGE markingSchemeSection_id markingSchemeSection_id INT NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Correction CHANGE evaluationSessionGiver_id evaluationSessionGiver_id INT DEFAULT NULL, CHANGE evaluationSessionReceiver_id evaluationSessionReceiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Evaluation CHANGE supervisor_id supervisor_id INT DEFAULT NULL, CHANGE test_id test_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE EvaluationInvite CHANGE evaluation_id evaluation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE EvaluationSession CHANGE candidate_id candidate_id INT DEFAULT NULL, CHANGE evaluation_id evaluation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Grade CHANGE correction_id correction_id INT DEFAULT NULL, CHANGE markingSchemeGrade_id markingSchemeGrade_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MarkingSchemeSection CHANGE markingScheme_id markingScheme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MarkingSchemeSectionGrade CHANGE markingSchemeSection_id markingSchemeSection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user ADD candidate_id INT NOT NULL, ADD supervisor_id INT NOT NULL');
        $this->addSql('ALTER TABLE MarkingScheme CHANGE test_id test_id INT DEFAULT NULL');
    }
}
