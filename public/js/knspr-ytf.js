
knspr = {};

knspr.ytf = {};

knspr.ytf.ajaxUrl = 'ajax.php';
knspr.ytf.videoPath = '';

knspr.ytf.showVideo = function(hash, title, content) {
	$('.video').removeClass('active');
	$('#video-' + hash).addClass('active');
	
	flowplayer('player', 'js/flowplayer/flowplayer-3.2.5.swf', knspr.ytf.videoPath + hash + '.flv');
	$('#description').html('<h2>' + title + '</h2><p>' + content + '</p>');
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