<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150309100418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE MarkingScheme ADD test_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MarkingScheme ADD CONSTRAINT FK_BB5655461E5D0459 FOREIGN KEY (test_id) REFERENCES Test (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB5655461E5D0459 ON MarkingScheme (test_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE MarkingScheme DROP FOREIGN KEY FK_BB5655461E5D0459');
        $this->addSql('DROP INDEX UNIQ_BB5655461E5D0459 ON MarkingScheme');
        $this->addSql('ALTER TABLE MarkingScheme DROP test_id');
    }
}
