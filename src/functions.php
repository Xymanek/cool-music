<?php
declare(strict_types=1);

use Entity\Album;
use Entity\Track;

/**
 * Shortcut for use in templates.
 * Used to protect against HTML injection attacks
 *
 * @param string $str Unescaped string
 * @return string
 */
function h (string $str): string
{
    return htmlentities($str);
}

/**
 * @param Track[] $tracks
 * @return int[]
 */
function getAllGenreIds (array $tracks): array
{
    return array_unique(
        array_map(function (Track $track): int {
            return $track->genreId;
        }, $tracks)
    );
}

/**
 * @param Track[] $tracks
 * @return int[]
 */
function getAllAlbumIds (array $tracks): array
{
    return array_unique(
        array_map(function (Track $track): int {
            return $track->albumId;
        }, $tracks)
    );
}

/**
 * @param Album[] $tracks
 * @return int[]
 */
function getAllArtistIds (array $tracks): array
{
    return array_unique(
        array_map(function (Album $track): int {
            return $track->artistId;
        }, $tracks)
    );
}
