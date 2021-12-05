<?php
/**
 *------------------------------------------------------------------------------
 *  iC Library - Library by Jooml!C, for Joomla!
 *------------------------------------------------------------------------------
 * @package     iC Library
 * @subpackage  render
 * @copyright   Copyright (c)2013-2019 Cyril Rezé, Jooml!C - All rights reserved
 *
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      Cyril Rezé (Lyr!C)
 * @link        http://www.joomlic.com
 *
 * @version     1.4.0 2016-05-12
 * @since       1.4.0
 *------------------------------------------------------------------------------
*/

// No direct access to this file
defined('_JEXEC') or die();

/**
 * class iCUrl
 */
class iCRender
{
	/**
	 * Function to return Url TAG
	 *
	 * @since	1.4.0
	 */
	static public function urlTag($url, $target = null, $nofollow = true)
	{
		$target		= $target ? $target : '_blank';
		$nofollow	= $nofollow ? ' rel="nofollow"' : '';

		$link		= iCUrl::urlParsed($url, 'scheme');

		return '<a href="' . $link . '"' . $nofollow . ' target="' . $target . '">' . $url . '</a>';
	}
}
