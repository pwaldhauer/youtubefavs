
knspr = {};

knspr.ytf = {};

knspr.ytf.ajaxUrl = 'ajax.php';
knspr.ytf.videoPath = '';

knspr.ytf.autoPlay = false;
knspr.ytf.random = false;
knspr.ytf.currentVideo = '';

knspr.ytf.showVideo = function(hash, title, content) {
	$('.video').removeClass('active');
	$('#video-' + hash).addClass('active');

	location.hash = hash;

	knspr.ytf.currentVideo = hash;
	
	player = flowplayer('player', 'js/flowplayer/flowplayer-3.2.5.swf', {
		clip: {
			url: knspr.ytf.videoPath + hash + '.flv',
			autoPlay: true,
			onFinish: function() {
				if(knspr.ytf.autoPlay == false) {
					return;
				}

				nextVideo = '';
				if(knspr.ytf.random == true) {
					nextVideo = knspr.ytf.getNextRandom();
				} else {
					nextVideo = knspr.ytf.getNext(knspr.ytf.currentVideo);
				}

				knspr.ytf.loadVideo(nextVideo);
			}
		}

	});

	$('#description').html('<h2>' + title + '</h2><p>' + content + '</p>');
}

knspr.ytf.playPrevious = function() {
	knspr.ytf.loadVideo(knspr.ytf.getPrevious(knspr.ytf.currentVideo));
}

knspr.ytf.playNext = function() {
	knspr.ytf.loadVideo(knspr.ytf.getNext(knspr.ytf.currentVideo));
}

knspr.ytf.getNextRandom = function() {
	var elems = $('.video');
	nextVideo = $(elems.get(Math.floor(Math.random() * elems.length))).attr('id').substr(6);
	return nextVideo;
}

knspr.ytf.getNext = function(hash) {
	var id = $('#video-' + hash).next().attr('id');

	if(id == undefined) {
		id = $($('.video').get(0)).attr('id');
	}

	return id.substr(6);
}

knspr.ytf.getPrevious = function(hash) {
	var id =  $('#video-' + hash).prev().attr('id');
	if(id == undefined) {
		id = $($('.video').get(0)).attr('id');
	}
	
	return id.substr(6);
}

knspr.ytf.doSearch = function() {
	$.post(knspr.ytf.ajaxUrl, {action: 'search', search: $('#search').val()}, function(data) {
		var json = $.parseJSON(data);

		$('#list').html(json.list);

		if($('#instant').is(':checked') && json.first != undefined && json.first != null) {		
			knspr.ytf.showVideo(json.first.hash, json.first.title, json.first.content);
		}

	});
}

knspr.ytf.loadVideo = function(hash) {
	$.post(knspr.ytf.ajaxUrl, {action: 'getByHash', hash: hash}, function(data) {
			var json = $.parseJSON(data);

			if(json != undefined && json != null) {
				knspr.ytf.showVideo(json.hash, json.title, json.content);
			}
		});
}

knspr.ytf.activatePlay = function() {
	knspr.ytf.autoPlay = true;
	$('#control-play').attr('src', 'css/control_play_blue.png');
}

knspr.ytf.deactivatePlay = function() {
	knspr.ytf.autoPlay = false;
	$('#control-play').attr('src', 'css/control_play.png');
}

knspr.ytf.toggleRandom = function() {
	knspr.ytf.random = !knspr.ytf.random;

	if(knspr.ytf.random) {
		$('#control-random').attr('src', 'css/arrow_switch_green.png');
	} else {
		$('#control-random').attr('src', 'css/arrow_switch.png');
	}		
		
}

