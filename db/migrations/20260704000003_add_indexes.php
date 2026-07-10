<?php

use Phinx\Migration\AbstractMigration;

class AddIndexes extends AbstractMigration
{
    public function change()
    {
        // Users
        $table = $this->table('users');
        if (!$table->hasIndexByName('idx_users_login')) {
            $table->addIndex(['login'], ['name' => 'idx_users_login'])->save();
        }
        if (!$table->hasIndexByName('idx_users_email')) {
            $table->addIndex(['email'], ['name' => 'idx_users_email'])->save();
        }
        if (!$table->hasIndexByName('idx_users_referer')) {
            $table->addIndex(['referer'], ['name' => 'idx_users_referer'])->save();
        }

        // Tracker
        $table = $this->table('tracker');
        if (!$table->hasIndexByName('idx_tracker_username')) {
            $table->addIndex(['username'], ['name' => 'idx_tracker_username'])->save();
        }
        if (!$table->hasIndexByName('idx_tracker_date')) {
            $table->addIndex(['date'], ['name' => 'idx_tracker_date'])->save();
        }
        if (!$table->hasIndexByName('idx_tracker_type')) {
            $table->addIndex(['type'], ['name' => 'idx_tracker_type'])->save();
        }
        if (!$table->hasIndexByName('idx_tracker_user_date')) {
            $table->addIndex(['username', 'date'], ['name' => 'idx_tracker_user_date'])->save();
        }
        if (!$table->hasIndexByName('idx_tracker_user_type')) {
            $table->addIndex(['username', 'type'], ['name' => 'idx_tracker_user_type'])->save();
        }

        // Requests
        $table = $this->table('Requests');
        if (!$table->hasIndexByName('idx_requests_username')) {
            $table->addIndex(['username'], ['name' => 'idx_requests_username'])->save();
        }
        if (!$table->hasIndexByName('idx_requests_status')) {
            $table->addIndex(['status'], ['name' => 'idx_requests_status'])->save();
        }
        if (!$table->hasIndexByName('idx_requests_user_status')) {
            $table->addIndex(['username', 'status'], ['name' => 'idx_requests_user_status'])->save();
        }

        // Completed
        $table = $this->table('Completed');
        if (!$table->hasIndexByName('idx_completed_username')) {
            $table->addIndex(['username'], ['name' => 'idx_completed_username'])->save();
        }

        // Notifications
        $table = $this->table('notifications');
        if (!$table->hasIndexByName('idx_notifications_username')) {
            $table->addIndex(['username'], ['name' => 'idx_notifications_username'])->save();
        }
        if (!$table->hasIndexByName('idx_notifications_read')) {
            $table->addIndex(['username', 'is_read'], ['name' => 'idx_notifications_read'])->save();
        }

        // Offer status
        $table = $this->table('offer_status');
        if (!$table->hasIndexByName('idx_offer_status_user_of')) {
            $table->addIndex(['user', 'of_id'], ['name' => 'idx_offer_status_user_of'])->save();
        }

        // Access data
        $table = $this->table('access_data');
        if (!$table->hasIndexByName('idx_access_account')) {
            $table->addIndex(['accountId'], ['name' => 'idx_access_account'])->save();
        }
        if (!$table->hasIndexByName('idx_access_token')) {
            $table->addIndex(['accessToken'], ['name' => 'idx_access_token'])->save();
        }

        // Failed attempts
        if ($this->hasTable('failed_attempts')) {
            $table = $this->table('failed_attempts');
            if (!$table->hasIndexByName('idx_failed_attempts_id')) {
                $table->addIndex(['identifier', 'action', 'attempt_time'], ['name' => 'idx_failed_attempts_id'])->save();
            }
        }

        // IP bans
        if ($this->hasTable('ip_ban')) {
            $table = $this->table('ip_ban');
            if (!$table->hasIndexByName('idx_ip_ban_ip')) {
                $table->addIndex(['ip'], ['name' => 'idx_ip_ban_ip'])->save();
            }
        }

        // Rate limits
        $table = $this->table('rate_limits');
        if (!$table->hasIndexByName('idx_rate_limits_id')) {
            $table->addIndex(['identifier', 'action', 'window_start'], ['name' => 'idx_rate_limits_id'])->save();
        }

        // Postback log
        $table = $this->table('postback_log');
        if (!$table->hasIndexByName('idx_postback_tx')) {
            $table->addIndex(['transaction_id'], ['name' => 'idx_postback_tx'])->save();
        }
        if (!$table->hasIndexByName('idx_postback_provider')) {
            $table->addIndex(['provider', 'created_at'], ['name' => 'idx_postback_provider'])->save();
        }
    }
}
