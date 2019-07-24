<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Playlist $playlist
 */

use Action\PlaylistController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'playlists';
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            <?= h($playlist->title) ?>
        </h2>
        <div>
            <a href="<?= PlaylistController::generateUrlNoParts() ?>" class="btn btn-confirm">
                Back to list
            </a>
        </div>
    </div>

    <p>
        Are you sure you want to delete this playlist?
    </p>

    <form method="post">
        <input type="submit" class="btn btn-danger btn-big full-width" value="Confirm">
    </form>
</div>
