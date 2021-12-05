<?php
/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - A powerful social content platform to manage multiple social networks.
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2007-2019 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        https://www.extly.com
 */

define('EXTLY_CRONJOB_RUNNING', true);
define('AUTOTWEET_CRONJOB_RUNNING', true);

/**
 * starts the AutoTweet cronjob
 * Call this file form crontab.
 **/

// Make sure we're being called from the command line, not a web interface
if (array_key_exists('REQUEST_METHOD', $_SERVER))
{
	die();
}

// Not included in this membership
