<?php

namespace FlyCash;

class Logger
{
    private static ?\Monolog\Logger $instance = null;

    public static function instance(): \Monolog\Logger
    {
        if (self::$instance === null) {
            if (isset($GLOBALS['logger']) && $GLOBALS['logger'] instanceof \Monolog\Logger) {
                self::$instance = $GLOBALS['logger'];
            } else {
                throw new \RuntimeException('Monolog logger not initialized. Ensure bootstrap.php is loaded.');
            }
        }
        return self::$instance;
    }

    /** @param array<string, mixed> $context */
    public static function warning(string $message, array $context = []): void
    {
        self::instance()->warning($message, $context);
    }

    /** @param array<string, mixed> $context */
    public static function error(string $message, array $context = []): void
    {
        self::instance()->error($message, $context);
    }

    /** @param array<string, mixed> $context */
    public static function info(string $message, array $context = []): void
    {
        self::instance()->info($message, $context);
    }

    /** @param array<string, mixed> $context */
    public static function debug(string $message, array $context = []): void
    {
        self::instance()->debug($message, $context);
    }

    /** @param array<string, mixed> $context */
    public static function log($level, string $message, array $context = []): void
    {
        self::instance()->log($level, $message, $context);
    }

    /** @param array<int, mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): void
    {
        $logger = self::instance();
        $logger->$name(...$arguments);
    }
}
