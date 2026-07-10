<?php

namespace Tests\Unit\Services;

use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->getPDO()->query("SELECT 1");
        } catch (\Throwable $e) {
            $this->markTestSkipped('Database not available');
        }
    }

    public function test_add_and_count_unread(): void
    {
        $service = new \FlyCash\Services\NotificationService($this->getPDO());
        $userId = $this->createTestUser('notiftest');

        $service->add($userId, 'Test Title', 'Test Message', 100, 'credit');
        $service->add($userId, 'Test Title 2', 'Test Message 2', 50, 'bonus');

        $count = $service->countUnread($userId);
        $this->assertSame(2, $count);

        $this->removeTestUser('notiftest');
    }

    public function test_get_unread_returns_notifications(): void
    {
        $service = new \FlyCash\Services\NotificationService($this->getPDO());
        $userId = $this->createTestUser('notiftest2');
        $service->add($userId, 'Title', 'Message', 10, 'credit');

        $unread = $service->getUnread($userId);
        $this->assertCount(1, $unread);
        $this->assertSame('Title', $unread[0]['title']);

        $this->removeTestUser('notiftest2');
    }

    public function test_mark_as_read(): void
    {
        $service = new \FlyCash\Services\NotificationService($this->getPDO());
        $userId = $this->createTestUser('notiftest3');

        $now = time();
        $sql = "INSERT INTO notifications (user_id, username, title, message, points, type, created_at) VALUES (:uid, '', :t, :m, :p, :ty, :c)";
        $this->getPDO()->prepare($sql)->execute([
            ':uid' => $userId, ':t' => 'Title', ':m' => 'Msg', ':p' => 0, ':ty' => 'credit', ':c' => $now,
        ]);
        $id = $this->getPDO()->lastInsertId();

        $this->assertTrue($service->markAsRead((int)$id, $userId));

        $stmt = $this->getPDO()->prepare("SELECT is_read FROM notifications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $this->assertSame(1, (int)$stmt->fetchColumn());

        $this->removeTestUser('notiftest3');
    }

    public function test_mark_all_as_read(): void
    {
        $service = new \FlyCash\Services\NotificationService($this->getPDO());
        $userId = $this->createTestUser('notiftest4');
        $service->add($userId, 'A', 'B', 0, 'credit');
        $service->add($userId, 'C', 'D', 0, 'credit');

        $this->assertTrue($service->markAllAsRead($userId));
        $this->assertSame(0, $service->countUnread($userId));

        $this->removeTestUser('notiftest4');
    }
}
