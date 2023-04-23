<?php

declare(strict_types=1);

namespace Plugin\jtl_landswitcher\Migrations;

use JTL\Plugin\Migration;
use JTL\Update\IMigration;

class Migration20230422155500 extends Migration implements IMigration
{
  public function up()
  {
    $this->execute("CREATE TABLE IF NOT EXISTS `jtl_test_redirect` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `tland_cISO` varchar(5) NOT NULL UNIQUE,
      `url` varchar(255) NOT NULL,
      `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB COLLATE utf8_unicode_ci");
  }

  public function down()
  {
    $this->execute("DROP TABLE IF EXISTS `jtl_test_table`");
  }
}
