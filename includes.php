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

define('APPVERSION', '0.6');
define('DATABASE', dirname(__FILE__) .'/data.db');
definE('VIDEODIR', dirname(__FILE__) .'/public/videos');
definE('SHDIR', dirname(__FILE__) .'/tmp');

class Video {
	public $url;
	public $title;
	public $content;
	public $added;
	public $hash;
	public $downloaded;
}

class KnsprYoutubeInterface {

	public static function getListContent($videos) {
		$output = '';

		foreach($videos as $video) {
			$output .= '<li class="video" id="video-'. $video->hash .'">';
			$output .= '<a class="title" href="#'. $video->hash .'" onclick="knspr.ytf.showVideo(\''. $video->hash .'\', \''. addslashes(htmlentities($video->title, ENT_COMPAT, 'UTF-8')) .'\', \''. self::myNl2Br(addslashes(htmlentities($video->content, ENT_COMPAT, 'UTF-8'))) .'\');">'. htmlentities($video->title, ENT_COMPAT, 'UTF-8') .'</a>
			<span class="meta">Hinzugefügt am '. date('d.m.Y H:i:s', strtotime($video->added)) .', <a href="'. $video->url .'">Youtube-Link &rarr;</a></span>';
			$output .= '</li>';
		}

		return $output;
	}


	public static function myNl2Br($str) {
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '<br/>', $str);

		return $str;
	}
}

class KnsprYoutube {

	private $pdo = null;

	public function connect() {
		$this->pdo = new PDO('sqlite:'. DATABASE);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function createTable() {
		if($this->pdo == null) {
			die('No PDO connection');
		}

		$tableVideos = 'CREATE TABLE videos(
			url VARCHAR(255) NOT NULL,
			title VARCHAR(255) NOT NULL,
			content text NOT NULL,
			added DATETIME NOT NULL,
			hash VARCHAR(100) NOT NULL,
			downloaded tinyint(1) DEFAULT 0);';
			
		$indexVideos = 'CREATE UNIQUE INDEX uniquehash ON videos (hash);';

		$tableConfig = 'CREATE TABLE config(
			key VARCHAR(255) NOT NULL,
			value VARCHAR(255) NOT NULL);';
		
		$indexConfig = 'CREATE UNIQUE INDEX keyhash ON config(key);';
		
		$dataConfig = 'INSERT INTO config VALUES ("username", "");';

		$this->pdo->exec($tableVideos);
		$this->pdo->exec($tableConfig);		
		$this->pdo->exec($indexVideos);
		$this->pdo->exec($indexConfig);
		$this->pdo->exec($dataConfig);
	}
	
	public function setUsername($username) {
		$stmt = $this->pdo->prepare('UPDATE config SET value = :value WHERE key = "username"');
		$stmt->bindParam(':value', $username);
		
		$stmt->execute();
	}
	
	public function getUsername() {		
		$stmt = $this->pdo->prepare('SELECT value FROM config WHERE key = "username"');
		$stmt->execute();

		$result = $stmt->fetch();
		
		return $result['value'];
	}

	public function insert(Video $video) {
		$stmt = $this->pdo->prepare('INSERT INTO videos VALUES(:url, :title, :content, :added, :hash, :downloaded)');

		$stmt->bindParam(':url', $video->url);
		$stmt->bindParam(':title', $video->title);
		$stmt->bindParam(':content', $video->content);
		$stmt->bindParam(':added', $video->added);
		$stmt->bindParam(':hash', $video->hash);
		$stmt->bindParam(':downloaded', $video->downloaded);

		$stmt->execute();
	}

	public function isDownloaded(Video $video) {
		$stmt = $this->pdo->prepare('SELECT title FROM videos WHERE hash = :hash AND downloaded = 1');
		$stmt->bindParam(':hash', $video->hash);
		
		$stmt->execute();

		$result = $stmt->fetch();

		return ($result == false) ? false : true;
	}
	
	public function	exists(Video $video) {
		$stmt = $this->pdo->prepare('SELECT title FROM videos WHERE hash = :hash');
		$stmt->bindParam(':hash', $video->hash);
		
		$stmt->execute();

		$result = $stmt->fetch();

		return ($result == false) ? false : true;
	}

	public function setDownloaded($hash) {
		$stmt = $this->pdo->prepare('UPDATE videos SET downloaded = 1 WHERE hash = :hash');
		$stmt->bindParam(':hash', $hash);

		$stmt->execute();
	}

	public function getList($search = null) {
		$results = array();

		if($search == null) {
			$stmt = $this->pdo->prepare('SELECT * FROM videos WHERE downloaded = 1 ORDER by added DESC');
		} else {
			$stmt = $this->pdo->prepare('SELECT * FROM videos WHERE downloaded = 1 AND (title LIKE :search OR content LIKE :search) ORDER by added DESC');
			$stmt->bindParam(':search', $param = "%$search%");
		}
		
		$stmt->execute();

		while($row = $stmt->fetch()) {
			$video = new Video();
			$video->url = $row['url'];
			$video->title = $row['title'];
			$video->content = $row['content'];
			$video->added = $row['added'];
			$video->downloaded = $row['downloaded'];
			$video->hash = $row['hash'];

			$results[] = $video;
		}

		return $results;
	}

	public function getByHash($hash) {
		$stmt = $this->pdo->prepare('SELECT * FROM videos WHERE hash = :hash');
		$stmt->bindParam(':hash', $hash);
		$stmt->execute();

		$row = $stmt->fetch();

		if($row == false) {
			return null;
		}

		$video = new Video();
		$video->url = $row['url'];
		$video->title = $row['title'];
		$video->content = $row['content'];
		$video->added = $row['added'];
		$video->downloaded = $row['downloaded'];
		$video->hash = $row['hash'];

		return $video;
	}

	public function getCount($onlyDownloaded = false) {

		if($onlyDownloaded) {
			$stmt = $this->pdo->prepare('SELECT COUNT(hash) FROM videos WHERE downloaded = 1');
		} else {
			$stmt = $this->pdo->prepare('SELECT COUNT(hash) FROM videos');
		}

		$stmt->execute();
		$result = $stmt->fetch();

		return $result[0];
	}	

	
	public static function println($msg) {
		echo '[KnsprYoutube] '. $msg ."\n";
	}

	public function createScriptDirectory() {
		if(!is_dir(SHDIR)) {
			mkdir(SHDIR);
			chmod(SHDIR, 0777);

			return;
		}

		
	}

	public function createShellScript(Video $video) {
		if($video->downloaded == true) {
			return;
		}

		$this->createScriptDirectory();
		
		$videodir = VIDEODIR;

		$shell = <<<EOF
#!/bin/bash

youtube-dl -o "{$videodir}/{$video->hash}.flv" "$video->url"

if [ $? -eq 0 ]; then
	php cron-afterdownload.php "$video->hash"
else
	echo "[KnsprYoutube] Download failed."
fi

EOF;

		file_put_contents($this->getFileName($video->hash), $shell);
	}

	public function getFileName($hash) {
		return SHDIR .'/'. $hash .'.sh';
	}
}

