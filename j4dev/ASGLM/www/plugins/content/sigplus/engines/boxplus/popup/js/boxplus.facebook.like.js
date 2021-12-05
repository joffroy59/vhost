/**@license boxplus: helper function for Facebook Like button
 * @author  Levente Hunyadi
 * @version 1.4.2
 * @remarks Copyright (C) 2009-2010 Levente Hunyadi
 * @remarks Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
 * @see     http://hunyadi.info.hu/projects/boxplus
 **/

__jQuery__(function ($) {
	window.boxplusFacebookCaption = function (anchor, sizing) {
		var summarynode = $("#" + $("img", anchor).attr("id") + "_summary");  // get summary node
		var summarytext = summarynode.size() ? summarynode.html() : anchor.attr("title");
		if (sizing) {
			return summarytext;
		} else {
			// URL to link with Facebook Like button
			var url = anchor[0].href;

			// generate HTML for Facebook Like button
			var html = '<iframe src="//www.facebook.com/plugins/like.php?locale='+ window.boxplusFacebookLanguageCode +'&amp;href='+ escape(url) +'&amp;layout=standard&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:35px;" allowTransparency="true"></iframe>';
			
			// add summary text below Like button
			if (summarytext) {
				html += '<div>' + summarytext + '</div>';
			}

			return html;
		}
	}
});
