<?php

use Phinx\Migration\AbstractMigration;

class CreatePostbackLog extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('postback_log', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8',
        ]);

        $table
            ->addColumn('id', 'integer', [
                'signed' => false,
                'identity' => true,
            ])
            ->addColumn('provider', 'string', ['limit' => 50])
            ->addColumn('transaction_id', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('user_id', 'string', ['limit' => 100])
            ->addColumn('amount', 'integer', ['signed' => false, 'default' => 0])
            ->addColumn('status', 'enum', ['values' => ['success', 'failed', 'skipped'], 'default' => 'success'])
            ->addColumn('ip_addr', 'string', ['limit' => 45, 'default' => ''])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['transaction_id'], ['name' => 'idx_tx'])
            ->addIndex(['provider', 'created_at'], ['name' => 'idx_provider_created'])
            ->create();
    }
}
