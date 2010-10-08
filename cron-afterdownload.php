<?php

require_once('includes.php');

if($argc < 2) {
	die('Wrong. First argument needs to be the hash.');
}

$hash = $argv[1];

$yt = new KnsprYoutube();
$yt->connect();

$yt->setDownloaded($hash);

KnsprYoutube::println('Marked this video as downloaded');

if(file_exists($yt->getFileName($hash))) {
	unlink($yt->getFileName($hash));
}

KnsprYoutube::println('Removed script file');

?>
