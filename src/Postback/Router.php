<?php

namespace FlyCash\Postback;

use FlyCash\Logger;
use PDO;

class Router
{
    private PDO $db;

    /** @var array<string, class-string> */
    private static array $map = [
        'adgatemedia' => Handlers\AdGateMediaHandler::class,
        'adgem'       => Handlers\AdGemHandler::class,
        'admantum'    => Handlers\AdMantumHandler::class,
        'adscendmedia' => Handlers\AdScendMediaHandler::class,
        'cpalead'     => Handlers\CpaLeadHandler::class,
        'kiwiwall'    => Handlers\KiwiWallHandler::class,
        'offertoro'   => Handlers\OfferToroHandler::class,
        'ogads'       => Handlers\OgAdsHandler::class,
        'osa'         => Handlers\OgAdsHandler::class,
        'wannads'     => Handlers\WannadsHandler::class,
    ];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function dispatch(string $provider): void
    {
        $provider = strtolower($provider);

        if (!isset(self::$map[$provider])) {
            Logger::warning('Unknown postback provider', ['provider' => $provider]);
            http_response_code(400);
            echo 'Unknown provider.';
            exit;
        }

        $class = self::$map[$provider];

        if (!class_exists($class)) {
            Logger::error('Postback handler class not found', ['class' => $class, 'provider' => $provider]);
            http_response_code(500);
            echo 'Handler not available.';
            exit;
        }

        $handler = new $class($this->db);
        $handler->handle();
    }

    public static function register(string $name, string $class): void
    {
        self::$map[strtolower($name)] = $class;
    }
}
