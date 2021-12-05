<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$footerLines = explode(' - ',$lineone);

?>
<div class="footer1<?php echo $moduleclass_sfx; ?>"><?php echo $footerLines[0]; ?></div>
<div class="footer2<?php echo $moduleclass_sfx; ?>"><?php echo $footerLines[1]; ?></div>
<!--div class="footer2<?php echo $moduleclass_sfx; ?>"><?php echo JText::_('MOD_FOOTER_LINE2'); ?></div-->
