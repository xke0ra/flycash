<?php

use Phinx\Migration\AbstractMigration;

class AddOfferwallsPositionIndex extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('offerwalls');
        if (!$table->hasIndexByName('idx_offerwalls_position')) {
            $table->addIndex(['position'], ['name' => 'idx_offerwalls_position'])->save();
        }

        // Also ensure tracker has a compound index on (user_id, date) for OgAds daily cap queries
        $table = $this->table('tracker');
        if (!$table->hasIndexByName('idx_tracker_user_date')) {
            $table->addIndex(['user_id', 'date'], ['name' => 'idx_tracker_user_date'])->save();
        }
    }
}
