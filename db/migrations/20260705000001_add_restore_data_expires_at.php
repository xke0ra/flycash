<?php

use Phinx\Migration\AbstractMigration;

class AddRestoreDataExpiresAt extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('restore_data');

        if (!$table->hasColumn('expiresAt')) {
            $table->addColumn('expiresAt', 'integer', [
                'signed' => false,
                'default' => 0,
                'after' => 'removeAt',
            ])->save();
        }
    }
}
