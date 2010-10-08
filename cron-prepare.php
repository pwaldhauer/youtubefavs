<?php

$url = 'http://gdata.youtube.com/feeds/api/users/knuspermagier/favorites';

require('includes.php');

$yt = new KnsprYoutube();
$yt->connect();

$start = 1;

while(true) {
	$content = file_get_contents($url . '?start-index='. $start .'&max-results=50');
	$xml = simplexml_load_string($content);

	if(!isset($xml->entry)) {
		break;
	}

	foreach($xml->entry as $entry) {
		$entry = (array)$entry;

		$video = new Video();
		$link = $entry['link'][0]->attributes();

		$video->url = (string)$link->href[0];
		$video->title = (string)$entry['title'];
		$video->content = (string)$entry['content'];
		$video->added = $entry['published'];
		$video->hash = sha1($video->added . $video->title);
		$video->downloaded = false;

		if(!$yt->exists($video)) {
			$yt->insert($video);
			KnsprYoutube::println('Added video to download queue: "'. $video->title .'"');
		}

		if(!$yt->isDownloaded($video)) {
			$yt->createShellScript($video);
		}
	}
		

	$start += 50;
}





?>
