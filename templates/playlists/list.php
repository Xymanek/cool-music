<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Playlist[] $playlists
 */

use Action\GenerateRandomPlaylistAction;
use Action\PlaylistController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'playlists';
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            Your playlists
        </h2>
        <div>
            <a href="<?= PlaylistController::generateUrlForCreate() ?>" class="btn btn-success">
                Create
            </a>
            <form action="<?= GenerateRandomPlaylistAction::generateUrl() ?>" method="post">
                <button type="submit" class="btn btn-confirm">Generate random one</button>
            </form>
        </div>
    </div>

    <?php if (count($playlists) > 0): ?>
        <div class="genre-list-container">
            <?php foreach ($playlists as $playlist): ?>
                <a href="<?= PlaylistController::generateUrlForShow($playlist->id) ?>" class="btn btn-confirm genre-link">
                    <?= h($playlist->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You have none 😢</p>
        <p>Use the button above to add a new one</p>
    <?php endif ?>
</div>
