<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Playlist $playlist
 *
 * @var string           $title
 */

use Action\PlaylistController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'playlists';

// Form defaults
$title = $title ?? $playlist->title;
?>

<div class="content-box">
    <div class="content-box-header-with-actions">
        <h2 class="content-box-header">
            Edit <?= h($playlist->title) ?>
        </h2>
        <div>
            <a href="<?= PlaylistController::generateUrlForShow($playlist->id) ?>" class="btn btn-confirm">
                Back
            </a>
        </div>
    </div>

    <form method="post">
        <div class="form-row">
            <label for="playlist-title">Name</label>
            <input type="text" id="playlist-title" name="title" value="<?= $title ?>">
        </div>

        <button class="btn btn-success full-width btn-big" type="submit" disabled id="submit-button">Save</button>
    </form>
</div>

<?php $renderInfo->prepareBlock('scripts', function () { ?>
    <script>
        var submitButton = document.getElementById('submit-button');
        var tile = document.getElementById('playlist-title');

        tile.addEventListener('keyup', function () {
            if (tile.value.length > 2) {
                submitButton.removeAttribute('disabled');
            } else {
                submitButton.setAttribute('disabled', 'disabled');
            }
        });
    </script>
<?php }); ?>