<?php
/**
 *----------------------------------------------------------------------------
 * iCagenda     Plugin Quick Icon - iCagenda Update Notification
 *----------------------------------------------------------------------------
 * @version     1.2 2018-04-11
 *
 * @package     iCagenda.Plugin
 * @subpackage  Quickicon.icagendaupdate
 * @link        https://icagenda.joomlic.com
 *
 * @author      Cyril Rezé
 * @copyright   (c) 2012-2019 Jooml!C / Cyril Rezé. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @since       iCagenda 3.5.16
 *----------------------------------------------------------------------------
*/

defined('_JEXEC') or die;

/**
 * iCagenda Quick Icon update notification plugin
 */
class PlgQuickiconiCagendaupdate extends JPlugin
{
	/**
	 * Constructor (ONLY NEEDED FOR JOOMLA 2.5)
	 *
	 * @param  object  $subject The object to observe
	 * @param  array   $config  An array that holds the plugin configuration
	 *
	 * @since  1.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param  string  $context  The calling context
	 *
	 * @return array  A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since  1.0
	 */
	public function onGetIcons($context)
	{
		$UTILITIES_DIR = is_dir(JPATH_ADMINISTRATOR . '/components/com_icagenda/utilities');

		if ($context != $this->params->get('context', 'mod_quickicon')
			|| ! JFactory::getUser()->authorise('core.manage', 'com_installer')
			|| ! $UTILITIES_DIR
			)
		{
			return;
		}

		// Load Vector iCicons Font
		JHtml::stylesheet( 'media/com_icagenda/icicons/style.css' );

		// Load iCagenda Live Update
		require_once JPATH_ADMINISTRATOR . '/components/com_icagenda/liveupdate/liveupdate.php';

		$updateInfo = LiveUpdate::getUpdateInformation();

		// Initialize the array of button options
		$button = array();

		// Not supported
		if ( ! $updateInfo->supported)
		{
			$button['text'] = JText::_('PLG_QUICKICON_ICAGENDAUPDATE_LIVEUPDATE')
							. ' <span class="label label-warning">' . JText::_('PLG_QUICKICON_ICAGENDAUPDATE_NOT_SUPPORTED') . '</span>';
			$button['icon'] = version_compare(JVERSION, '3.0', 'ge') ? 'iclogo' : JUri::root() . 'media/com_icagenda/images/logo_joomlic.png';
		}

		// Crash
		elseif ($updateInfo->stuck)
		{
			$button['text'] = JText::_('PLG_QUICKICON_ICAGENDAUPDATE_LIVEUPDATE')
							. ' <span class="label label-important">' . JText::_('PLG_QUICKICON_ICAGENDAUPDATE_CRASHED') . '</span>';
			$button['icon'] = version_compare(JVERSION, '3.0', 'ge') ? 'iclogo' : JUri::root() . 'media/com_icagenda/images/logo_joomlic.png';
		}

		// Has updates
		elseif ($updateInfo->hasUpdates)
		{
			$button['text'] = '<strong>' . JText::sprintf('PLG_QUICKICON_ICAGENDAUPDATE_UPDATEFOUND', $updateInfo->version) . '</strong>';
			$button['icon'] = version_compare(JVERSION, '3.0', 'ge') ? 'iclogo' : JUri::root() . 'media/com_icagenda/images/logo_joomlic.png';
		}

		// Already in the latest release
		else
		{
			$button['text'] = JText::_('PLG_QUICKICON_ICAGENDAUPDATE_UPTODATE');
			$button['icon'] = version_compare(JVERSION, '3.0', 'ge') ? 'iclogo' : JUri::root() . 'media/com_icagenda/images/logo_joomlic.png';
		}

		$token = JSession::getFormToken() . '=' . 1;

		// Joomla 3 only
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			$script = array();
			$script[]= 'jQuery(document).ready(function(){';
			$script[]= '	jQuery(".icon-iclogo").addClass("iCicon-iclogo").removeClass("icon-iclogo");';
			$script[]= '	jQuery(".iCicon-iclogo").css("margin-right", "9px");';

			if ($updateInfo->hasUpdates)
			{
				$script[]= '	jQuery("#system-message-container").prepend("'
						. '<div class=\"alert alert-warning\"><center><strong>'
						. JText::sprintf('PLG_QUICKICON_ICAGENDAUPDATE_UPDATEFOUND_MESSAGE', $updateInfo->version)
						. ' <button class=\"btn btn-primary\" onclick=\"document.location=\''
						. JUri::base() . 'index.php?option=com_installer&view=update&task=update.find&' . $token . '\';\">'
						. JText::_('PLG_QUICKICON_ICAGENDAUPDATE_UPDATEFOUND_BUTTON') . '</button>'
						. '</strong></center></div>");';
			}

			$script[]= '});';

			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			$link = 'index.php?option=com_installer&view=update&task=update.find&' . $token;
		}
		// Joomla 2.5
		else
		{
			$link = 'index.php?option=com_icagenda&view=liveupdate';
		}

		return array(
			array(
				'link'  => $link,
				'image'	=> $button['icon'],
				'icon'	=> 'header/icon-48-download.png',
				'text'	=> $button['text'],
				'id'	=> 'plg_quickicon_icagendaupdate',
				'group'	=> 'MOD_QUICKICON_MAINTENANCE'
			)
		);
	}
}
