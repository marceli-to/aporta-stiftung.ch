<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class ApplicationStore
{
    public const PENDING = 'json/pending';
    public const FORWARDED = 'json/forwarded';
    public const FAILED = 'json/failed';

    private const DIRS = [self::PENDING, self::FAILED, self::FORWARDED];

    public function store(array $payload): void
    {
        $id = $payload['submission_id'] ?? null;
        if (! $id) {
            return;
        }

        $this->disk()->put(
            self::PENDING."/{$id}.json",
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    public function markForwarded(string $submissionId): void
    {
        $this->moveTo($submissionId, self::FORWARDED);
    }

    public function markFailed(string $submissionId): void
    {
        $this->moveTo($submissionId, self::FAILED);
    }

    public function load(string $submissionId): ?array
    {
        $disk = $this->disk();
        foreach (self::DIRS as $dir) {
            $path = "{$dir}/{$submissionId}.json";
            if ($disk->exists($path)) {
                $data = json_decode((string) $disk->get($path), true);

                return is_array($data) ? $data : null;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listFailed(): array
    {
        $disk = $this->disk();
        $list = [];

        foreach ($disk->files(self::FAILED) as $file) {
            if (! str_ends_with($file, '.json')) {
                continue;
            }
            $data = json_decode((string) $disk->get($file), true);
            if (is_array($data)) {
                $list[] = $data;
            }
        }

        return $list;
    }

    private function moveTo(string $submissionId, string $destDir): void
    {
        $disk = $this->disk();
        $filename = "{$submissionId}.json";

        foreach (self::DIRS as $dir) {
            if ($dir === $destDir) {
                continue;
            }
            $src = "{$dir}/{$filename}";
            if ($disk->exists($src)) {
                $disk->move($src, "{$destDir}/{$filename}");

                return;
            }
        }
    }

    private function disk(): Filesystem
    {
        return Storage::disk('local');
    }
}
