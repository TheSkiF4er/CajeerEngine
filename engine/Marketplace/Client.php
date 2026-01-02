<?php
declare(strict_types=1);

namespace Marketplace;

/**
 * Lightweight Marketplace HTTP client.
 *
 * Config is loaded from `system/marketplace.php` by default and can be overridden
 * by passing an explicit config array.
 */
final class Client
{
    private array $cfg;

    /**
     * @param array|null $cfg Marketplace configuration (at least 'base_url').
     */
    public function __construct(?array $cfg = null)
    {
        $this->cfg = $cfg ?? self::loadConfig();
    }

    private static function loadConfig(): array
    {
        $cfg = [];

        if (\defined('ROOT_PATH')) {
            $file = rtrim((string)ROOT_PATH, '/').'/system/marketplace.php';
            if (is_file($file)) {
                $loaded = require $file;
                if (is_array($loaded)) {
                    $cfg = $loaded;
                }
            }
        }

        // ENV override (optional)
        $envBase = getenv('CE_MARKETPLACE_BASE_URL');
        if (is_string($envBase) && $envBase !== '') {
            $cfg['base_url'] = $envBase;
        }

        if (!isset($cfg['base_url']) || !is_string($cfg['base_url']) || $cfg['base_url'] === '') {
            // Safe default (matches skeleton config)
            $cfg['base_url'] = 'https://marketplace.cajeer.ru/api/v1';
        }

        if (!isset($cfg['timeout']) || !is_int($cfg['timeout'])) {
            $cfg['timeout'] = 10;
        }

        return $cfg;
    }

    private function url(string $path): string
    {
        return rtrim((string)$this->cfg['base_url'], '/') . '/' . ltrim($path, '/');
    }

    /**
     * Fetch JSON from Marketplace. Never throws on network/JSON errors; returns ok=false instead.
     */
    public function fetchJson(string $path): array
    {
        $url = $this->url($path);

        $timeout = (int)($this->cfg['timeout'] ?? 10);
        $ctx = stream_context_create([
            'http' => [
                'timeout' => $timeout,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\n",
            ],
        ]);

        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            return ['ok' => false, 'error' => 'fetch_failed', 'url' => $url];
        }

        $arr = json_decode($raw, true);
        if (!is_array($arr)) {
            return ['ok' => false, 'error' => 'invalid_json', 'url' => $url, 'raw' => mb_substr((string)$raw, 0, 8000)];
        }

        // If upstream does not provide ok field, treat as ok.
        if (!array_key_exists('ok', $arr)) {
            $arr['ok'] = true;
        }

        return $arr;
    }

    // Admin helpers used by Modules\admin\Controllers\MarketplaceController
    public function status(): array
    {
        return $this->fetchJson('status');
    }

    public function listThemes(): array
    {
        return $this->fetchJson('themes');
    }

    public function listPlugins(): array
    {
        return $this->fetchJson('plugins');
    }

    // Generic helpers (optional)
    public function index(): array
    {
        return $this->fetchJson('index');
    }
}