<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var bool|null        $needMoreReviews
 * @var \Entity\Track[]  $tracks
 * @var \Entity\Artist[] $artistsById
 * @var \Entity\Album[]  $albumsById
 * @var \Entity\Genre[]  $genresById
 */

use Action\TracksController;
use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'tracks';

$needMoreReviews = $needMoreReviews ?? false;
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            Recommended tracks
        </h2>
        <div>
            <a href="<?= TracksController::generateUrlNoParts() ?>" class="btn btn-confirm">
                Show all
            </a>
        </div>
    </div>

    <?php if ($needMoreReviews): ?>
        <p>Please rate at least 3 tracks in order to generate recommendations</p>
    <?php endif ?>
</div>

<?php if (!$needMoreReviews): ?>
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

    <?php if (count($tracks) < 15): ?>
        <div class="content-box">
            Review more tracks to generate more suggestions
        </div>
    <?php endif ?>
<?php endif ?>