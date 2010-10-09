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

require_once('includes.php');

$yt = new KnsprYoutube();
$yt->connect();

$username = $yt->getUsername();

$url = 'http://gdata.youtube.com/feeds/api/users/'.  $username .'/favorites';

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