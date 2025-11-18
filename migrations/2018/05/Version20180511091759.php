<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180511091759 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('ALTER TABLE study_area CHANGE owner_user_id owner_user_id INT NOT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('ALTER TABLE study_area DROP FOREIGN KEY FK_A84CD5512B18554A');
    $this->addSql('ALTER TABLE study_area CHANGE owner_user_id owner_user_id INT DEFAULT NULL');
    $this->addSql('ALTER TABLE study_area ADD CONSTRAINT FK_A84CD5512B18554A FOREIGN KEY (owner_user_id) REFERENCES user__table (id)');
  }

  public function isTransactional(): bool
  {
    return false;
  }
}
