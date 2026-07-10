<?php

use Phinx\Migration\AbstractMigration;

class AddUserIdForeignKeys extends AbstractMigration
{
    public function change()
    {
        // --- 1. tracker ---
        $table = $this->table('tracker');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'id'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE tracker t JOIN users u ON t.username = u.login SET t.user_id = u.id");
        }

        // --- 2. Requests ---
        $table = $this->table('Requests');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'rid'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE Requests r JOIN users u ON r.username = u.login SET r.user_id = u.id");
        }

        // --- 3. Completed ---
        $table = $this->table('Completed');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'rid'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE Completed c JOIN users u ON c.username = u.login SET c.user_id = u.id");
        }

        // --- 4. notifications ---
        $table = $this->table('notifications');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'id'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE notifications n JOIN users u ON n.username = u.login SET n.user_id = u.id");
        }

        // --- 5. offer_status ---
        $table = $this->table('offer_status');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'id'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE offer_status o JOIN users u ON o.user = u.login SET o.user_id = u.id");
        }

        // --- 6. referers ---
        $table = $this->table('referers');
        if (!$table->hasColumn('user_id')) {
            $table->addColumn('user_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'id'])
                  ->addColumn('referer_id', 'integer', ['signed' => false, 'null' => true, 'after' => 'user_id'])
                  ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                  ->addForeignKey('referer_id', 'users', 'id', ['delete' => 'SET NULL', 'update' => 'CASCADE'])
                  ->save();
            $this->execute("UPDATE referers r JOIN users u ON r.username = u.login SET r.user_id = u.id");
            $this->execute("UPDATE referers r JOIN users u ON r.referer = u.login SET r.referer_id = u.id");
        }
    }
}
