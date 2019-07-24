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
use Entity\Track;
use Entity\TrackInPlaylist;
use HttpResponse;
use Notification;

class PlaylistController extends CrudController
{
    use RequireLoggedInTrait;

    protected static $baseParts = ['playlists'];

    protected function executeNoParts (): HttpResponse
    {
        return $this->renderView('playlists/list.php', [
            'playlists' => Playlist::fetchByCriteria(
                ComparisonCondition::equals(
                    'user_id',
                    MysqlParam::integer(Auth::getInstance()->getLoggedInUser()->id)
                )
            )
        ]);
    }

    protected function create (): HttpResponse
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $userId = Auth::getInstance()->getLoggedInUser()->id;

            $errors = [];
            $title = $_POST['title'];

            $this->validateTitleLength($title, $errors);
            $this->validateUniqueTitle($title, $errors);

            if (count($errors) > 0) {
                $this->queueErrorsNotification($errors);

                return $this->renderView('playlists/create.php', [
                    'title' => $title,
                ]);
            }

            $playlist = new Playlist();
            $playlist->userId = $userId;
            $playlist->title = $title;
            $playlist->save();

            $notification = new Notification();
            $notification->content = 'Playlist ' . h($playlist->title) . ' created';
            $notification->styleSuccess();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($playlist->id));
        }

        return $this->renderView('playlists/create.php');
    }

    protected function show (int $id): HttpResponse
    {
        $playlist = $this->getAndCheck($id);

        // In theory this should be a join
        // But for sake of keeping the "ORM" simple, I will just do it manually
        $trackIds = array_map(
            function (TrackInPlaylist $trackInPlaylist): int {
                return $trackInPlaylist->trackId;
            },
            TrackInPlaylist::fetchByCriteria(
                ComparisonCondition::equals('playlist_id', MysqlParam::integer($playlist->id))
            )
        );

        $tracks = array_map(function (int $id): Track {
            return Track::findById($id);
        }, $trackIds);

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

        return $this->renderView('playlists/show.php', [
            'playlist' => $playlist,

            'tracks' => $tracks,
            'genresById' => $genresById,
            'albumsById' => $albumsById,
            'artistsById' => $artistsById,
        ]);
    }

    protected function edit (int $id): HttpResponse
    {
        $playlist = $this->getAndCheck($id);

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $errors = [];
            $title = $_POST['title'];

            $this->validateTitleLength($title, $errors);
            $this->validateUniqueTitle($title, $errors, $playlist);

            if (count($errors) > 0) {
                $this->queueErrorsNotification($errors);

                return $this->renderView('playlists/edit.php', [
                    'playlist' => $playlist,
                    'title' => $title,
                ]);
            }

            $playlist->title = $title;
            $playlist->save();

            $notification = new Notification();
            $notification->content = 'Playlist ' . h($playlist->title) . ' updated';
            $notification->styleSuccess();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($playlist->id));
        }

        return $this->renderView('playlists/edit.php', [
            'playlist' => $playlist,
        ]);
    }

    protected function delete (int $id): HttpResponse
    {
        $playlist = $this->getAndCheck($id);

        if (strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
            $playlist->delete();

            $notification = new Notification();
            $notification->content = 'Playlist ' . h($playlist->title) . ' deleted';
            $notification->styleSuccess();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlNoParts());
        }

        return $this->renderView('playlists/delete.php', [
            'playlist' => $playlist,
        ]);
    }

    protected function customAction (string $action, int $id, array $additionalParts): HttpResponse
    {
        switch ($action) {
            case 'manage-track';
                return $this->manageTrack($id);
        }

        return parent::customAction($action, $id, $additionalParts);
    }

    protected function manageTrack (int $playlistId): HttpResponse
    {
        try {
            $playlist = $this->getAndCheck($playlistId);
        } catch (NotFoundException $e) {
            $notification = new Notification();
            $notification->content = h('Cannot manage playlist - playlist not found');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlNoParts());
        }

        $trackId = $_POST['track_id'] ?? null;
        $action = $_POST['action'] ?? null;

        if ($trackId === null) {
            $notification = new Notification();
            $notification->content = h('Cannot manage playlist - no track specified');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($playlist->id));
        }

        if ($action === null) {
            $notification = new Notification();
            $notification->content = h('Cannot manage playlist - no action (add/remove) specified');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($playlist->id));
        }

        try {
            $trackId = (int) $trackId;
            $track = Track::findById($trackId);
        } catch (NoResultsException $e) {
            $notification = new Notification();
            $notification->content = h('Cannot manage playlist - track not found');
            $notification->styleDanger();
            $notification->addToQueue();

            return HttpResponse::redirect(self::generateUrlForShow($playlist->id));
        }

        switch ($action) {
            case 'add':
                if ($playlist->hasTrack($trackId)) {
                    $notification = new Notification();
                    $notification->content = h("Track {$track->title} already exists in playlist {$playlist->title}");
                    $notification->styleConfirm();
                    $notification->addToQueue();

                    return HttpResponse::redirect($_SERVER['HTTP_REFERER']);
                }

                $trackInPlaylist = new TrackInPlaylist();
                $trackInPlaylist->playlistId = $playlist->id;
                $trackInPlaylist->trackId = $track->id;
                $trackInPlaylist->save();

                $notification = new Notification();
                $notification->content = h("Track {$track->title} added to playlist {$playlist->title}");
                $notification->styleSuccess();
                $notification->addToQueue();

                return HttpResponse::redirect($_SERVER['HTTP_REFERER']);

            case 'remove':
                try {
                    $trackInPlaylist = TrackInPlaylist::fetchOneByCriteria(
                        ComparisonCondition::equals('playlist_id', MysqlParam::integer($playlist->id)),
                        ComparisonCondition::equals('track_id', MysqlParam::integer($track->id))
                    );
                } catch (NoResultsException $e) {
                    $notification = new Notification();
                    $notification->content = h("Track {$track->title} already does not exist in playlist {$playlist->title}");
                    $notification->styleConfirm();
                    $notification->addToQueue();

                    return HttpResponse::redirect($_SERVER['HTTP_REFERER']);
                }

                $trackInPlaylist->delete();

                $notification = new Notification();
                $notification->content = h("Track {$track->title} removed from playlist {$playlist->title}");
                $notification->styleSuccess();
                $notification->addToQueue();

                return HttpResponse::redirect($_SERVER['HTTP_REFERER']);

            default:
                $notification = new Notification();
                $notification->content = h('Cannot manage playlist - specified action is invalid');
                $notification->styleDanger();
                $notification->addToQueue();

                return HttpResponse::redirect($_SERVER['HTTP_REFERER']);
        }
    }

    ////////////////
    /// Fetching ///
    ////////////////

    private function getAndCheck (int $id): Playlist
    {
        try {
            $playlist = Playlist::findById($id);
        } catch (NoResultsException $e) {
            throw new NotFoundException('', 0, $e);
        }

        if ($playlist->userId !== Auth::getInstance()->getLoggedInUser()->id) {
            throw new NotFoundException('Cannot view playlists of other users');
        }

        return $playlist;
    }

    //////////////////
    /// Validation ///
    //////////////////

    private function validateTitleLength ($title, array &$errors)
    {
        if (empty($title)) {
            $errors[] = 'Name is required';
        } elseif (strlen($title) < 3) {
            $errors[] = 'Name is too short (min 3 characters)';
        } elseif (strlen($title) > 30) {
            $errors[] = 'Name is too long (max 30 characters)';
        }
    }

    private function validateUniqueTitle ($title, array &$errors, Playlist $currentPlaylist = null)
    {
        $userId = Auth::getInstance()->getLoggedInUser()->id;

        $conditions = [
            ComparisonCondition::equals('user_id', MysqlParam::integer($userId)),
            ComparisonCondition::equals('title', MysqlParam::string($title)),
        ];

        if ($currentPlaylist !== null) {
            $conditions[] = ComparisonCondition::notEquals('id', MysqlParam::integer($currentPlaylist->id));
        }

        $exitingPlaylists = Playlist::fetchByCriteria(...$conditions);

        if (count($exitingPlaylists) > 0) {
            $errors[] = 'You already have a playlist with this name';
        }
    }
}