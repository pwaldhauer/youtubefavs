<?php

require('includes.php');

$yt = new KnsprYoutube();
$yt->connect();

$videos = $yt->getList();


/**
js auslagern, dateien neu ordnen (crons woanders außerhalb public html)


manche videos erscheinen garnich im feed (wearhscheibnlich geblockte)
manche erscheinen trotzdem, gibt dann fehlermeldung  bei youtube-dl
*/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link rel="stylesheet" href="theme/style1.css" type="text/css" />
<script type="text/javascript" src="js/flowplayer/flowplayer-3.2.4.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>

<title>Youtube Favorites</title>

<script>


function showVideo(hash, title, content) {
	$('.video').removeClass('active');
	$('#video-' + hash).addClass('active');
	
	flowplayer('player', 'js/flowplayer/flowplayer-3.2.5.swf', 'videos/' + hash + '.flv');
	$('#description').html('<h2>' + title + '</h2><p>' + content + '</p>');

	addHash(hash);
}

function addHash(hash) {
	if(location.href.indexOf('#') != -1) {
		var url = location.href.substr(0, location.href.indexOf('#'));
	} else {
		var url = location.href;
	}

	location.href = url + '#' + hash;
}
	

function doSearch() {
	$.post('ajax.php', {action: 'search', search: $('#search').val()}, function(data) {
		var json = $.parseJSON(data);

		$('#list').html(json.list);

		if($('#instant').is(':checked') && json.first != undefined && json.first != null) {		
			showVideo(json.first.hash, json.first.title, json.first.content);
		}

	});
}

$(function() {
	$('#search').bind('keyup', function() { doSearch(); $('.x').show()});

	$('#search').bind('focus', function() {
		 if($(this).val() == 'Suche') $(this).val('');
	});

	$('#search').bind('blur', function() {
		 if($(this).val() == '') $(this).val('Suche');
	});

	$('.x').bind('click', function() {
		$('#search').val('');
		doSearch();
		$(this).hide();
	});

	if(location.href.indexOf('#') != -1) {
		var hash = location.href.substr(location.href.indexOf('#')+ 1);

		$.post('ajax.php', {action: 'getByHash', hash: hash}, function(data) {
			var json = $.parseJSON(data);

			if(json != undefined && json != null) {
				showVideo(json.hash, json.title, json.content);
			}
		});
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

