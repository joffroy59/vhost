<?php
/**
 * @package Smart Countdown 3 AJAX server for Joomla! 3.0
 * @version 3.2
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_smartcountdown3')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller	= JControllerLegacy::getInstance('smartCountdown3');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
