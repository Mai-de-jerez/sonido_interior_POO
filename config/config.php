<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_URL', '/sonido-interior-POO');

define(
    'SITE_URL',
    'http://' . $_SERVER['HTTP_HOST'] . BASE_URL
);