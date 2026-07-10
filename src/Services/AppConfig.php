<?php

namespace FlyCash\Services;

class AppConfig
{
    private static ?ConfigService $config = null;

    private static function config(): ConfigService
    {
        if (self::$config === null) {
            global $dbo;
            self::$config = new ConfigService($dbo);
        }
        return self::$config;
    }

    public static function setConfigService(ConfigService $service): void
    {
        self::$config = $service;
    }

    public static function getPointsRatio(): int
    {
        return self::config()->getInt('USER_RATIO', 1000);
    }

    public static function isDemoMode(): bool
    {
        return self::config()->getBool('APP_DEMO', false);
    }

    public static function getAppPath(): string
    {
        return self::config()->get('APP_PATH') ?? 'app';
    }

    public static function getSiteName(): string
    {
        return self::config()->get('SITE_NAME') ?? 'Pocket Rewards';
    }

    public static function getMinPayout(): int
    {
        return self::config()->getInt('MIN_PAYOUT', 5000);
    }

    public static function getCurrency(): string
    {
        return self::config()->get('CURRENCY') ?? 'Points';
    }

    public static function getRefererBonus(): int
    {
        return self::config()->getInt('REFERER_BONUS', 100);
    }

    public static function getCheckinBonus(): int
    {
        return self::config()->getInt('CHECKIN_BONUS', 10);
    }

    public static function getDailyCheckinLimit(): int
    {
        return self::config()->getInt('DAILY_CHECKIN_LIMIT', 1);
    }

    public static function getAdGemPublisherId(): string
    {
        return self::config()->get('AdGem_PubId') ?? '';
    }

    public static function getAdGateMediaWalletId(): string
    {
        return self::config()->get('AdGateMedia_WalletId') ?? '';
    }

    public static function getKiwiWallSecret(): string
    {
        return self::config()->get('KiwiWall_Secret') ?? '';
    }

    public static function getOfferToroSecret(): string
    {
        return self::config()->get('OfferToro_Secret') ?? '';
    }

    public static function getAdMantumPubId(): string
    {
        return self::config()->get('AdMantum_PubId') ?? '';
    }

    public static function getAdMantumAppId(): string
    {
        return self::config()->get('AdMantum_AppId') ?? '';
    }

    public static function getAdMantumSecretKey(): string
    {
        return self::config()->get('AdMantum_SecretKey') ?? '';
    }

    public static function getOgAdsAffiliateId(): string
    {
        return self::config()->get('OgAds_AffiliateId') ?? '';
    }

    public static function getOgAdsSource(): string
    {
        return self::config()->get('OgAds_Source') ?? '';
    }

    public static function getAdScendMediaPubId(): string
    {
        return self::config()->get('AdScendMedia_PubId') ?? '';
    }
}
