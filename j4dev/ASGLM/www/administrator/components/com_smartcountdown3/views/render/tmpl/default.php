<?php

defined('_JEXEC') or die;

?>
	<div class="clearfix" style="padding:1em;background-color:#eee;">
		<h3>
			<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW_LABEL'); ?>
		</h3>
		<p>
			<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW_DESC'); ?>
		</p>
		<div class="pull-right">
			<a class="btn btn-success" href="#" onclick="window.location.reload();return false;"><?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW_REFRESH'); ?></a>
			<a class="btn btn-danger" href="#" onclick="window.close();return false;"><?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW_CLOSE'); ?></a>
		</div>
	</div>
	
<?php
if(empty($this->module))
{
	echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW_ERROR');
}
else
{
	$params = new JRegistry;
	$params->loadString($this->module->params);
	
	// disable automatic redirection related options (they will not work in admin preview)
	$params->set('counter_clickable', 0);
	$params->set('event_goto_menu', 0);
	$params->set('event_goto_url', '');
	
	$module = $this->module;
	$path = JPATH_SITE . '/modules/mod_smartcountdown3/mod_smartcountdown3.php';
	$is_admin_preview = true;
	include $path;
}
?>
