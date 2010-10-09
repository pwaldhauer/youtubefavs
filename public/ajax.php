<?php
/******************************************************************************
 * 
 * knspr-youtubefavs - some scripts to backup your youtube favs
 * Copyright (C) 2010 Philipp Waldhauer
 *
 * This program is free software; you can redistribute it and/or modify it 
 * under the terms of the GNU General Public License as published by the 
 * Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with 
 * this program; if not, write to the Free Software Foundation, Inc., 
 * 51 Franklin St, Fifth Floor, Boston, MA 02110, USA 
 * 
 *****************************************************************************/

require_once('../includes.php');

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