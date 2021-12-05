<?php
/**
 *------------------------------------------------------------------------------
 *  iC Library - Library by Jooml!C, for Joomla!
 *------------------------------------------------------------------------------
 * @package     iC Library
 * @subpackage  Form Rules
 * @copyright   Copyright (c)2013-2019 Cyril Rezé, Jooml!C - All rights reserved
 *
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      Cyril Rezé (Lyr!C)
 * @link        http://www.joomlic.com
 *
 * @version     1.4.4 2017-06-06
 * @since       1.4.2
 *------------------------------------------------------------------------------
*/

// No direct access to this file
defined('_JEXEC') or die();

if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.registry.registry');
}

use Joomla\Registry\Registry;

/**
 * Form Rule class for iCagenda.
 *
 * Positive integer validation.
 */
class iCFormRulePositiveinteger extends JFormRule
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var     string
	 * @since   1.4.2
	 */
	protected $regex = '^[1-9][0-9]*$';
}
