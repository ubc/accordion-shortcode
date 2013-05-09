var YTControl_Shortcode = {
	players: {},
	
	onReady: function() {
		jQuery('.yc_player').each( function() {
			var element = jQuery(this);
			var id = element.attr('id');
			
			var player = new YT.Player( id, {
				videoId: element.data('vid'),
				playerVars: {
					'autoplay': element.data('play'),
					'autohide': element.data('hide'),
					'theme': element.data('theme'),
					'showinfo': 0,
					'modestbranding': 1,
					'origin': location.host,
				},
				events: {
					'onReady': function( event ) {
						YTControl_Shortcode.players[id] = event.target;
					}
				}
			} );
		} );
	},
	
	skipTo: function( id, time ) {
		YouTube_SYTControl_Shortcodehortcode.players[id].seekTo( time, true );
	},
}

function onYouTubeIframeAPIReady() {
	YTControl_Shortcode.onReady();
}

// This code loads the YouTube IFrame Player API asynchronously.
var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
