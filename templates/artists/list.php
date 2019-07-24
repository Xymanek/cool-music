<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Artist[] $artists
 */

use Action\ArtistController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'artists';
?>

<div class="content-box genre-list-container">
    <?php foreach ($artists as $artist): ?>
        <a href="<?= ArtistController::generateUrlForShow($artist->id) ?>" class="btn btn-confirm genre-link">
            <?= h($artist->title) ?>
        </a>
    <?php endforeach; ?>
</div>

