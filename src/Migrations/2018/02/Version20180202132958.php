<?php declare(strict_types=1);

namespace DoctrineMigrations;

use App\Database\Migration\ContainerAwareMigration;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180202132958 extends AbstractMigration implements ContainerAwareInterface
{

  use ContainerAwareMigration;

  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
   */
  public function up(Schema $schema)
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->abortIf(true, "This migration can not be completed, as the correct version is not installed. " .
        "Please revert to 75ff0f0, and rerun the migrations up until that point, and then resume the upgrade.");
  }


  /**
   * @param Schema $schema
   *
   * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
   */
  public function postUp(Schema $schema)
  {
    $this->abortIf(true, "This migration can not be completed, as the correct version is not installed. " .
        "Please revert to 75ff0f0, and rerun the migrations up until that point, and then resume the upgrade.");

    // Implementation removed due to code change
  }

  public function down(Schema $schema)
  {
    // this down() migration is auto-generated, please modify it to your needs
  }
}
