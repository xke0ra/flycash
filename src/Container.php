<?php

namespace FlyCash;

use League\Container\Container as LeagueContainer;
use PDO;
use FlyCash\Services\AnalyticsService;
use FlyCash\Services\AuthService;
use FlyCash\Services\ConfigService;
use FlyCash\Services\EmailVerificationService;
use FlyCash\Services\NotificationService;
use FlyCash\Services\OfferwallService;
use FlyCash\Services\PasswordService;
use FlyCash\Services\PointsService;
use FlyCash\Services\ProfileService;
use FlyCash\Services\SecurityService;
use FlyCash\Services\UserService;

class Container
{
    private static ?LeagueContainer $container = null;

    private static function instance(): LeagueContainer
    {
        if (self::$container === null) {
            self::$container = new LeagueContainer();
            self::registerServices();
        }
        return self::$container;
    }

    public static function get(string $id): object
    {
        return self::instance()->get($id);
    }

    public static function set(string $id, object $service): void
    {
        self::instance()->add($id, $service)->setShared(true);
    }

    public static function has(string $id): bool
    {
        return self::instance()->has($id);
    }

    public static function reset(): void
    {
        self::$container = null;
    }

    private static function registerServices(): void
    {
        $c = self::$container;

        $c->add(PDO::class, function () {
            if (isset($GLOBALS['dbo']) && $GLOBALS['dbo'] instanceof PDO) {
                return $GLOBALS['dbo'];
            }
            $host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $name = defined('DB_NAME') ? DB_NAME : 'pocket_db';
            $user = defined('DB_USER') ? DB_USER : 'root';
            $pass = defined('DB_PASS') ? DB_PASS : '';
            return new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8",
                $user, $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        })->setShared(true);

        $c->add('db', fn () => $c->get(PDO::class))->setShared(true);
        $c->add(ConfigService::class)->setShared(true);
        $c->add(PointsService::class)->setShared(true);
        $c->add(SecurityService::class)->setShared(true);
        $c->add(UserService::class)->setShared(true);
        $c->add(AuthService::class)->setShared(true);
        $c->add(ProfileService::class)->setShared(true);
        $c->add(EmailVerificationService::class)->setShared(true);
        $c->add(NotificationService::class)->setShared(true);
        $c->add(OfferwallService::class)->setShared(true);
        $c->add(AnalyticsService::class)->setShared(true);
        $c->add(PasswordService::class)->setShared(true);
    }
}
