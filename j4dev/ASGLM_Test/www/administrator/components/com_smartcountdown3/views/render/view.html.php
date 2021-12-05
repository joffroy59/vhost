<?php

defined('_JEXEC') or die;

class SmartCountdown3ViewRender extends JViewLegacy
{
	protected $module;
	
	public function display($tpl = null)
	{
		JFactory::getLanguage()->load('mod_smartcountdown3', JPATH_SITE . '/modules/mod_smartcountdown3');
		
		$app = JFactory::getApplication();
	
		$id = $app->input->getInt('id', 0);
		
		$db = JFactory::getDbo();
		$db->setQuery('SELECT a.* FROM #__modules AS a WHERE a.id = ' . $id);
		$this->module = $db->loadObject();
		
		/* Looks that we do not need this
		$this->module->publish_up = $this->module->publish_down = $db->getNullDate();
		$this->module->published = 1;
		$this->module->access = 1;
		*/
		
		parent::display($tpl);
	}
}