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

KnsprYoutube::println('Welcome. Creating database.');

if(file_exists(DATABASE)) {
	KnsprYoutube::println('Database file already exists. Nothing to do.');
}

$yt = new KnsprYoutube();
$yt->connect();
$yt->createTable();

KnsprYoutube::println('Please provide your Youtube Username:');
$name = trim(fread(STDIN, 255));

$yt->setUsername($name);

KnsprYoutube::println('Creating directories...');

if(!is_dir(SHDIR)) {
	mkdir(SHDIR, 0777);
	KnsprYoutube::println(SHDIR .' -- Temporary script files.');
} else {
	KnsprYoutube::println(SHDIR .' already existed');
}

if(!is_dir(VIDEODIR)) {
	mkdir(VIDEODIR, 0777);
	KnsprYoutube::println(VIDEODIR .' -- Downloaded videos.');
} else {
	KnsprYoutube::println(VIDEODIR .' already existed');
}
	

KnsprYoutube::println('Ready.');