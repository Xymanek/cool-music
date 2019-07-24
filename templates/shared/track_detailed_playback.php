<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Track    $track
 * @var \Entity\Artist   $artist
 * @var \Entity\Album    $album
 * @var \Entity\Genre    $genre
 * @var \Entity\Playlist $currentPlaylist
 */

use Action\PlaylistController;
use Action\TracksController;
use Entity\Review;
use View\ViewEngine;

$showDetailsLink = $showDetailsLink ?? false;

// Note: do not show buttons container if there is nothing there since it creates 10px of empty vertical space
//       due to the margins
?>

<div class="track-detailed-playback">
    <div class="track-info-container">
        <div>
            <div class="track-name"><?= h($track->title) ?></div>
            <div class="album-name"><?= h($album->title) ?></div>
            <div class="artist-name"><?= h($artist->title) ?></div>
        </div>
        <div class="right-column">
            <div class="genre-name"><?= h($genre->title) ?></div>
            <?= ViewEngine::getInstance()->render('shared/rating-stars.php', [
                'rating' => Review::getAverageRatingForTrack($track)
            ]) ?>

            <?php if (isset($currentPlaylist) || $showDetailsLink): ?>
                <div class="details-link-container">
                    <?php if (isset($currentPlaylist)): ?>
                        <form method="post"
                              action="<?= PlaylistController::generateUrlForAction($currentPlaylist->id,
                                  'manage-track') ?>">
                            <input type="hidden" name="track_id" value="<?= $track->id ?>">
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    <?php endif; ?>
                    <?php if ($showDetailsLink): ?>
                        <a class="btn btn-confirm" href="<?= TracksController::generateUrlForShow($track->id) ?>">
                            Details
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif ?>

        </div>
    </div>

    <?php if (Auth::getInstance()->getLoggedInUser() !== null): ?>
        <audio controls>
            <source src="<?= $track->getFullSampleUrl() ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    <?php else: ?>
        <div class="sign-in-required">
            Please sign in to access content
        </div>
    <?php endif ?>
</div>