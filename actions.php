<?php

use Action\AlbumController;
use Action\ArtistController;
use Action\GenerateRandomPlaylistAction;
use Action\GenreController;
use Action\HomeAction;
use Action\LoginAction;
use Action\LogoutAction;
use Action\NotFoundAction;
use Action\PlaylistController;
use Action\RecommendedTracksAction;
use Action\RegisterAction;
use Action\SearchAction;
use Action\TracksController;

Router::getInstance()->notFoundAction = new NotFoundAction();
Router::getInstance()->actions = [
    new HomeAction(),
    new SearchAction(),

    new RegisterAction(),
    new LoginAction(),
    new LogoutAction(),

    new RecommendedTracksAction(), // Needs to come before tracks
    new TracksController(),
    new ArtistController(),
    new AlbumController(),
    new GenreController(),

    new GenerateRandomPlaylistAction(), // Needs to come before playlists
    new PlaylistController(),
];