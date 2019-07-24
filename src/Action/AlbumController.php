<?php
declare(strict_types=1);

namespace Action;

use Database\Exception\NoResultsException;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Entity\Album;
use Entity\Artist;
use Entity\Genre;
use Entity\Track;
use HttpResponse;

class AlbumController extends CrudController
{
    protected static $baseParts = ['albums'];

    protected function executeNoParts (): HttpResponse
    {
        $albums = Album::fetchAll();

        $artistsById = [];
        foreach (getAllArtistIds($albums) as $artistId) {
            $artistsById[$artistId] = Artist::findById($artistId);
        }

        return $this->renderView('albums/list.php', [
            'albums' => $albums,
            'artistsById' => $artistsById,
        ]);
    }

    protected function show (int $id): HttpResponse
    {
        try {
            $album = Album::findById($id);
        } catch (NoResultsException $e) {
            throw new NotFoundException('', 0, $e);
        }

        $artist = Artist::findById($album->artistId);
        $tracks = Track::fetchByCriteria(
            ComparisonCondition::equals('album_id', MysqlParam::integer($id))
        );

        $genresById = [];
        foreach (getAllGenreIds($tracks) as $genreId) {
            $genresById[$genreId] = Genre::findById($genreId);
        }

        return $this->renderView('albums/show.php', [
            'album' => $album,
            'artist' => $artist,
            'tracks' => $tracks,
            'genresById' => $genresById
        ]);
    }
}