<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Offer[]  $offers
 * @var \Entity\Offer    $currentOffer
 */
$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'home';

$urlRoot = Router::getInstance()->getGlobalPrefix();

if (isset($currentOffer)) {
    $offers = [$currentOffer];
}
?>
<?php $renderInfo->prepareBlock('css', function () use ($urlRoot) { ?>
    <link href="<?= $urlRoot ?>assets/css/index.css" rel="stylesheet">
<?php }); ?>

<div class="content-box">
    <h2 class="content-box-header">
        <?php if (isset($currentOffer)): ?>
            Your current plan
        <?php else: ?>
            Our offers
        <?php endif ?>
    </h2>
</div>

<div id="plans-wrap">
    <?php foreach ($offers as $offer): ?>
        <div class="content-box plan-box">
            <img src="<?= $offer->getFullImageUrl() ?>" alt="<?= h($offer->title) ?> offer graphic" class="plan-image"/>
            <div>
                <div class="plan-header-row">
                    <div class="plan-name"><?= h($offer->title) ?></div>
                    <div class="plan-price">$<?= h($offer->price) ?></div>
                </div>
                <div class="plan-description"><?= h($offer->description) ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>