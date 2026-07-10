<?php

use Phinx\Migration\AbstractMigration;

class AddPostbackUniqueKey extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('postback_log');

        // Remove old non-unique index on transaction_id
        if ($table->hasIndexByName('idx_postback_tx')) {
            $table->removeIndexByName('idx_postback_tx')->save();
        }

        // Remove old composite index if exists
        if ($table->hasIndexByName('idx_provider_created')) {
            $table->removeIndexByName('idx_provider_created')->save();
        }
        if ($table->hasIndexByName('idx_postback_provider')) {
            $table->removeIndexByName('idx_postback_provider')->save();
        }

        // Add unique key on (provider, transaction_id)
        // MySQL allows multiple NULL transaction_id values under a unique key
        $table->addIndex(['provider', 'transaction_id'], [
            'name' => 'uq_provider_tx',
            'unique' => true,
        ])->save();
    }
}
