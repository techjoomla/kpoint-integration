  <link rel="stylesheet" href="http://bajaj.cloudaccess.host/templates/masterbootstrap/css/bootstrap.min.css" type="text/css" />
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.11.2.min.js"></script>
<script>
	jQuery(document).ready(function () {
	jQuery("#filter_search").keypress(function (event) {
			var keycode = (event.keyCode ? event.keyCode : event.which);
				if (keycode == "13")
				{
					var loc = window.location.pathname;
					var filter_search = jQuery("#filter_search").val();
					jQuery.ajax({
					url:loc+"?option=com_attachments&task=callSysPlgin&plgType=video&callType=1&plgName=kpoint&plgtask=getHtmlAjax",
							type: "POST",
							datatype : "json",
							headers: {
								'Content-Type': 'application/x-www-form-urlencoded'
								},
							data: { qtext: filter_search},
							success:function(data)
							{
								console.log(data);
								jQuery("#appendHtml").html(data);
							},
							error : function(data){
							}
						});
				}
            });
	});

	function getHtml(param)
	{
		var filter_search = jQuery("#filter_search").val();
		if (param == "serach")
		{
			jQuery("tbody").hide();
			jQuery("tfoot").show();
			jQuery("button").prop("disabled","disabled");
			jQuery("input").prop("disabled","disabled");

			jQuery.ajax({
			url:"index.php?option=com_attachments&task=callSysPlgin&plgType=video&callType=1&plgName=kpoint&plgtask=getHtmlAjax&qtext="+filter_search,
					type: "post",
					datatype : "json",
					success:function(data)
					{
						console.log(data);
						jQuery("#appendHtml").html('');
						//jQuery("input").removeAttr("disabled");
						//jQuery("button").removeAttr("disabled");
						jQuery("#appendHtml").html(data);
					},
					error : function(data){
						jQuery("tfoot").hide();
						jQuery("input").removeAttr("disabled");
						jQuery("button").removeAttr("disabled");
					}
				});
		}
		else if (param == "clear")
		{
			jQuery("tbody").hide();
			jQuery("tfoot").show();
			jQuery("button").prop("disabled","disabled");
			jQuery("input").prop("disabled","disabled");
			jQuery.ajax({
			url:"index.php?option=com_attachments&task=callSysPlgin&plgType=video&callType=1&plgName=kpoint&plgtask=getHtmlAjax",
					type: "post",
					datatype : "json",
					success:function(data)
					{
						//alert("else if " + data);
						//~ jQuery("tfoot").hide();
						//~ jQuery("input").removeAttr("disabled");
						//~ jQuery("button").removeAttr("disabled");
						//~ jQuery("#appendHtml").html(data);
						jQuery("#appendHtml").html('');
						jQuery("#appendHtml").html(data);
					},
					error : function(data){
						jQuery("tfoot").hide();
						jQuery("input").removeAttr("disabled");
						jQuery("button").removeAttr("disabled");
					}
				});
		}
		else
		{
			jQuery("tbody").hide();
			jQuery("tfoot").show();
			jQuery("button").prop("disabled","disabled");
			jQuery("input").prop("disabled","disabled");
			var limit = jQuery("#list_limit").val();
			var endlimit = parseInt(limit) + 1;

			jQuery.ajax({
			url:"index.php?option=com_attachments&task=callSysPlgin&plgType=video&callType=1&plgName=kpoint&plgtask=getHtmlAjax&qtext="+filter_search+"&first="+limit+"&max="+endlimit,
					type: "post",
					datatype : "json",
					success:function(data)
					{
						//~ jQuery("tfoot").hide();
						//~ jQuery("input").removeAttr("disabled");
						//~ jQuery("button").removeAttr("disabled");
						//~ jQuery("#appendHtml").html(data);
						jQuery("#appendHtml").html('');
						jQuery("#appendHtml").html(data);
					},
					error : function(data){
						jQuery("tfoot").hide();
						jQuery("input").removeAttr("disabled");
						jQuery("button").removeAttr("disabled");
					}
				});
		}
	}

	function bindKpoint(ids,displayname,href,url,des)
	{

		var html = "<iframe src='"+ url +"' allowFullScreen webkitallowFullScreen mozallowFullScreen width='512' height='384' rel='nofollow' style='border: 0px;'></iframe>";

		var jQueryiframe = jQuery(parent.document).find('#jform_articletext_ifr');
		jQueryiframe.contents().find("body").append("{kapsule="+ids+"}");
		//jQueryiframe.contents().find("body").append("<iframe src='"+ url +"' allowFullScreen webkitallowFullScreen mozallowFullScreen width='512' height='384' rel='nofollow' style='border: 0px;'></iframe>");
		window.parent.SqueezeBox.close();
	}

</script>


<?php

/**
 * @package Tjlms
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
*/
JHtml::_('jquery.framework', false);
JHtml::_('jquery.ui', array('sortable'));
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.client.http' );

if (is_array($vars) && array_key_exists('totalcount',$vars))
{
	if ((INT)$vars['totalcount'] < 0)
	{
		echo "Sorry no list found";
	}
	else
	{
		$input = JFactory::getApplication()->input;
		$lesson_id = $input->get('lesson_id', '0', 'INT');



	$html =	'<div id="appendHtml">
	<tr><td colspan="2"><div class="btn-wrapper input-append">
				<input type="text" name="filter" id="filter_search" value="" placeholder="Search">
				<button type="submit" class="btn-primary btn hasTooltip" title="" data-original-title="Search" onclick="getHtml(\'serach\');">
					<i class="icon-search"></i>Search
				</button><button type="submit" class="btn" onclick="getHtml(\'clear\');"><i class="icon-remove"></i>Clear</button>
			</div>
			</td><td></td><td>
					<!--select id="list_limit" name="list[limit]" class="input-mini chzn-done" onchange="getHtml(\'pagination\');" >
		<option value="5" selected>5</option>
		<option value="10">10</option>
		<option value="30">30</option>
		<option value="40">40</option>
		<option value="100">100</option>
		<option value="0">All</option>
	</select-->
				</td></tr>
		<table class="table table-striped table-responsive" id="categoryList" >


				<thead>
				<tr>
					<th class="center">
						Id
					</th>
					<th>
						Name
					</th>
					<th>
						Image
					</th>
					<th>
						Description
					</th>
				</tr>
			</thead>
			<tbody>';
		//print_r($vars); die("Amol");
		if (empty($vars['list']))
		{
			$html .= '<tr>
						<td colspan="4">Record not found</td>
				</tr>';
		}
		else
		{
		$i = 1;
			foreach ($vars['list'] as $key => $value)
			{
				//print_r($value);
				$html .= '<tr>
						<td>'.$i.'</td>
						<td><a href="#" onclick="bindKpoint(\''.$value['kapsule_id'].'\',\''.$value['displayname'].'\',\''.$value['thumbnail_url'].'\',\''.$value['embed_url'].'\',\''.$value['description'].'\')">'.$value['displayname'].'</a></td>
						<td><img style="width:25%;height:25%;"src="'.str_replace("\/",'/',$value['thumbnail_url']).'"</td>
						<td>'.$value['description'].'</td>
				</tr>';
				$i++;
			}
		}
		$html .= '
		</tbody>
		<tfoot style="display:none;">
		<tr><td colspan="4"><img src="' . JURI::root() . 'images/loading_squares.gif"></td></tr></tfoot>
		</table>
		</div>
		';

		$html = str_replace('\/','',$html);
		$html = str_replace('\t','',$html);
		echo $html;
	}
}
else
{
	if ($vars['error'])
	{
		echo '<div class="alert alert-danger" role="alert">'.$vars['error']['message'].'</div>';
	}
	else
	{
		if ($vars == 1)
		{
			echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">'.JText::_('COM_TJLMS_VIDEO_KPINT_PARAMETER_NOT_FOUND').'</span></div>';
		}
		else
		{
			echo '<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">'.JText::_('COM_TJLMS_VIDEO_KPINT_API_NOT_FOUND').'</div>';
		}
	}
}
?>
