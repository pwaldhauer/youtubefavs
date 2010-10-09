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

if($argc < 2) {
	die('Go away. First argument needs to be the hash.');
}

$hash = $argv[1];

$yt = new KnsprYoutube();
$yt->connect();

$yt->setDownloaded($hash);

KnsprYoutube::println('Marked this video as downloaded');

if(file_exists($yt->getFileName($hash))) {
	unlink($yt->getFileName($hash));
	KnsprYoutube::println('Removed script file');
}

