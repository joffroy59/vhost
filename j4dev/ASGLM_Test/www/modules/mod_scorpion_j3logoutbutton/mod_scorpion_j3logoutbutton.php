<?php
/**
 * This module creates a logout button for Joomla 3
 * 
 * @package     Joomla.mod_scorpion_j3logoutbutton
 * @subpackage  Modules
 * @link        http://ScorpionComputers.nl
 * @license     GNU/GPL, see LICENSE.php
 * mod_scorpion_j3logoutbutton is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );
 
$logoutButton = modScorpionJ3LogoutButton::getLogoutButton( $params );
require( JModuleHelper::getLayoutPath( 'mod_scorpion_j3logoutbutton' ) );

?>