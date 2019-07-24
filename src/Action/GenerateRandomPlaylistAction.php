<?php
declare(strict_types=1);

namespace Action;

use Auth;
use Database\DatabaseConnection;
use Database\OrderClauseEntry;
use Database\SelectQueryBuilder;
use Entity\Playlist;
use Entity\Track;
use Entity\TrackInPlaylist;
use HttpResponse;

class GenerateRandomPlaylistAction extends SimplePageAction
{
    use RequireLoggedInTrait;

    protected static $pathParts = ['playlists', 'generate-random'];

    public function execute (): HttpResponse
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            throw new NotFoundException('Wrong method');
        }

        $connection = DatabaseConnection::getInstance();
        $playlist = null;

        $connection->transactional(function () use (&$playlist) {
            $playlist = new Playlist();
            $playlist->userId = Auth::getInstance()->getLoggedInUser()->id;
            $playlist->title = 'Generated #' . uniqid();
            $playlist->save();

            $tracksBuilder = SelectQueryBuilder::create()
                ->setOrderEntries(OrderClauseEntry::rand())
                ->setLimit(10);

            $tracks = Track::fetchByBuilder($tracksBuilder);

            foreach ($tracks as $track) {
                $trackInPlaylist = new TrackInPlaylist();
                $trackInPlaylist->playlistId = $playlist->id;
                $trackInPlaylist->trackId = $track->id;
                $trackInPlaylist->save();
            }
        });

        /** @noinspection PhpUndefinedFieldInspection */
        return HttpResponse::redirect(PlaylistController::generateUrlForShow($playlist->id));
    }
}