<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ConvertMyIsamToInnoDb extends AbstractMigration
{
    public function up(): void
    {
        $this->execute("ALTER TABLE youtube ENGINE=InnoDB");
        $this->execute("ALTER TABLE watched_video ENGINE=InnoDB");
    }

    public function down(): void
    {
        $this->execute("ALTER TABLE youtube ENGINE=MyISAM");
        $this->execute("ALTER TABLE watched_video ENGINE=MyISAM");
    }
}
