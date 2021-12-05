<?php

defined('_JEXEC') or die;

class SmartCountdown3ModelModules extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'published', 'a.published',
				'position', 'a.position'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__modules AS a');
		
		$query->where('a.module = ' . $db->Quote('mod_smartcountdown3'));

		// Filter by published state.
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.published = ' . (int) $state);
		}
		elseif ($state === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in subject or message.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where('(a.title LIKE ' . $search . ' OR a.position LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.title')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		$test = (string)$query;
		
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		if(empty($items))
		{
			return $items;
		}
		
		foreach($items as &$item)
		{
			// convert params to registry
			$params = new JRegistry();
			$params->loadString($item->params);
			$item->params = $params->toArray();
			
			$enabled_plugins = 0;
			foreach($item->params as $k => $v)
			{
				if(strpos($k, '_enabled') > 0 && $v != 0)
				{
					$enabled_plugins++;
				}
			}
			if($enabled_plugins)
			{
				$item->deadline = true;
			}
			else 
			{
				$item->deadline = !empty($item->params['deadline']) ? $item->params['deadline'] : null;
			}
		}
		
		return $items;
	}
}
