<?php
/**
 * @var \View\RenderInfo $renderInfo
 *
 * @var string           $title
 */

use Action\PlaylistController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'playlists';

// Form defaults
$title = $title ?? '';
?>

    <div class="content-box">
        <div class="content-box-header-with-actions">
            <h2 class="content-box-header">
                New playlist
            </h2>
            <div>
                <a href="<?= PlaylistController::generateUrlNoParts() ?>" class="btn btn-confirm">
                    Back to list
                </a>
            </div>
        </div>

        <form method="post">
            <div class="form-row">
                <label for="playlist-title">Name</label>
                <input type="text" id="playlist-title" name="title" value="<?= $title ?>">
            </div>

            <button class="btn btn-success full-width btn-big" type="submit" id="submit-button" disabled>
                Create
            </button>
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