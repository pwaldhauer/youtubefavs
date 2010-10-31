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

$yt = new KnsprYoutube();
$yt->connect();

$videos = $yt->getList();

/**

TODO:
 - manche videos erscheinen garnich im feed (wearhscheibnlich geblockte)
 - manche erscheinen trotzdem, gibt dann fehlermeldung  bei youtube-dl
*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link rel="stylesheet" href="css/style.css" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="js/flowplayer/flowplayer-3.2.4.min.js"></script>
<script type="text/javascript" src="js/knspr-ytf.js"></script>

<title>Your Youtube Favorites</title>

<script>

$(function() {

	knspr.ytf.videoPath = 'videos/';
	
	$('#search').bind('keyup', function() { 
		knspr.ytf.doSearch();
		$('.x').show()
	});

	$('#search').bind('focus', function() {
		 if($(this).val() == 'Suche') $(this).val('');
	});

	$('#search').bind('blur', function() {
		 if($(this).val() == '') $(this).val('Suche');
	});

	$('.x').bind('click', function() {
		$('#search').val('');
		knspr.ytf.doSearch();
		
		$(this).hide();
	});

	if(location.href.indexOf('#') != -1) {
		knspr.ytf.loadVideo(location.href.substr(location.href.indexOf('#')+ 1));
	}
	
});

</script>

</head>

<body>
	<div id="top" class="clearfix">

		<form class="searchForm">
			<input class="search" name="search" id="search" value="Suche" />
			<span class="x">x</span>
			
			<label for="instant"><input type="checkbox" class="check" name="instant" id="instant" value="1"/> <span title="Wenn aktiviert, wird das erste zurückgegebene Video automatisch gestartet">Instant-Suche</a></label>
		</form>

		<ul class="toolbar">
			<li onclick="javascript:knspr.ytf.playPrevious();"><img src="css/control_rewind.png" alt="previous" title="Previous" /></li>
			<li onclick="javascript:knspr.ytf.activatePlay();"><img id="control-play" src="css/control_play.png" alt="play" title="Play" /></li>
			<li onclick="javascript:knspr.ytf.deactivatePlay();"><img id="control-stop" src="css/control_stop.png" alt="stop" title="Stop" /></li>
			<li onclick="javascript:knspr.ytf.playNext();"><img src="css/control_fastforward.png" alt="forward" title="Next" /></li>
			<li onclick="javascript:knspr.ytf.toggleRandom();"><img id="control-random" src="css/arrow_switch.png" alt="random" title="Activate Random" /></li>
		</ul>

		<div class="info">
		Verfügbare Videos: <?php echo $yt->getCount(true) ?>/<span title="Insgesamt"><?php echo $yt->getCount() ?></span>
		</div>

	</div>

	<ul id="list">
		<?php echo KnsprYoutubeInterface::getListContent($videos) ?>
	</ul>

	<div id="player"></div>

	<div id="description"></div>
</body>
</html>

