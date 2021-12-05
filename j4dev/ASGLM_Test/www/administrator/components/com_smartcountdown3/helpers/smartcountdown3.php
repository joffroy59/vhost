<?php

// No direct access
defined('_JEXEC') or die;


class smartCountdown3Helper
{
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_SMARTCOUNTDOWN3_SUBMENU_MODULES'),
				'index.php?option=com_smartcountdown3&view=modules',
			$vName == 'modules'
		);
	}
}
