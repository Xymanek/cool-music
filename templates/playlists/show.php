<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Playlist $playlist
 *
 * @var \Entity\Track[]  $tracks
 * @var \Entity\Artist[] $artistsById
 * @var \Entity\Album[]  $albumsById
 * @var \Entity\Genre[]  $genresById
 */

use Action\PlaylistController;
use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'playlists';

// TODO: Add remove button to track panel
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            <?= h($playlist->title) ?>
        </h2>
        <div>
            <a href="<?= PlaylistController::generateUrlForAction($playlist->id, 'delete') ?>" class="btn btn-danger">
                Delete
            </a>
            <a href="<?= PlaylistController::generateUrlForAction($playlist->id, 'edit') ?>" class="btn btn-confirm">
                Edit
            </a>
            <a href="<?= PlaylistController::generateUrlNoParts() ?>" class="btn btn-confirm">
                Back to list
            </a>
        </div>
    </div>

    <?php if (count($tracks) === 0): ?>
        <p>No tracks here 😢</p>
    <?php endif ?>
</div>

<?php foreach ($tracks as $track): ?>
    <div class="content-box">
        <?= ViewEngine::getInstance()->render('shared/track_detailed_playback.php', [
            'track' => $track,
            'album' => $albumsById[$track->albumId],
            'genre' => $genresById[$track->genreId],
            'artist' => $artistsById[$albumsById[$track->albumId]->artistId],
            'showDetailsLink' => true,
            'currentPlaylist' => $playlist,
        ]) ?>
    </div>
<?php endforeach; ?>
