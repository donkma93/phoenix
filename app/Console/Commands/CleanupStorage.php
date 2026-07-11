<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Reclaim disk space from SAFE disposable files only:
 *  - public/tmp          (merged print PDFs, short-lived)
 *  - public/imgs/orders  (Excel/CSV import copies — not used at runtime)
 *  - storage/debugbar    (dev request dumps)
 *  - storage/app/previews-*.zip
 *
 * NEVER touches MyIB / Shippo label PDFs under storage/app/public/uploads/PNX_LABEL
 * (or any path matching uploads/PNX_LABEL). Those files are required for preview,
 * print, and tracking display.
 *
 * Run:  php artisan storage:cleanup
 *       php artisan storage:cleanup --dry-run
 */
class CleanupStorage extends Command
{
    /**
     * Absolute path prefixes that must never be deleted by this command.
     */
    protected $protectedPathFragments = [
        'uploads' . DIRECTORY_SEPARATOR . 'PNX_LABEL',
        'uploads/PNX_LABEL',
        'documents' . DIRECTORY_SEPARATOR . 'g7',
        'documents/g7',
    ];

    protected $signature = 'storage:cleanup
                            {--days=14 : Delete order import xlsx/csv older than this many days (0 = all import files)}
                            {--tmp-hours=24 : Delete public/tmp merge files older than this many hours (0 = all tmp)}
                            {--debugbar-hours=24 : Delete debugbar dumps older than this many hours}
                            {--dry-run : Report only, do not delete}';

    protected $description = 'Clean disposable temp/import/debugbar files only. Never deletes MyIB tracking label PDFs.';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');
        $importDays = max(0, (int) $this->option('days'));
        $tmpHours = max(0, (int) $this->option('tmp-hours'));
        $debugbarHours = max(0, (int) $this->option('debugbar-hours'));

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . 'Starting safe storage cleanup...');
        $this->warn('Protected: MyIB label PDFs (uploads/PNX_LABEL) and G7 labels are NEVER deleted by this command.');

        $stats = [
            'tmp' => $this->cleanDirectoryByAge(public_path('tmp'), $tmpHours / 24, $dryRun),
            'order_imports' => $this->cleanDirectoryByAge(public_path('imgs/orders'), $importDays, $dryRun),
            'debugbar' => $this->cleanDirectoryByAge(storage_path('debugbar'), $debugbarHours / 24, $dryRun),
            'preview_zips' => $this->cleanStorageAppZips($tmpHours / 24, $dryRun),
        ];

        $totalFiles = 0;
        $totalBytes = 0;
        foreach ($stats as $name => $result) {
            $totalFiles += $result['files'];
            $totalBytes += $result['bytes'];
            $this->line(sprintf(
                '  %-16s files=%d  freed=%s',
                $name,
                $result['files'],
                $this->formatBytes($result['bytes'])
            ));
        }

        $msg = sprintf(
            'Storage cleanup done: %d files, %s freed%s (labels untouched)',
            $totalFiles,
            $this->formatBytes($totalBytes),
            $dryRun ? ' (dry-run)' : ''
        );
        $this->info($msg);
        Log::info($msg, $stats);

        return 0;
    }

    /**
     * True if path is under a protected operational media folder (MyIB labels, etc.).
     */
    protected function isProtectedPath(string $path): bool
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        foreach ($this->protectedPathFragments as $fragment) {
            $frag = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $fragment);
            if (stripos($normalized, $frag) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Delete files older than $days under $dir.
     * When $days is 0, every non-protected file is considered expired.
     */
    protected function cleanDirectoryByAge(string $dir, float $days, bool $dryRun, bool $recursive = true): array
    {
        $files = 0;
        $bytes = 0;

        if (!is_dir($dir)) {
            return compact('files', 'bytes');
        }

        // Hard stop: never walk into protected trees even if mis-configured as $dir
        if ($this->isProtectedPath($dir)) {
            $this->warn('Skip protected directory: ' . $dir);
            return compact('files', 'bytes');
        }

        $cutoff = $days <= 0
            ? PHP_INT_MAX
            : Carbon::now()->subDays($days)->getTimestamp();

        $iterator = $recursive
            ? new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
            )
            : new \DirectoryIterator($dir);

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            if ($fileInfo->getFilename() === '.gitignore') {
                continue;
            }

            $path = $fileInfo->getPathname();
            if ($this->isProtectedPath($path)) {
                continue;
            }

            // Only disposable extensions for order-import folder (never PDF labels)
            if ($this->isOrderImportDir($dir)) {
                $ext = strtolower($fileInfo->getExtension());
                if (!in_array($ext, ['xlsx', 'xls', 'csv', 'ods'], true)) {
                    continue;
                }
            }

            // public/tmp: only short-lived merge artifacts (C_*.pdf, random temp pdf)
            // Do NOT delete files that look like stored tracking labels
            if ($this->isTmpDir($dir)) {
                $name = $fileInfo->getFilename();
                // Keep anything that is not a temp merge / random name from orderPrintMultiple
                // orderPrintMultiple writes: C_{timestamp}{random}.pdf and {timestamp}{random}.pdf
                if (!preg_match('/\.(pdf|zip)$/i', $name)) {
                    continue;
                }
            }

            if ($fileInfo->getMTime() >= $cutoff) {
                continue;
            }

            $size = $fileInfo->getSize();
            if (!$dryRun) {
                @unlink($path);
            }
            $files++;
            $bytes += $size;
        }

        return compact('files', 'bytes');
    }

    protected function isOrderImportDir(string $dir): bool
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, rtrim($dir, '/\\'));
        return substr($normalized, -strlen('imgs' . DIRECTORY_SEPARATOR . 'orders'))
            === 'imgs' . DIRECTORY_SEPARATOR . 'orders'
            || substr($normalized, -strlen('imgs/orders')) === 'imgs/orders';
    }

    protected function isTmpDir(string $dir): bool
    {
        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, rtrim($dir, '/\\'));
        return substr($normalized, -4) === DIRECTORY_SEPARATOR . 'tmp'
            || substr($normalized, -3) === 'tmp';
    }

    /**
     * Leftover bulk-download zip archives under storage/app (not labels).
     */
    protected function cleanStorageAppZips(float $days, bool $dryRun): array
    {
        $files = 0;
        $bytes = 0;
        $dir = storage_path('app');
        if (!is_dir($dir)) {
            return compact('files', 'bytes');
        }

        $cutoff = $days <= 0 ? PHP_INT_MAX : Carbon::now()->subDays($days)->getTimestamp();
        foreach (glob($dir . DIRECTORY_SEPARATOR . 'previews-*.zip') ?: [] as $path) {
            if (!is_file($path) || filemtime($path) >= $cutoff) {
                continue;
            }
            if ($this->isProtectedPath($path)) {
                continue;
            }
            $size = filesize($path) ?: 0;
            if (!$dryRun) {
                @unlink($path);
            }
            $files++;
            $bytes += $size;
        }

        return compact('files', 'bytes');
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 2) . ' MB';
    }
}
