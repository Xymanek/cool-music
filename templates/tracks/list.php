<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Track[]  $tracks
 * @var \Entity\Artist[] $artistsById
 * @var \Entity\Album[]  $albumsById
 * @var \Entity\Genre[]  $genresById
 */

use Action\RecommendedTracksAction;
use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'tracks';
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            All tracks
        </h2>
        <div>
            <?php if (Auth::getInstance()->getLoggedInUser() !== null): ?>
                <a href="<?= RecommendedTracksAction::generateUrl() ?>" class="btn btn-confirm">
                    Show recommended
                </a>
            <?php endif ?>
        </div>
    </div>
</div>

<?php foreach ($tracks as $track): ?>
    <div class="content-box">
        <?= ViewEngine::getInstance()->render('shared/track_detailed_playback.php', [
            'track' => $track,
            'album' => $albumsById[$track->albumId],
            'genre' => $genresById[$track->genreId],
            'artist' => $artistsById[$albumsById[$track->albumId]->artistId],
            'showDetailsLink' => true,
        ]) ?>
    </div>
<?php endforeach; ?>
