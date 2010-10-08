<?php

require_once('includes.php');

KnsprYoutube::println('Welcome. Creating database.');

if(file_exists(DATABASE)) {
	KnsprYoutube::println('Database file already exists. Nothing to do.');
}

$yt = new KnsprYoutube();
$yt->connect();
$yt->createTable();

KnsprYoutube::println('Ready.');
