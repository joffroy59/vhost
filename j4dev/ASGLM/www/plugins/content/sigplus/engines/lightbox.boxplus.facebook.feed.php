<?php
/**
* @file
* @brief    sigplus Image Gallery Plus boxplus Facebook Feed dialog extension
* @author   Levente Hunyadi
* @version  1.4.2
* @remarks  Copyright (C) 2009-2011 Levente Hunyadi
* @remarks  Licensed under GNU/GPLv3, see http://www.gnu.org/licenses/gpl-3.0.html
* @see      http://hunyadi.info.hu/projects/sigplus
*/

// Make sure you fill in your Facebook App ID below or some Facebook functionality will not work.

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
* Support class for jQuery-based boxplus lightweight pop-up window engine with Facebook support.
* @see http://hunyadi.info.hu/projects/boxplus/
*/
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'lightbox.boxplus.php';

class SIGPlusBoxPlusFacebookFeedEngine extends SIGPlusBoxPlusEngine {
	public function __construct($params = false) {
		parent::__construct($params);
	}

	protected function getDescriptionFunction() {
		return 'boxplusFacebookCaption';
	}

	public function addScripts($galleryid, SIGPlusGalleryParameters $params) {
		parent::addScripts($galleryid, $params);
		$this->addScript('/plugins/content/sigplus/engines/boxplus/popup/js/boxplus.facebook.feed.js');

		// set Facebook Application ID
		$facebookappid = '';  // MANDATORY: add your (typically 15-digit) Facebook App ID in between the quotes

		// set Facebook language
		$language = JFactory::getLanguage();
		$languagecode = str_replace('-', '_', $language->getTag());

		// make sure Facebook Application ID is set
		if (!$facebookappid) {
			throw new SIGPlusNotSupportedException();
		}

		// load Facebook SDK scripts and initialize the SDK
		$script = '<script>'.PHP_EOL.
			// initialize Facebook SDK when it has been loaded
			'window.fbAsyncInit = function() {'.PHP_EOL.
			'FB.init({'.PHP_EOL.
			'appId: "'.$facebookappid.'",'.PHP_EOL.
			'xfbml: false,'.PHP_EOL.
			'version: "v2.1"'.PHP_EOL.
			'});'.PHP_EOL.
			'};'.PHP_EOL.

			// load Facebook SDK
			'(function(d, s, id) {'.PHP_EOL.
			'var js, fjs = d.getElementsByTagName(s)[0];'.PHP_EOL.
			'if (d.getElementById(id)) {return;}'.PHP_EOL.
			'js = d.createElement(s); js.id = id;'.PHP_EOL.
			'js.src = "//connect.facebook.net/'.$languagecode.'/sdk.js";'.PHP_EOL.
			'fjs.parentNode.insertBefore(js, fjs);'.PHP_EOL.
			'}(document, "script", "facebook-jssdk"));'.PHP_EOL.
		'</script>';
		$this->addCustomTag($script);
	}
}