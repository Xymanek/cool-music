<?php
declare(strict_types=1);

namespace Action;

use Auth;
use Database\Exception\NoResultsException;
use Database\MysqlParam;
use Database\WhereClause\ComparisonCondition;
use Entity\Album;
use Entity\Artist;
use Entity\Genre;
use Entity\Playlist;
use Entity\Review;
use Entity\Track;
use HttpResponse;
use Notification;

class TracksController extends CrudController
{
    protected static $baseParts = ['tracks'];

    protected function executeNoParts (): HttpResponse
    {
        $artists = Artist::fetchAll();
        $albums = Album::fetchAll();
        $genres = Genre::fetchAll();

        $artistsById = [];
        foreach ($artists as $artist) {
            $artistsById[$artist->id] = $artist;
        }

        $albumsById = [];
        foreach ($albums as $album) {
            $albumsById[$album->id] = $album;
        }

        $genresById = [];
        foreach ($genres as $genre) {
            $genresById[$genre->id] = $genre;
        }

        return $this->renderView('tracks/list.php', [
            'tracks' => Track::fetchAll(),
            'artistsById' => $artistsById,
            'albumsById' => $albumsById,
            'genresById' => $genresById,
        ]);
    }

    protected function show (int $id): HttpResponse
    {
        try {
            $track = Track::findById($id);
        } catch (NoResultsException $e) {
            throw new NotFoundException('', 0, $e);
        }

        $album = Album::findById($track->albumId);
        $genre = Genre::findById($track->genreId);
        $artist = Artist::findById($album->artistId);
        $reviews = Review::fetchByCriteria(ComparisonCondition::equals('track_id', MysqlParam::integer($id)));

        // Playlist functionality
        if (Auth::getInstance()->getLoggedInUser() !== null) {
            $userPlaylists = Playlist::fetchByCriteria(ComparisonCondition::equals(
                'user_id', MysqlParam::integer(Auth::getInstance()->getLoggedInUser()->id)
            ));
        }

        return $this->renderView('tracks/show.php', [
            'track' => $track,
            'album' => $album,
            'genre' => $genre,
            'artist' => $artist,
            'reviews' => $reviews,
            'userPlaylists' => $userPlaylists ?? null,
        ]);
    }

    protected function customAction (string $action, int $id, array $additionalParts): HttpResponse
    {
        if ($action === 'add-review' && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            return $this->addReview($id);
        }

        return parent::customAction($action, $id, $additionalParts);
    }

    const ALLOWED_RATINGS = [1, 2, 3, 4, 5];

    private function addReview (int $trackId): HttpResponse
    {
        try {
            Track::findById($trackId);
        } catch (NoResultsException $e) {
            $notification = new Notification();
            $notification->content = h('Cannot add review - track not found');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlNoParts());
        }

        $user = Auth::getInstance()->getLoggedInUser();

        if ($user === null) {
            $notification = new Notification();
            $notification->content = h('Only logged in users can add reviews');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($trackId));
        }

        $rating = (int) ($_POST['rating'] ?? 0);
        $content = $_POST['content'] ?? '';
        $errors = [];

        if (!in_array($rating, self::ALLOWED_RATINGS, true)) {
            $errors[] = 'Please select a rating';
        }

        if (count($errors) === 0) {
            $review = new Review();
            $review->trackId = $trackId;
            $review->userName = $user->username;
            $review->rating = $rating;
            $review->content = $content;
            $review->save();

            $notification = new Notification();
            $notification->content = 'Review saved';
            $notification->styleSuccess();
            $notification->addToQueue();
        } else {
            $this->queueErrorsNotification($errors);
        }

        return HttpResponse::redirect(self::generateUrlForShow($trackId));
    }
}