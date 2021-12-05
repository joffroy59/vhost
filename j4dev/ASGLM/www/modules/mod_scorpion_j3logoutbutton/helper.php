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
defined('_JEXEC') or die('Restricted access');

class modScorpionJ3LogoutButton
{
    /**
     * Retrieves the hello message
     *
     * @param array $params An object containing the module parameters
     * @access public
     */    
    public static function getLogoutButton( $params )
    {
		$myToken = JSession::getFormToken();
		$output = '<a href="index.php?option=com_users&task=user.logout&'. $myToken .'=1" class="'. $params->get( 'class4link' ) .'"><input  type="button" name="Submit" class="button '. $params->get( 'class4btn' ) .'" value="Logout"></a>';
		return $output;
    }
}
?>