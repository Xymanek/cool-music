<?php
/**
 * @var \View\RenderInfo   $renderInfo
 * @var \Entity\Track      $track
 * @var \Entity\Artist     $artist
 * @var \Entity\Album      $album
 * @var \Entity\Genre      $genre
 * @var \Entity\Review[]   $reviews
 * @var \Entity\Playlist[] $userPlaylists
 */

use Action\PlaylistController;
use Action\TracksController;
use View\ViewEngine;

$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'tracks';
?>

<div class="album-artwork-wrap track-page-artwork">
    <img src="<?= $album->getFullImageUrl() ?>" class="fit-parent-horizontal"
         alt="<?= h($album->title) ?> album artwork">
</div>

<div class="content-box">
    <?= ViewEngine::getInstance()->render('shared/track_with_artwork.php', [
        'track' => $track,
        'album' => $album,
        'genre' => $genre,
        'artist' => $artist,
    ]) ?>
    <?= h($track->description) ?>
</div>

<?php if (Auth::getInstance()->getLoggedInUser() !== null): ?>
    <div class="content-box">
        <h3 class="content-box-header">Your playlists</h3>

        <div class="track-playlists-container">
            <?php foreach ($userPlaylists as $playlist): ?>
                <div class="playlist-row">
                    <a href="<?= PlaylistController::generateUrlForShow($playlist->id) ?>"
                       class="btn btn-confirm playlist-link">
                        <?= h($playlist->title) ?>
                    </a>

                    <form method="post"
                          action="<?= PlaylistController::generateUrlForAction($playlist->id, 'manage-track') ?>">
                        <input type="hidden" name="track_id" value="<?= $track->id ?>">

                        <?php if ($playlist->hasTrack($track->id)): ?>
                            <input type="hidden" name="action" value="remove">
                            <button type="submit" class="btn btn-danger">Remove this track</button>
                        <?php else: ?>
                            <input type="hidden" name="action" value="add">
                            <button type="submit" class="btn btn-success">Add this track</button>
                        <?php endif ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($userPlaylists) === 0): ?>
            <p>You have no playlists 😢</p>
            <p>Creat some by pressing on "Playlists" in the main menu above</p>
        <?php endif; ?>
    </div>
<?php endif ?>

<div class="content-box">
    <h3 class="content-box-header">Reviews</h3>

    <?php if (count($reviews) > 0): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="track-review">
                <div class="review-info">
                    <div class="review-username"><?= h($review->userName) ?></div>
                    <?= ViewEngine::getInstance()->render('shared/rating-stars.php', [
                        'rating' => $review->rating
                    ]) ?>
                </div>
                <div>
                    <?= h($review->content) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div>No reviews, be the first to submit one</div>
    <?php endif; ?>
</div>

<div class="content-box">
    <h3 class="content-box-header">Leave a review</h3>

    <?php if (Auth::getInstance()->getLoggedInUser() !== null): ?>
        <form method="post" class="track-review-form"
              action="<?= TracksController::generateUrlForAction($track->id, 'add-review') ?>">
            <div class="rating-submit-row">
                <fieldset class="starability-basic">
                    <input type="radio" id="no-rate" class="input-no-rate" name="rating" value="0" checked
                           aria-label="No rating."/>

                    <input type="radio" id="first-rate1" name="rating" value="1"/>
                    <label for="first-rate1" title="Terrible">1 star</label>

                    <input type="radio" id="first-rate2" name="rating" value="2"/>
                    <label for="first-rate2" title="Not good">2 stars</label>

                    <input type="radio" id="first-rate3" name="rating" value="3"/>
                    <label for="first-rate3" title="Average">3 stars</label>

                    <input type="radio" id="first-rate4" name="rating" value="4"/>
                    <label for="first-rate4" title="Very good">4 stars</label>

                    <input type="radio" id="first-rate5" name="rating" value="5"/>
                    <label for="first-rate5" title="Amazing">5 stars</label>
                </fieldset>
                <button type="submit" class="btn btn-confirm" id="submit-button" disabled>Submit</button>
            </div>
            <label>
                <textarea placeholder="Your comments here..." name="content"></textarea>
            </label>
        </form>
    <?php else: ?>
        <div class="sign-in-required">
            Please sign in to leave a review
        </div>
    <?php endif ?>
</div>

<?php if (Auth::getInstance()->getLoggedInUser() !== null): ?>
    <?php $renderInfo->prepareBlock('scripts', function () { ?>
        <script>
             var submitButton = document.getElementById('submit-button');

            for (var i = 1; i <= 5; i++) {
                document
                    .getElementById('first-rate' + i)
                    .addEventListener('click', function () {
                        submitButton.removeAttribute('disabled');
                    })
            }
        </script>
    <?php }); ?>
<?php endif ?>
