<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Track    $track
 * @var \Entity\Artist   $artist
 * @var \Entity\Album    $album
 * @var \Entity\Genre    $genre
 */
use View\ViewEngine;

?>
<div class="track-entry-image">
    <img src="<?= $album->getFullImageUrl() ?>" class="fit-parent-horizontal" alt="<?= h($album->title) ?> album artwork">
    <?= ViewEngine::getInstance()->render('shared/track_detailed_playback.php', [
        'track' => $track,
        'album' => $album,
        'genre' => $genre,
        'artist' => $artist,
    ]) ?>
</div>
