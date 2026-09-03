<?php

use Carbon\Carbon;

/**
 * Clean name before upload
 *
 * @param string $name
 * @return string
 */
function cleanName(string $name)
{
    $exploded = explode('.', $name);

    if (count($exploded) >= 2) {
        $extension = array_pop($exploded);
        $basename = implode('.', $exploded);
    } else {
        $extension = '';
        $basename = $name;
    }

    $basename = preg_replace('/\s+/', '_', $basename);

    return sprintf(
        "%s_%s%s",
        Carbon::now()->unix(),
        $basename,
        $extension ? ('.' . $extension) : ''
    );
}

/**
 * Delete a local label/PDF/image file referenced by label_url if it lives on this server.
 * Supports absolute URLs (/storage/...), relative public paths, and storage disk paths.
 */
function deleteLocalMediaFile(?string $urlOrPath): bool
{
    if (!$urlOrPath || !is_string($urlOrPath)) {
        return false;
    }

    $path = $urlOrPath;

    // Absolute URL → path component only
    if (preg_match('#^https?://#i', $path)) {
        $parsed = parse_url($path, PHP_URL_PATH);
        if (!$parsed) {
            return false;
        }
        $path = $parsed;
    }

    $path = str_replace('\\', '/', $path);

    // storage symlink: /storage/uploads/... → storage/app/public/uploads/...
    if (strpos($path, '/storage/') !== false || strpos($path, 'storage/') === 0) {
        $relative = preg_replace('#^.*/storage/#', '', $path);
        $relative = ltrim($relative, '/');
        $full = storage_path('app/public/' . $relative);
        if (is_file($full)) {
            return @unlink($full);
        }
    }

    // Public path (e.g. /imgs/documents/..., public/tmp/...)
    $publicCandidate = public_path(ltrim($path, '/'));
    if (is_file($publicCandidate)) {
        return @unlink($publicCandidate);
    }

    // Already an absolute filesystem path
    if (is_file($path)) {
        return @unlink($path);
    }

    return false;
}

function cm2inch($size)
{
    if ($size == null) {
        return null;
    }

    return $size * 0.39370;
}

function kg2pound($weight)
{
    if ($weight == null) {
        return null;
    }

    return $weight * 2.20462262;
}
