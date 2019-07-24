<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var string           $currentPage
 */

use Action\AlbumController;
use Action\ArtistController;
use Action\GenreController;
use Action\HomeAction;
use Action\LoginAction;
use Action\LogoutAction;
use Action\PlaylistController;
use Action\RegisterAction;
use Action\SearchAction;
use Action\TracksController;
use View\MenuItem;

$urlRoot = Router::getInstance()->getGlobalPrefix();
$user = Auth::getInstance()->getLoggedInUser();

if (empty($htmlTitle)) {
    $htmlTitle = 'CoolMusic';
} else {
    $htmlTitle .= ' | CoolMusic';
}

//////////////////
/// Build menu ///
//////////////////

/** @var MenuItem[] $menuItems */
$menuItems = [];
$currentPage = $currentPage ?? null;

$menuItems[] = new MenuItem('Home', HomeAction::generateUrl(), $currentPage === 'home');
$menuItems[] = new MenuItem('Search', SearchAction::generateUrl(), $currentPage === 'search');
$menuItems[] = new MenuItem('Artists', ArtistController::generateUrlNoParts(), $currentPage === 'artists');
$menuItems[] = new MenuItem('Albums', AlbumController::generateUrlNoParts(), $currentPage === 'albums');
$menuItems[] = new MenuItem('Genres', GenreController::generateUrlNoParts(), $currentPage === 'genres');
$menuItems[] = new MenuItem('Tracks', TracksController::generateUrlNoParts(), $currentPage === 'tracks');

if ($user == null) {
    $menuItems[] = new MenuItem('Register', RegisterAction::generateUrl(), $currentPage === 'register');
    $menuItems[] = new MenuItem('Login', LoginAction::generateUrl(), $currentPage === 'login');
} else {
    $menuItems[] = new MenuItem('Playlists', PlaylistController::generateUrlNoParts(), $currentPage === 'playlists');
    $menuItems[] = new MenuItem('Logout', LogoutAction::generateUrl(), $currentPage === 'logout', 'red');
}

/////////////////////
/// Notifications ///
/////////////////////

$notifications = Notification::consumeAllQueued();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no"/>
    <title><?= $htmlTitle ?></title>

    <link rel="apple-touch-icon" sizes="180x180" href="<?= $urlRoot ?>assets/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $urlRoot ?>assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $urlRoot ?>assets/favicon-16x16.png">
    <link rel="manifest" href="<?= $urlRoot ?>assets/site.webmanifest">
    <link rel="mask-icon" href="<?= $urlRoot ?>assets/safari-pinned-tab.svg" color="#5cb2f2">
    <link rel="shortcut icon" href="<?= $urlRoot ?>assets/favicon.ico">
    <meta name="msapplication-TileColor" content="#5cb2f2">
    <meta name="msapplication-config" content="<?= $urlRoot ?>assets/browserconfig.xml">
    <meta name="theme-color" content="#5cb2f2">

    <link href="<?= $urlRoot ?>assets/css/starability-basic.min.css" rel="stylesheet">
    <link href="<?= $urlRoot ?>assets/fontawesome/css/all.min.css" rel="stylesheet">
    <link href="<?= $urlRoot ?>assets/css/style.css" rel="stylesheet">
    <?= $renderInfo->renderBlock('css') ?>
</head>
<body>

<div id="desktop-warning" style="display: none"> <!-- Hiding here so that it's hidden if css can't be loaded -->
    It appears that you are viewing this site on a desktop.
    Please note that it was designed for mobiles.
    Use "responsive view" (or similar) tool in browser to get a proper view
</div>

<header id="site-header">
    <div class="wrap">
        <div class="title-container">
            <div>
                <h1>CoolMusic</h1>
                <?php if ($user !== null): ?>
                    Hi, <?= h($user->username) ?>
                <?php endif; ?>
            </div>
        </div>
        <img src="<?= $urlRoot ?>assets/images/headphones-2104207_640.png" alt="Company logo (headphones)">
    </div>

    <nav id="main-menu" class="nav-item-container">
        <?php foreach ($menuItems as $item): ?>
            <a
                    href="<?= $item->url ?>"
                    class="navigation-item<?php if ($item->current): ?> current<?php endif ?>"
                    <?php if ($item->colour !== null): ?>style="background-color: <?= $item->colour ?>"<?php endif; ?>
            >
                <?= h($item->label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
</header>

<div id="page-main-wrap">
    <div id="content-wrap">
        <?php foreach ($notifications as $notification): ?>
            <div class="content-box"
                 style="color: <?= $notification->color ?>; background-color: <?= $notification->backgroundColor ?>">
                <?= $notification->content ?>
            </div>
        <?php endforeach; ?>

        <?= $renderInfo->childContent ?>
    </div>
</div>

<footer id="site-footer">
    <p>CoolMusic.com © 2019</p>
</footer>

<?= $renderInfo->renderBlock('scripts') ?>

</body>
</html>
