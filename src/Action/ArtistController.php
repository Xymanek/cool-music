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

class ArtistController extends CrudController
{
    protected static $baseParts = ['artists'];

    protected function executeNoParts (): HttpResponse
    {
        return $this->renderView('artists/list.php', [
            'artists' => Artist::fetchAll()
        ]);
    }

    protected function show (int $id): HttpResponse
    {
        try {
            $artist = Artist::findById($id);
        } catch (NoResultsException $e) {
            throw new NotFoundException('', 0, $e);
        }

        $albums = Album::fetchByCriteria(
            ComparisonCondition::equals('artist_id', MysqlParam::integer($id))
        );

        $tracksByAlbumId = [];
        foreach ($albums as $album) {
            $tracksByAlbumId[$album->id] = Track::fetchByCriteria(
                ComparisonCondition::equals('album_id', MysqlParam::integer($album->id))
            );
        }

        $genresById = [];
        foreach (getAllGenreIds(array_merge(...$tracksByAlbumId)) as $genreId) {
            $genresById[$genreId] = Genre::findById($genreId);
        }

        return $this->renderView('artists/show.php', [
            'artist' => $artist,
            'albums' => $albums,
            'tracksByAlbumId' => $tracksByAlbumId,
            'genresById' => $genresById,
        ]);
    }
}