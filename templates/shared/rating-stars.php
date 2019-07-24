<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var float            $rating
 */

// The stars can only show 0.5, 1, 1.5, etc - adjust the value
$full = (int) $rating;
$remainder = $rating - $full;

if ($remainder >= 0.75) {
    $rating = $full + 1;
} else if ($remainder >= 0.25) {
    $rating = $full + 0.5;
} else {
    $rating = $full;
}

// Clamp rating to 5 stars. This should never happen, but just in case
if ($rating > 5) {
    $rating = 5;
}
?>

<p class="starability-result" data-rating="<?= $rating ?>">
    Rating: <?= $rating ?> stars
</p>
