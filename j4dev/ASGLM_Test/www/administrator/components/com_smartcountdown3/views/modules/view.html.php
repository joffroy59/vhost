<?php

defined('_JEXEC') or die;
JHtml::_('behavior.modal');

class SmartCountdown3ViewModules extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}
		
		smartCountdown3Helper::addSubmenu('modules');

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= JHelperContent::getActions('com_smartcountdown3');

		JToolbarHelper::title(JText::_('COM_SMARTCOUNTDOWN3_MANAGER_MODULES'), 'scd-manager-moduels');
		
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_smartcountdown3');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_smartcountdown3&view=modules');
		
		JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_state',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => 0, "all" => 0)), 'value', 'text', $this->state->get('filter.state'), true)
		);
		
	}
	
	protected function getSortFields()
	{
		return array(
				'a.id' => JText::_('JGRID_HEADING_ID'),
				'a.published' => JText::_('JSTATUS'),
				'a.title' => JText::_('JGLOBAL_TITLE'),
				'a.position' => JText::_('COM_SMARTCOUNTDOWN3_MODULES_SORT_POSITION')
		);
	}
}
