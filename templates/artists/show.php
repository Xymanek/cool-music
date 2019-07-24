<?php
/**
 * @var \View\RenderInfo  $renderInfo
 * @var \Entity\Artist    $artist
 * @var \Entity\Album[]   $albums
 * @var \Entity\Track[][] $tracksByAlbumId
 * @var \Entity\Genre[]   $genresById
 */

use Action\AlbumController;
use Action\TracksController;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'artists';
?>

    <div class="content-box">
        <h2 class="content-box-header"><?= h($artist->title) ?></h2>
    </div>

<?php foreach ($albums as $album): ?>
    <div class="content-box album-preview">
        <img src="<?= $album->getFullImageUrl() ?>" alt="<?= h($album->title) ?> album artwork" class="album-artwork">
        <div class="album-details-container">
            <div class="content-box-header-with-actions album-header-row">
                <h3 class="content-box-header album-name"><?= h($album->title) ?></h3>
                <div>
                    <a class="btn btn-confirm" href="<?= AlbumController::generateUrlForShow($album->id) ?>">
                        Album details
                    </a>
                </div>
            </div>
            <div>
                <?php foreach ($tracksByAlbumId[$album->id] as $track): ?>
                    <div class="track-row">
                        <div class="track-name"><?= h($track->title) ?></div>
                        <div class="track-genre"><?= h($genresById[$track->genreId]->title) ?></div>
                        <a href="<?= TracksController::generateUrlForShow($track->id) ?>" class="btn btn-confirm">
                            Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>