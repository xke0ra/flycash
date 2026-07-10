<?php

use Phinx\Migration\AbstractMigration;

class UnifyRequestsCompleted extends AbstractMigration
{
    public function up()
    {
        // Create unified redemptions table
        $table = $this->table('redemptions', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb3',
        ]);

        $table->addColumn('id', 'integer', ['signed' => false, 'identity' => true])
              ->addColumn('user_id', 'integer', ['signed' => false, 'null' => true])
              ->addColumn('request_from', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('dev_name', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('dev_man', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('gift_name', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('req_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
              ->addColumn('points_used', 'integer', ['default' => 0])
              ->addColumn('date', 'integer', ['signed' => false, 'default' => 0])
              ->addColumn('status', 'enum', ['values' => ['pending', 'processing', 'completed', 'rejected', 'cancelled'], 'default' => 'pending'])
              ->addColumn('username', 'string', ['limit' => 64, 'default' => ''])
              ->addColumn('note', 'text', ['null' => true])
              ->addColumn('created_at', 'integer', ['signed' => false, 'default' => 0])
              ->addColumn('updated_at', 'integer', ['signed' => false, 'default' => 0])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addIndex(['user_id'])
              ->addIndex(['status'])
              ->addIndex(['user_id', 'status'])
              ->save();

        // Migrate Requests — pending (0), processing (2), rejected (3)
        $this->execute("
            INSERT INTO redemptions (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username, created_at, updated_at)
            SELECT r.user_id, r.request_from, r.dev_name, r.dev_man, r.gift_name, r.req_amount, r.points_used, r.date,
                   CASE r.status
                       WHEN 0 THEN 'pending'
                       WHEN 2 THEN 'processing'
                       WHEN 3 THEN 'rejected'
                       ELSE 'pending'
                   END,
                   r.username, r.date, UNIX_TIMESTAMP()
            FROM Requests r
        ");

        // Migrate Completed — all status 1 becomes 'completed'
        $this->execute("
            INSERT INTO redemptions (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username, created_at, updated_at)
            SELECT c.user_id, c.request_from, c.dev_name, c.dev_man, c.gift_name, c.req_amount, c.points_used, c.date,
                   'completed',
                   c.username, c.date, UNIX_TIMESTAMP()
            FROM Completed c
        ");

        // Drop old tables
        $this->table('Requests')->drop()->save();
        $this->table('Completed')->drop()->save();
    }

    public function down()
    {
        // Recreate Requests table
        $table = $this->table('Requests', [
            'id' => false,
            'primary_key' => ['rid'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb3',
        ]);
        $table->addColumn('rid', 'integer', ['signed' => false, 'identity' => true])
              ->addColumn('user_id', 'integer', ['signed' => false, 'null' => true])
              ->addColumn('request_from', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('dev_name', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('dev_man', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('gift_name', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('req_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
              ->addColumn('points_used', 'integer', ['default' => 0])
              ->addColumn('date', 'integer', ['signed' => false, 'default' => 0])
              ->addColumn('status', 'integer', ['default' => 0])
              ->addColumn('username', 'string', ['limit' => 64, 'default' => ''])
              ->addColumn('note', 'text', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addIndex(['username'])
              ->addIndex(['status'])
              ->addIndex(['username', 'status'])
              ->save();

        // Recreate Completed table
        $table = $this->table('Completed', [
            'id' => false,
            'primary_key' => ['rid'],
            'engine' => 'InnoDB',
            'encoding' => 'utf8mb3',
        ]);
        $table->addColumn('rid', 'integer', ['signed' => false, 'identity' => true])
              ->addColumn('user_id', 'integer', ['signed' => false, 'null' => true])
              ->addColumn('request_from', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('dev_name', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('dev_man', 'string', ['limit' => 128, 'null' => true])
              ->addColumn('gift_name', 'string', ['limit' => 128, 'default' => ''])
              ->addColumn('req_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => '0.00'])
              ->addColumn('points_used', 'integer', ['default' => 0])
              ->addColumn('date', 'integer', ['signed' => false, 'default' => 0])
              ->addColumn('status', 'integer', ['default' => 1])
              ->addColumn('username', 'string', ['limit' => 64, 'default' => ''])
              ->addColumn('note', 'text', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addIndex(['username'])
              ->addIndex(['status'])
              ->save();

        // Restore data from redemptions
        $this->execute("
            INSERT INTO Requests (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username, note)
            SELECT user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date,
                   CASE status
                       WHEN 'pending' THEN 0
                       WHEN 'processing' THEN 2
                       WHEN 'rejected' THEN 3
                       ELSE 0
                   END,
                   username, note
            FROM redemptions WHERE status IN ('pending', 'processing', 'rejected')
        ");

        $this->execute("
            INSERT INTO Completed (user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, status, username, note)
            SELECT user_id, request_from, dev_name, dev_man, gift_name, req_amount, points_used, date, 1, username, note
            FROM redemptions WHERE status = 'completed'
        ");

        $this->table('redemptions')->drop()->save();
    }
}
