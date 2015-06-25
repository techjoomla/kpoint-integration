var lessonStartTime = new Date();
var tjkpoint_Pause = false;
var tjkpoint_Finish = false;
var lessonStoptime = '';
// Function to play kpoint video
techjoomla.jQuery(document).ready(function() {

	if (detectIE())
	{
		iframe_src = plugdataObject.domain+'/kapsule/'+plugdataObject.file_id;
		iframe_src += '/v2/embedded?np=1&autoplay=true&iframefAPI=true';
		iframe_src += '&client_id='+plugdataObject.client_id;
		iframe_src += '&user_email='+plugdataObject.email_id;
		iframe_src += '&user_name='+plugdataObject.display_name;
		iframe_src += '&challenge='+plugdataObject.challenge;
		iframe_src += '&xauth_token='+plugdataObject.xauth_token;

		techjoomla.jQuery('<iframe>', {
			src: iframe_src,
			id:  'myFrame',
			frameborder: 0,
			scrolling: 'no',
			height: (techjoomla.jQuery(window.parent).height()-80),
			width: techjoomla.jQuery(window.parent).width()
		}).appendTo('#main_kapsule-ie');
	}
	else
	{
		var tag = document.createElement("script");
		tag.src = "https://d2hi01dxs6qk7k.cloudfront.net/js/fapi/v1/fapi.min.js";
		var firstScriptTag = document.getElementsByTagName("script")[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	}

});

	// Function to play video
	function onKPointIframeAPIReady() {
		wheight = techjoomla.jQuery(window.parent).height()-80;
		wwidth = techjoomla.jQuery(window.parent).width();
		fapi = new Player("main_kapsule", {
			width: wwidth,
			height:wheight,
			events: {'onReady': 'kapsuleReadyEvent'},
			kapsuleId: plugdataObject.file_id ,
			domain: plugdataObject.domain,
			autoplay : true,
			tokenParams: {
				type : "TOKEN",
				clientId : plugdataObject.client_id,
				userEmail : plugdataObject.email_id,
				userName : plugdataObject.display_name,
				challenge : plugdataObject.challenge ,
				xauth_token : plugdataObject.xauth_token
			}
		});



		var total_content = fapi.duration();
		total_content = Math.floor(total_content / 1000) ;
		var current_position = fapi.currentTime();
		current_position = Math.floor(current_position / 1000) ;
		lessonStoptime = new Date();
		var timespentonLesson = lessonStoptime - lessonStartTime;
		var timeinseconds = Math.round(timespentonLesson / 1000);



		// Call this function on being of video
		plugdataObject.current_position = current_position;
		plugdataObject.total_content = total_content;
		plugdataObject.lesson_status = "started";
		plugdataObject.time_spent = timeinseconds;
		updateData(plugdataObject);

		var myVar = setInterval(timeUpdateEvent, 10000);

		// Update values after evry 10 secs
		//fapi.on("evtTimeUpdate",timeUpdateEvent);

		// Event to fetch changed state of video
		fapi.on(fapi.events.stateChange,onStateChanged);

		// Function to contionously update the video time log
		var player_id = "kpoint";
	}
	function kapsuleReadyEvent() {

		seekTo = plugdataObject.seekTo * 1000;
		console.log(seekTo);
		fapi.seekTo(seekTo);
	}

	function timeUpdateEvent(info) {
		var total_content = fapi.duration();
		total_content = Math.floor(total_content / 1000) ;
		var current_position = fapi.currentTime();
		current_position = Math.floor(current_position / 1000) ;
		lessonStoptime = new Date();
		var timespentonLesson = lessonStoptime - lessonStartTime;
		var timeinseconds = Math.round(timespentonLesson / 1000);

		if(!tjkpoint_Pause && !tjkpoint_Finish)
		{
			console.log("after every 10 sec"+total_content+"curren position"+current_position);
			plugdataObject.current_position = current_position;
			plugdataObject.total_content = total_content;
			plugdataObject.lesson_status = "incomplete";
			plugdataObject.time_spent = timeinseconds;
			updateData(plugdataObject);
		}
	}

	// Function to update state change of the video
	function onStateChanged(info)
	{
		lessonStoptime = new Date();
		var timespentonLesson = lessonStoptime - lessonStartTime;
		var total_content = fapi.duration();
		total_content = Math.floor(total_content / 1000) ;
		var current_position = fapi.currentTime();
		current_position = Math.floor(current_position / 1000) ;
		var timeinseconds = Math.round(timespentonLesson / 1000);

		if (info.state == fapi.playStates.PAUSED)
		{
			tjkpoint_Pause = true;
			if(!tjkpoint_Finish)
			{
				plugdataObject.current_position = current_position;
				plugdataObject.total_content = total_content;
				plugdataObject.lesson_status = "incomplete";
				plugdataObject.time_spent = timeinseconds;
				updateData(plugdataObject);
			}
		}

		if (info.state == fapi.playStates.ENDED)
		{
			tjkpoint_Finish = true;
			plugdataObject.current_position = current_position;
			plugdataObject.total_content = total_content;
			plugdataObject.lesson_status = "completed";
			plugdataObject.time_spent = timeinseconds;
			updateData(plugdataObject);
		}
	}

function detectIE() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
        // IE 11 => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
       // IE 12 => return version number
       return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    // other browser
    return false;
}
