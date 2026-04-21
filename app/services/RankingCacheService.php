<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RankingCacheService
{
    const ACTIVE_KEYS = 'ranking_fo:active_keys';
    const TTL_MINUTES = 5;

    const LAST_UPDATED_KEY = 'ranking_fo:last_updated';

    public function getLastUpdated(): ?string
    {
        return Cache::get(self::LAST_UPDATED_KEY);
    }

    public function touchLastUpdated(): void
    {
        Cache::put(
            self::LAST_UPDATED_KEY,
            now()->format('H:i:s'),
            now()->addHours(24)
        );
    }

    public function remember(string $key, callable $callback): mixed
    {
        $activeKeys = Cache::get(self::ACTIVE_KEYS, []);
        if (! in_array($key, $activeKeys)) {
            $activeKeys[] = $key;
            Cache::put(self::ACTIVE_KEYS, $activeKeys, now()->addHours(24));
        }

        $isNew = ! Cache::has($key);

        $result = Cache::remember($key, now()->addMinutes(self::TTL_MINUTES), $callback);

        // Catat waktu build hanya saat cache baru dibuat
        if ($isNew) {
            $this->touchLastUpdated();
        }

        return $result;
    }

    public function flush(): void
    {
        $activeKeys = Cache::get(self::ACTIVE_KEYS, []);
        foreach ($activeKeys as $key) {
            Cache::forget($key);
        }
        Cache::forget(self::ACTIVE_KEYS);
    }

    public function buildKey(
        string $dateFrom,
        string $dateTo,
        string $branchId,
        string $sortBy,
        string $mode,
        int $page,
        int $perPage
    ): string {
        return "ranking_fo:{$dateFrom}:{$dateTo}:{$branchId}:{$sortBy}:{$mode}:{$page}:{$perPage}";
    }

    public function buildTop3Key(
        string $dateFrom,
        string $dateTo,
        string $branchId,
        string $sortBy
    ): string {
        return "ranking_fo:top3:{$dateFrom}:{$dateTo}:{$branchId}:{$sortBy}";
    }

    public function forceRefresh(): string
    {
        $this->flush();
        // Kembalikan waktu flush supaya controller bisa kasih feedback
        return now()->format('H:i:s');
    }
}
