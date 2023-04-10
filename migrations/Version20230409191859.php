<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409191859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into organizer (username, roles, password) values ('ansgar', '[\"ROLE_USER\", \"ROLE_ORGANIZER\", \"ROLE_ALLOWED_TO_SWITCH\"]' ,'$2y$13$23WJ1ZnrNY4LPmkt474bROIKDV789l.hBabkdH5UITnIYmxOIUJDi');");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
