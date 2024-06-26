<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		3.2.1 March 18, 2011
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureToTop extends GantryFeature {
    var $_feature_name = 'totop';
	
	function init() {
		global $gantry;
		JHTML::_('behavior.framework', true);
		
		if ($this->get('enabled')) {
			$gantry->addScript('gantry-totop.js');
		}
	}
	
	function getPosition(){
		return "totop";
	}
	
}