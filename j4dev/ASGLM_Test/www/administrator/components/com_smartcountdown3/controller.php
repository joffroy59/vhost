<?php

// no direct access
defined('_JEXEC') or die;

class smartCountdown3Controller extends JControllerLegacy
{
	protected $default_view = 'modules';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/smartcountdown3.php';

		$view   = $this->input->get('view', 'modules');
		$layout = $this->input->get('layout', 'default');
		
		parent::display();
	}
}
