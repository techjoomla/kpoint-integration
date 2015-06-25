<?php
/**
 * @package    Shika
 * @author     TechJoomla | <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.plugin.plugin');?>

<?php
$lang = JFactory::getLanguage();
$lang->load('plg_tjvideo_kpoint', JPATH_ADMINISTRATOR);

/**
 * Vimeo plugin from techjoomla
 *
 * @since  1.0.0
 */

class PlgvideoKpoint extends JPlugin
{
	/**
	 * Plugin that supports uploading and tracking the videos for jWplayer plugin
	 *
	 * @param   string   &$subject  The context of the content being passed to the plugin.
	 * @param   integer  $config    Optional page number. Unused. Defaults to zero.
	 *
	 * @return  void.
	 *
	 * @since 1.0.0
	 */

	public function plgTjvideoKpoint(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Function to get needed data for this API
	 *
	 * @return  id from tjlms_lesson_tracking
	 *
	 * @since 1.0.0
	 */
	public function updateData()
	{
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$mode = $input->get('mode', '', 'STRING');
		$re = '';

		if ($mode != 'preview')
		{
			$post = $input->post;
			$lesson_id = $post->get('lesson_id', '', 'INT');
			$oluser_id = JFactory::getUser()->id;
			$lesson_status = $post->get('lesson_status', '', 'STRING');
			$trackObj = new stdClass;
			$trackObj->attempt = $post->get('attempt', '', 'INT');
			$trackObj->score = 0;
			$trackObj->total_content = $post->get('total_content', '', 'FLOAT');
			$trackObj->current_position = $post->get('current_position', '', 'FLOAT');
			$trackObj->time_spent = $post->get('time_spent', '', 'FLOAT');

			$trackObj->lesson_status = $lesson_status;

			require_once JPATH_SITE . '/components/com_tjlms/helpers/tracking.php';

			$comtjlmstrackingHelper = new comtjlmstrackingHelper;
			$trackingid = $comtjlmstrackingHelper->update_lesson_track($lesson_id, $oluser_id, $trackObj);

			echo json_encode($trackingid);
			jexit();
		}
	}

	/**
	 * Function to get Sub Format options when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_type>ContentInfo
	 *
	 * @param   ARRAY  $config  config specifying allowed plugins
	 *
	 * @return  object.
	 *
	 * @since 1.0.0
	 */
	public function getSubFormat_tjvideoContentInfo($config = array('kpoint'))
	{
		if (!in_array($this->_name, $config))
		{
			return;
		}

		$obj 			= array();
		$obj['name']	= $this->params->get('plugin_name', 'kpoint player');
		$obj['id']		= $this->_name;

		return $obj;
	}

	/**
	 * Function to get Sub Format HTML when creating / editing lesson format
	 * the name of function should follow standard getSubFormat_<plugin_name>ContentHTML
	 *
	 * @param   INT    $mod_id       id of the module to which lesson belongs
	 * @param   INT    $lesson_id    id of the lesson
	 * @param   MIXED  $lesson       Object of lesson
	 * @param   ARRAY  $comp_params  Params of component
	 *
	 * @return  html
	 *
	 * @since 1.0.0
	 */
	public function getSubFormat_KpointContentHTML($mod_id, $lesson_id, $lesson, $comp_params)
	{
		if (empty($lesson_id))
		{
			$lesson_id = 0;
		}

		$result = array();
		$plugin_name = $this->_name;
		$source = (isset($lesson->format_details['source'])) ? $lesson->format_details['source'] : '';

		$html = '';

		if ($lesson_id == 0)
		{
			$html .= '
			<script>
			jQuery(function($) {
				SqueezeBox.initialize({});
				SqueezeBox.assign($("a.modal").get(), {
					parse: "rel"
				});
			});
			</script>';
		}

		$html .= '
		<div class="control-label">' . JText::_("COM_TJLMS_VIDEO_FORMAT_URL_OPTIONS") . '</div>
		<div  class="controls">
			<input type="hidden" class="class_video_format"
						id="lesson_format' . $plugin_name . 'video_source"
						name="lesson_format[' . $plugin_name . '][video_source]"
						value="url"/>
			<div id="video_textarea" >
				<a class="modal btn"
				href="' . JURI::root() . 'index.php?option=com_tjlms&task=callSysPlgin&plgType=tjvideo';
				$html .= '&plgName=kpoint&plgtask=getHtml&callType=1&lesson_id=' . $lesson_id . '" rel="{size: {x: 700, y: 300}}">
						' . JText::_("COM_TJLMS_VIDEO_KPINT_BTN") . '
				</a>
				<span class="kpoint_text' . $lesson_id . '"/></span>  <img class="kpoint_href' . $lesson_id . '" href="" width="25%" height="25%"/>';
				$html .= '<input type="hidden" 	id="video_url" value="' . trim($source) . '"
							class="kpoint_video' . $lesson_id . ' input-block-level" cols="50" rows="2"
							name="lesson_format[' . $plugin_name . '][video_format_source]" ' . trim($source) . '>
			</div>
		</div>
		<script>function bindKpoint' . $lesson_id . '(ids,displayname,href,des)
{
	if(' . $lesson_id . ')
	{
		jQuery("#lesson-basic-form_' . $mod_id . '_' . $lesson_id . ' input[id=jform_name]").val(displayname);
		jQuery("#lesson-basic-form_' . $mod_id . '_' . $lesson_id . ' textarea[id=jform_description]").val(des);
	}
	else
	{
		jQuery("#lesson-basic-form_' . $mod_id . ' input[id=jform_name]").val(displayname);
		jQuery("#lesson-basic-form_' . $mod_id . ' textarea[id=jform_description]").val(des);
	}
	jQuery(".kpoint_video' . $lesson_id . '").val(ids);
	jQuery(".kpoint_text' . $lesson_id . '").text(displayname);
	jQuery(".kpoint_href' . $lesson_id . '").attr("src", href);
	window.parent.SqueezeBox.close();
}</script>';

		return $html;
	}

	/**
	 * Function to render the video
	 *
	 * @param   ARRAY  $config  data to be used to play video
	 *
	 * @return  complete html along with script is return.
	 *
	 * @since 1.0.0
	 */
	public function renderPluginHTML($config)
	{
		$input = JFactory::getApplication()->input;
		$mode = $input->get('mode', '', 'STRING');
		$scriptfile = JURI::root(true) . '/plugins/video/kpoint/assets/js/kpoint.js';

		// YOUR CODE TO RENDER HTML
		$file_id = $config['file'];
		$file_id = trim($file_id);

		$html = '
		<script type="text/javascript">
		var plugdataObject = {
			plgtype: "' . $this->_type . '",
			plgname: "' . $this->_name . '",
			plgtask:"updateData",
			lesson_id: ' . $config['lesson_id'] . ',
			attempt: ' . $config['attempt'] . ',
			file_id : "' . $file_id . '",
			seekTo : ' . $config['current'] . ',
			mode:  "' . $mode . '",
			domain : "' . $this->params->get('domain_name') . '",
			client_id : "' . $this->params->get('client_id') . '",
			xauth_token : "' . $this->createToken($this->params->get('email_id'), $this->params->get('display_name')) . '",
			email_id :  "'.$this->params->get('email_id').'",
			display_name :"'.$this->params->get('display_name').'",
			challenge : "' . time() . '"
		};
		</script>

		<div id="main_kapsule_container">
			<div id="main_kapsule"></div>
		</div>
		<div id="main_kapsule_container-ie">
			<div id="main_kapsule-ie"></div>
		</div>
		<div id="controls" class="tb">
			<span id="timerId" href="#"></span>
		</div>
		<script src="' . $scriptfile . '"></script>
		';

		// This may be an iframe directlys
		return $html;
	}

	/**
	 * Function to render kapsule list.
	 *
	 * @return  complete html along with script is return.
	 *
	 * @since 1.0.0
	 */
	public function getHtml()
	{
		$token = $this->createToken();
		$listData = $this->getKapsulelist($token);

		echo $html = $this->buildLayout($listData);
	}

	/**
	 * Function to render kapsule list.
	 *
	 * @return  complete html along with script is return.
	 *
	 * @since 1.0.0
	 */
	public function getHtmlAjax()
	{

		$token = $this->createToken();
		$listData = $this->getKapsulelist($token);

		$html = $this->buildLayout($listData);

		return $html;
	}

	/**
	 * Internal use functions
	 *
	 * @param   STRING  $layout  layout
	 *
	 * @return  file
	 *
	 * @since 1.0.0
	 */
	public function buildLayoutPath($layout)
	{
		$app = JFactory::getApplication();
		$core_file 	= dirname(__FILE__) . '/' . $this->_name . '/tmpl/' . $layout . '.php';
		$override = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/plugins/' . $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if (JFile::exists($override))
		{
			return $override;
		}
		else
		{
			return $core_file;
		}
	}

	/**
	 * Builds the layout to be shown, along with hidden fields.
	 *
	 * @param   ARRAY   $vars    vars to be used
	 * @param   STRING  $layout  layout
	 *
	 * @return  html
	 *
	 * @since 1.0.0
	 */
	public function buildLayout($vars, $layout = 'default')
	{
		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);
		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Builds token for api access
	 *
	 * @param   STRING  $email        vars to be used
	 * @param   STRING  $displayname  layout
	 *
	 * @return  RAW
	 *
	 * @since 1.0.0
	 */
	public function createToken($email = null, $displayname = null)
	{
		$input = JFactory::getApplication()->input;
		$qtext = $input->get('qtext', '', 'string');
		$first = $input->get('first', '', 'INT');
		$max = $input->get('max', '', 'INT');

		$KPOINT_HOST = $this->params->get('domain_name');
		$CLIENT_ID = $this->params->get('client_id');
		$SECRET_KEY = $this->params->get('secret_key');

		if ($email == null)
		{
			$email = $this->params->get('email_id');
		}

		if ($displayname == null)
		{
			$displayname = $this->params->get('display_name');
		}

		$this->challenge = time();
		$data = "$CLIENT_ID:$email:$displayname:$this->challenge";
		$token = hash_hmac("md5", $data, $SECRET_KEY, true);

		$b64token = base64_encode($token);
		$b64token = str_replace("=", "", $b64token);
		$b64token = str_replace("+", "-", $b64token);
		$b64token = str_replace("/", "_", $b64token);

		return $b64token;
	}

	/**
	 * Get list of video in kpoint of admin user
	 *
	 * @param   STRING  $token  token for api access
	 *
	 * @return  RAW
	 *
	 * @since 1.0.0
	 */
	public function getKapsulelist($token)
	{
		$application = JFactory::getApplication();
		$input = JFactory::getApplication()->input;

		$KPOINT_HOST = $this->params->get('domain_name');
		$CLIENT_ID = $this->params->get('client_id');
		$SECRET_KEY = $this->params->get('secret_key');
		$email = $this->params->get('email_id');
		$displayname = $this->params->get('display_name');

		$qtext = $input->get('qtext', '', 'string');
		$first = $input->get('first', '', 'INT');
		$max = $input->get('max', '', 'INT');

		if (empty($KPOINT_HOST) || empty($CLIENT_ID) || empty($SECRET_KEY) || empty($email) || empty($displayname))
		{
			return 1;
		}

		if ($qtext && empty($first))
		{
			$xtencode = "?client_id=" . $CLIENT_ID . "&user_email=" . $email . "&user_name=";
			$xtencode .= $displayname . "&challenge=" . $this->challenge . "&xauth_token=" . $token . "&qtext=" . $qtext . '*';
		}
		elseif ($first && empty($qtext))
		{
			$xtencode = "/recent?client_id=" . $CLIENT_ID . "&user_email=" . $email . "&user_name=";
			$xtencode .= $displayname . "&challenge=" . $this->challenge . "&xauth_token=" . $token . "&first=" . $first;
		}
		elseif ($first && $qtext)
		{
			$xtencode = "?client_id=" . $CLIENT_ID . "&user_email=" . $email . "&user_name=";
			$xtencode .= $displayname . "&challenge=" . $this->challenge . "&xauth_token=" . $token;
			$xtencode .= "&qtext=" . $qtext . '*' . "&first=" . $first ;
		}
		else
		{
			$xtencode = "/recent?client_id=" . $CLIENT_ID . "&user_email=" . $email . "&user_name=";
			$xtencode .= $displayname . "&challenge=" . $this->challenge . "&xauth_token=" . $token . "&first=0";
		}

		$last_letter_URL = substr($this->params->get('domain_name'), -1);

		if ($last_letter_URL == "/")
		{
			$url = $this->params->get('domain_name') . 'api/v1/xapi/kapsules' . $xtencode;
		}
		else
		{
			$url = $this->params->get('domain_name') . '/api/v1/xapi/kapsules' . $xtencode;
		}

		$http = new JHttp;
		$options = new JRegistry;
		$transport = new JHttpTransportStream($options);

		// Create a 'stream' transport.
		$http = new JHttp($options, $transport);
		try
		{
			$result = $http->post($url,'');

			return $obj = json_decode($result->body, true);

		}
		catch (Exception $e)
		{
			return $e->getMessage();
		}
	}
}
