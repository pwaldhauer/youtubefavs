<?php

require_once('includes.php');

$action = isset($_POST['action']) ? $_POST['action'] : null;

$yt = new KnsprYoutube();
$yt->connect();

if($action == 'search') {
	$result = array();

	$videos = $yt->getList($_POST['search']);
	$result['list'] = KnsprYoutubeInterface::getListContent($videos);

	if(count($videos)) {
		$result['first'] = $videos[0];
	}

	echo json_encode($result);
	die();
}

if($action == 'getByHash') {
	$video = $yt->getByHash($_POST['hash']);

	echo json_encode($video);
	die();
}
