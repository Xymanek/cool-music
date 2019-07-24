<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Album    $album
 * @var \Entity\Artist   $artist
 * @var \Entity\Track[]  $tracks
 * @var \Entity\Genre[]  $genresById
 */
use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'albums';
?>

<div class="content-box">
    <h2 class="content-box-header"><?= h($album->title) ?></h2>
</div>

<div class="album-artwork-wrap">
    <img src="<?= $album->getFullImageUrl() ?>" class="fit-parent-horizontal" alt="<?= h($album->title) ?> album artwork">
</div>

<?php foreach ($tracks as $track): ?>
    <div class="content-box">
        <?= ViewEngine::getInstance()->render('shared/track_detailed_playback.php', [
            'track' => $track,
            'album' => $album,
            'genre' => $genresById[$track->genreId],
            'artist' => $artist,
            'showDetailsLink' => true,
        ]) ?>
    </div>
<?php endforeach; ?>
