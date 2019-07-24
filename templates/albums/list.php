<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Album[]  $albums
 * @var \Entity\Artist[] $artistsById
 */

use Action\AlbumController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'albums';
?>

<?php foreach ($albums as $album): ?>
    <div class="content-box album-list-item">
        <img src="<?= $album->getFullThumbnailUrl() ?>" alt="<?= h($album->title) ?> album artwork" class="album-artwork">
        <a href="<?= AlbumController::generateUrlForShow($album->id) ?>" class="btn btn-confirm name-link">
            <div class="album-name"><?= h($album->title) ?></div>
            <div class="artist-name"><?= h($artistsById[$album->artistId]->title) ?></div>
        </a>
    </div>
<?php endforeach; ?>