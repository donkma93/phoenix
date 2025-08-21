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
