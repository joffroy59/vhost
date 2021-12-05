<?php
/**
 *----------------------------------------------------------------------------
 * iCagenda     Plugin Action Log
 *----------------------------------------------------------------------------
 * @version     1.0 2018-10-30
 *
 * @package     iCagenda.Plugin
 * @subpackage  Actionlog.icagenda
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
 * @since       iCagenda 3.7.5
 *----------------------------------------------------------------------------
*/

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');

/**
 * iCagenda Actions Logging Plugin.
 */
class PlgActionlogIcagenda extends JPlugin
{
	/**
	 * Array of loggable extensions.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $loggableExtensions = array();

	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$params = ComponentHelper::getComponent('com_actionlogs')->getParams();

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select($db->quoteName('extension'));
		$query->from($db->quoteName('#__action_logs_extensions'));
		$query->where($db->quoteName('extension') . ' = '. $db->quote('com_icagenda'));

		$db->setQuery($query);

		$result = $db->loadResult();

		if ( ! $result)
		{
			$extension = new stdClass();
			$extension->extension = 'com_icagenda';

			// Insert iCagenda (com_icagenda) into the action logs extensions table.
			$return = $db->insertObject('#__action_logs_extensions', $extension);
		}

		$this->loggableExtensions = $params->get('loggable_extensions', array());
	}

	/**
	 * After save content logging method
	 * This method adds a record to #__action_logs contains (message, date, context, user)
	 * Method is called right after the content is saved
	 *
	 * @param   string   $context  The context of the content passed to the plugin
	 * @param   object   $article  A JTableContent object
	 * @param   boolean  $isNew    If the content is just about to be created
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onContentAfterSave($context, $item, $isNew)
	{
		$option = $this->app->input->getCmd('option');

		if (!$this->checkLoggable($option))
		{
			return;
		}

		$params = ActionlogsHelper::getLogContentTypeParams($context);

		// If found a valid content type, don't process further
		if ($params !== null)
		{
			return;
		}

		$user = JFactory::getUser();

		list(, $contentType) = explode('.', $context);

		if ($isNew)
		{
			$messageLanguageKey = strtoupper('PLG_ACTIONLOG_ICAGENDA_' . $contentType . '_ADDED');
			$defaultLanguageKey = strtoupper('PLG_SYSTEM_ACTIONLOGS_CONTENT_ADDED');

			$action = 'add';
		}
		else
		{
			$messageLanguageKey = strtoupper('PLG_ACTIONLOG_ICAGENDA_' . $contentType . '_UPDATED');
			$defaultLanguageKey = strtoupper('PLG_SYSTEM_ACTIONLOGS_CONTENT_UPDATED');

			$action = 'update';
		}

		// If the content type doesn't has it own language key, use default language key
		if (!JFactory::getLanguage()->hasKey($messageLanguageKey))
		{
			$messageLanguageKey = $defaultLanguageKey;
		}

		$title = array(
			'event'        => 'title',
			'category'     => 'title',
			'registration' => 'name',
			'feature'      => 'title',
			'customfield'  => 'title',
		);

		$message = array(
			'action'      => $action,
			'type'        => strtoupper('PLG_ACTIONLOG_ICAGENDA_TYPE_' . $contentType),
			'id'          => $item->get('id'),
			'title'       => $item->get('title', $item->get($title[$contentType])),
			'itemlink'    => ActionlogsHelper::getContentTypeLink($option, $contentType, $item->get('id')),
			'userid'      => $user->id,
			'username'    => $user->username,
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $user->id,
			'app'         => strtoupper('PLG_ACTIONLOG_ICAGENDA_APPLICATION_' . $this->app->getName()),
		);

		$this->addLog(array($message), $messageLanguageKey, $context);
	}

	/**
	 * Proxy for ActionlogsModelUserlog addLog method
	 *
	 * This method adds a record to #__action_logs contains (message_language_key, message, date, context, user)
	 *
	 * @param   array   $messages            The contents of the messages to be logged
	 * @param   string  $messageLanguageKey  The language key of the message
	 * @param   string  $context             The context of the content passed to the plugin
	 * @param   int     $userId              ID of user perform the action, usually ID of current logged in user
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		JLoader::register('ActionlogsModelActionlog', JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php');

		/* @var ActionlogsModelActionlog $model */
		$model = JModelLegacy::getInstance('Actionlog', 'ActionlogsModel');
		$model->addLog($messages, $messageLanguageKey, $context, $userId);
	}

	/**
	 * Function to check if a component is loggable or not
	 *
	 * @param   string  $extension  The extension that triggered the event
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	protected function checkLoggable($extension)
	{
		return in_array($extension, $this->loggableExtensions);
	}
}
