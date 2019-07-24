<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Genre    $genre
 * @var \Entity\Track[]  $tracks
 * @var \Entity\Artist[] $artistsById
 * @var \Entity\Album[]  $albumsById
 */

use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'genres';
?>

<div class="content-box">
    <h2 class="content-box-header"><?= $genre->title ?></h2>
</div>

<?php foreach ($tracks as $track): ?>
    <div class="content-box">
        <?= ViewEngine::getInstance()->render('shared/track_detailed_playback.php', [
            'track' => $track,
            'album' => $albumsById[$track->albumId],
            'genre' => $genre,
            'artist' => $artistsById[$albumsById[$track->albumId]->artistId],
            'showDetailsLink' => true,
        ]) ?>
    </div>
<?php endforeach; ?>
