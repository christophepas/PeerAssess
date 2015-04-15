<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150308230152 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Candidate (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_293EB7E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Supervisor (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5C77B17BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, image_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, type SMALLINT NOT NULL, firstLogin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A64793DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidateHome (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, html TINYINT(1) NOT NULL, javascript TINYINT(1) NOT NULL, angular TINYINT(1) NOT NULL, node TINYINT(1) NOT NULL, php TINYINT(1) NOT NULL, symfony TINYINT(1) NOT NULL, ruby TINYINT(1) NOT NULL, ror TINYINT(1) NOT NULL, python TINYINT(1) NOT NULL, django TINYINT(1) NOT NULL, java TINYINT(1) NOT NULL, swift TINYINT(1) NOT NULL, objectiveC TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Feedback (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, status INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Correction (id INT AUTO_INCREMENT NOT NULL, result LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', evaluationSessionGiver_id INT DEFAULT NULL, evaluationSessionReceiver_id INT DEFAULT NULL, INDEX IDX_EDC0A268E8C3A781 (evaluationSessionGiver_id), INDEX IDX_EDC0A268B3AFFBFA (evaluationSessionReceiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE EvaluationSession (id INT AUTO_INCREMENT NOT NULL, candidate_id INT DEFAULT NULL, evaluation_id INT DEFAULT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, correctionStart DATETIME DEFAULT NULL, correctionEnd DATETIME DEFAULT NULL, toBeCorrected TINYINT(1) NOT NULL, scheduledDate DATETIME DEFAULT NULL, status SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, resultLink VARCHAR(255) DEFAULT NULL, sent TINYINT(1) NOT NULL, INDEX IDX_2160CC6A421F7B0 (candidate_id), INDEX IDX_2160CC6456C5646 (evaluation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Evaluation (id INT AUTO_INCREMENT NOT NULL, supervisor_id INT DEFAULT NULL, test_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_5C7EA6A5156BE243 (supervisor_id), INDEX IDX_5C7EA6A51E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, alt VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MarkingScheme (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, introduction LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MarkingSchemeSection (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, introduction LONGTEXT NOT NULL, markingScheme_id INT DEFAULT NULL, INDEX IDX_E476698440AD1254 (markingScheme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MarkingSchemeSectionGrade (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, markingSchemeSection_id INT DEFAULT NULL, INDEX IDX_F0BD0DE18FC17EF7 (markingSchemeSection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Test (id INT AUTO_INCREMENT NOT NULL, language INT NOT NULL, name VARCHAR(255) NOT NULL, shortDescription VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, readMe LONGTEXT NOT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Candidate ADD CONSTRAINT FK_293EB7E5A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE Supervisor ADD CONSTRAINT FK_5C77B17BA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A64793DA5256D FOREIGN KEY (image_id) REFERENCES Image (id)');
        $this->addSql('ALTER TABLE Correction ADD CONSTRAINT FK_EDC0A268E8C3A781 FOREIGN KEY (evaluationSessionGiver_id) REFERENCES EvaluationSession (id)');
        $this->addSql('ALTER TABLE Correction ADD CONSTRAINT FK_EDC0A268B3AFFBFA FOREIGN KEY (evaluationSessionReceiver_id) REFERENCES EvaluationSession (id)');
        $this->addSql('ALTER TABLE EvaluationSession ADD CONSTRAINT FK_2160CC6A421F7B0 FOREIGN KEY (candidate_id) REFERENCES Candidate (id)');
        $this->addSql('ALTER TABLE EvaluationSession ADD CONSTRAINT FK_2160CC6456C5646 FOREIGN KEY (evaluation_id) REFERENCES Evaluation (id)');
        $this->addSql('ALTER TABLE Evaluation ADD CONSTRAINT FK_5C7EA6A5156BE243 FOREIGN KEY (supervisor_id) REFERENCES Supervisor (id)');
        $this->addSql('ALTER TABLE Evaluation ADD CONSTRAINT FK_5C7EA6A51E5D0459 FOREIGN KEY (test_id) REFERENCES Test (id)');
        $this->addSql('ALTER TABLE MarkingSchemeSection ADD CONSTRAINT FK_E476698440AD1254 FOREIGN KEY (markingScheme_id) REFERENCES MarkingScheme (id)');
        $this->addSql('ALTER TABLE MarkingSchemeSectionGrade ADD CONSTRAINT FK_F0BD0DE18FC17EF7 FOREIGN KEY (markingSchemeSection_id) REFERENCES MarkingSchemeSection (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE EvaluationSession DROP FOREIGN KEY FK_2160CC6A421F7B0');
        $this->addSql('ALTER TABLE Evaluation DROP FOREIGN KEY FK_5C7EA6A5156BE243');
        $this->addSql('ALTER TABLE Candidate DROP FOREIGN KEY FK_293EB7E5A76ED395');
        $this->addSql('ALTER TABLE Supervisor DROP FOREIGN KEY FK_5C77B17BA76ED395');
        $this->addSql('ALTER TABLE Correction DROP FOREIGN KEY FK_EDC0A268E8C3A781');
        $this->addSql('ALTER TABLE Correction DROP FOREIGN KEY FK_EDC0A268B3AFFBFA');
        $this->addSql('ALTER TABLE EvaluationSession DROP FOREIGN KEY FK_2160CC6456C5646');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A64793DA5256D');
        $this->addSql('ALTER TABLE MarkingSchemeSection DROP FOREIGN KEY FK_E476698440AD1254');
        $this->addSql('ALTER TABLE MarkingSchemeSectionGrade DROP FOREIGN KEY FK_F0BD0DE18FC17EF7');
        $this->addSql('ALTER TABLE Evaluation DROP FOREIGN KEY FK_5C7EA6A51E5D0459');
        $this->addSql('DROP TABLE Candidate');
        $this->addSql('DROP TABLE Supervisor');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE candidateHome');
        $this->addSql('DROP TABLE Feedback');
        $this->addSql('DROP TABLE Correction');
        $this->addSql('DROP TABLE EvaluationSession');
        $this->addSql('DROP TABLE Evaluation');
        $this->addSql('DROP TABLE Image');
        $this->addSql('DROP TABLE MarkingScheme');
        $this->addSql('DROP TABLE MarkingSchemeSection');
        $this->addSql('DROP TABLE MarkingSchemeSectionGrade');
        $this->addSql('DROP TABLE Test');
    }
}
