<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var string           $query
 *
 * @var \Entity\Artist[] $artists
 * @var \Entity\Album[]  $albums
 * @var \Entity\Genre[]  $genres
 * @var \Entity\Track[]  $tracks
 */

use Action\AlbumController;
use Action\ArtistController;
use Action\GenreController;
use Action\TracksController;

$query = $query ?? '';
$nothingFound =
    (isset($artists) && count($artists) === 0) &&
    (isset($albums) && count($albums) === 0) &&
    (isset($genres) && count($genres) === 0) &&
    (isset($tracks) && count($tracks) === 0);

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'search';
?>

    <div class="content-box">
        <form>
            <label class="full-width">
                <input type="text" name="q" placeholder="Your query" value="<?= $query ?>">
            </label>
            <button type="submit" class="btn btn-confirm full-width">Search</button>
        </form>

        <?php if ($nothingFound): ?>
            <p style="margin-top: 15px; text-align: center">Nothing found</p>
        <?php endif ?>
    </div>

<?php if (isset($artists) && count($artists) > 0) : ?>
    <div class="content-box">
        <h3 class="content-box-header">Artists</h3>
        <div class="genre-list-container">
            <?php foreach ($artists as $artist): ?>
                <a href="<?= ArtistController::generateUrlForShow($artist->id) ?>" class="btn btn-confirm genre-link">
                    <?= h($artist->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($albums) && count($albums) > 0) : ?>
    <div class="content-box">
        <h3 class="content-box-header">Albums</h3>
        <div class="genre-list-container">
            <?php foreach ($albums as $album): ?>
                <a href="<?= AlbumController::generateUrlForShow($album->id) ?>" class="btn btn-confirm genre-link">
                    <?= h($album->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($genres) && count($genres) > 0) : ?>
    <div class="content-box">
        <h3 class="content-box-header">Genres</h3>
        <div class="genre-list-container">
            <?php foreach ($genres as $genre): ?>
                <a href="<?= GenreController::generateUrlForShow($genre->id) ?>" class="btn btn-confirm genre-link">
                    <?= h($genre->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($tracks) && count($tracks) > 0) : ?>
    <div class="content-box">
        <h3 class="content-box-header">Tracks</h3>
        <div class="genre-list-container">
            <?php foreach ($tracks as $track): ?>
                <a href="<?= TracksController::generateUrlForShow($track->id) ?>" class="btn btn-confirm genre-link">
                    <?= h($track->title) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>