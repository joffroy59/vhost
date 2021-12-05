/**@license boxplus: helper function for Facebook integration
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
			var image = $('img:first', anchor);
			var imageurl = anchor[0].href;
			var titletext = image.size() ? image.attr('alt') : '';
		
			// generate HTML for Facebook Feed button
			var container = $('<div />');
			var button = $('<span><a href="javascript:void(0);">Share on Facebook</a></span>');
			button.click(function () {
				FB.ui({
					method: 'feed',
					link: location.href,
					picture: imageurl,
					caption: titletext,
					description: summarytext
				}, function (response) {});		
			});
			container.append(button);

			// add summary text below Feed button
			if (summarytext) {
				container.append('<div>' + summarytext + '</div>');
			}

			return container;
		}
	}
});
