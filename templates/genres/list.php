<?php

/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Genre[]  $genres
 */

use Action\GenreController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'genres';
?>

<div class="content-box genre-list-container">
    <?php foreach ($genres as $genre): ?>
        <a href="<?= GenreController::generateUrlForShow($genre->id) ?>" class="btn btn-confirm genre-link">
            <?= h($genre->title) ?>
        </a>
    <?php endforeach; ?>
</div>
