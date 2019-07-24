<?php
declare(strict_types=1);

namespace Action;

use Auth;
use Entity\Album;
use Entity\Artist;
use Entity\Genre;
use Entity\Review;
use Entity\Track;
use HttpResponse;

class RecommendedTracksAction extends SimplePageAction
{
    use RequireLoggedInTrait;

    protected static $pathParts = ['tracks', 'recommend'];

    public function execute (): HttpResponse
    {
        $user = Auth::getInstance()->getLoggedInUser();

        if (Review::countForUser($user) < 3) {
            return $this->renderView('tracks/recommend.php', [
                'needMoreReviews' => true,
            ]);
        }

        $tracks = Track::getRecommendedForUser($user->username);

        $albumsById = [];
        foreach (getAllAlbumIds($tracks) as $albumId) {
            $albumsById[$albumId] = Album::findById($albumId);
        }

        $artistsById = [];
        foreach (getAllArtistIds($albumsById) as $artistId) {
            $artistsById[$artistId] = Artist::findById($artistId);
        }

        $genresById = [];
        foreach (getAllGenreIds($tracks) as $genreId) {
            $genresById[$genreId] = Genre::findById($genreId);
        }

        return $this->renderView('tracks/recommend.php', [
            'tracks' => $tracks,
            'genresById' => $genresById,
            'albumsById' => $albumsById,
            'artistsById' => $artistsById,
        ]);
    }
}