<?php

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_smartcountdown3&view=modules'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_SEARCH_IN_TITLE');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_SMARTCOUNTDOWN3_MODULES_SEARCH_IN_TITLE'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clearfix"> </div>
		<div class="clearfix"> </div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th width="1%" style="min-width:55px" class="nowrap center">
							<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th class="title">
							<?php echo JHtml::_('grid.sort', 'COM_SMARTCOUNTDOWN3_MODULES_HEADER_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="30%">
							<?php echo JHtml::_('grid.sort', 'COM_SMARTCOUNTDOWN3_MODULES_HEADER_POSITION', 'a.position', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_HEADER_DEADLINE'); ?>
						</th>
						<th width="1%" class="nowrap center hidden-phone"></th>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.', false, 'cb', $item->publish_up, $item->publish_down); ?>
						</td>
						<td class="nowrap has-context">
						<?php if($user->authorise('core.edit', 'com_modules.module.' . $item->id) && $user->authorise('core.manage', 'com_modules')) : ?>
							<a title="<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODUELS_EDIT_IN_MANAGER'); ?>" href="<?php echo JRoute::_('index.php?option=com_modules&task=module.edit&id=' . (int) $item->id); ?>">
								<?php echo $this->escape($item->title); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						</td>
						<td class="has-context">
							<?php if(!empty($item->position)) : ?>
								<?php echo $this->escape($item->position); ?>
							<?php else : ?>
								---
							<?php endif; ?>
						</td>
						<td class="nowrap has-context">
							<?php if($item->deadline === true) : ?>
								<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PLUGINS_ENABLED'); ?>
							<?php elseif(empty($item->deadline)) : ?>
								<?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_DEADLINE_INVALID'); ?>
							<?php else : ?>
								<?php echo JDate::getInstance($item->deadline)->toSql(); ?>
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<a onclick="window.open('<?php echo JRoute::_('index.php?option=com_smartcountdown3&view=render&tmpl=component&id=' . (int) $item->id, true); ?>', 'newwindow', 'width=600, height=400, left=50, top=50, scrollbars=yes'); return false;" class="btn" href="<?php echo JRoute::_('index.php?option=com_smartcountdown3&view=render&tmpl=component&id=' . (int) $item->id, true); ?>"><?php echo JText::_('COM_SMARTCOUNTDOWN3_MODULES_PREVIEW'); ?></a>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>
