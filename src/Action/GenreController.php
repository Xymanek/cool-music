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

class GenreController extends CrudController
{
    protected static $baseParts = ['genres'];

    protected function executeNoParts (): HttpResponse
    {
        return $this->renderView('genres/list.php', [
            'genres' => Genre::fetchAll()
        ]);
    }

    protected function show (int $id): HttpResponse
    {
        try {
            $genre = Genre::findById($id);
        } catch (NoResultsException $e) {
            throw new NotFoundException('', 0, $e);
        }

        $tracks = Track::fetchByCriteria(
            ComparisonCondition::equals('genre_id', MysqlParam::integer($id))
        );

        $albumsById = [];
        foreach (getAllAlbumIds($tracks) as $albumId) {
            $albumsById[$albumId] = Album::findById($albumId);
        }

        $artistsById = [];
        foreach (getAllArtistIds($albumsById) as $artistId) {
            $artistsById[$artistId] = Artist::findById($artistId);
        }

        return $this->renderView('genres/show.php', [
            'genre' => $genre,
            'tracks' => $tracks,
            'albumsById' => $albumsById,
            'artistsById' => $artistsById,
        ]);
    }
}