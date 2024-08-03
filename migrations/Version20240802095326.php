<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802095326 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD cursus_id INT DEFAULT NULL, ADD lesson_id INT DEFAULT NULL, ADD certification_doc VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7540AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D7540AEF4B9 ON certification (cursus_id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75CDF80196 ON certification (lesson_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7540AEF4B9');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75CDF80196');
        $this->addSql('DROP INDEX IDX_6C3C6D7540AEF4B9 ON certification');
        $this->addSql('DROP INDEX IDX_6C3C6D75CDF80196 ON certification');
        $this->addSql('ALTER TABLE certification DROP cursus_id, DROP lesson_id, DROP certification_doc');
    }
}
