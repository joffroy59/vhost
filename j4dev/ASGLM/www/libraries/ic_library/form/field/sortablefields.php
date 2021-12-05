<?php
/**
 *------------------------------------------------------------------------------
 *  iC Library - Library by Jooml!C, for Joomla!
 *------------------------------------------------------------------------------
 * @package     iC Library
 * @subpackage  Fields
 * @copyright   Copyright (c)2013-2019 Cyril Rezé, Jooml!C - All rights reserved
 *
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      Cyril Rezé (Lyr!C)
 * @link        http://www.joomlic.com
 *
 * @version     1.4.4 2017-07-02
 * @since       1.4.0
 *------------------------------------------------------------------------------
*/

// No direct access to this file
defined('_JEXEC') or die();

// Joomla 2.5
if (version_compare(JVERSION, '3.0', 'lt'))
{
	jimport('joomla.form.formfield');
}

JFormHelper::loadFieldClass('list');


/**
 * Sortable fields form field
 */
class iCFormFieldSortableFields extends JFormFieldList
{
	public $type = 'sortablefields';

	public function getLabel()
	{
		$app        = JFactory::getApplication();
		$document   = JFactory::getDocument();

		$isMenu     = ($app->input->get('option') == 'com_menus');
		$menuParams = $isMenu ? 'params_' : '';

		// Joomla 2.5
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			$level = E_ALL & ~E_NOTICE & ~E_DEPRECATED;

			// Remove not-error message (only needed for Joomla 2.5) :
			// Strict Standards: Only variables should be assigned by reference in .../libraries/joomla/form/form.php
			if (version_compare(PHP_VERSION, '5.4.0-dev', '>='))
			{
				if ( ! defined('E_STRICT'))
				{
					define('E_STRICT', 2048);
				}

				$level &= ~E_STRICT;
			}

			error_reporting($level);

			if ($isMenu)
			{
				return false;
			}
		}

		if (empty($this->element['label']) && empty($this->element['description']))
		{
			return '';
		}

		$title			= $this->element['label']
						? (string) $this->element['label']
						: ($this->element['title'] ? (string) $this->element['title'] : '');
		$heading		= $this->element['heading'] ? (string) $this->element['heading'] : 'h4';
		$description	= (string) $this->element['description'];
		$class			= ! empty($this->class)
						? ' class="' . $this->class . '"'
						: ' class="alert alert-info input-xxlarge" style="display:block;clear:both;"';
		$close			= (string) $this->element['close'];

		$html = array();

		if ($close)
		{
			$close = $close == 'true' ? 'alert' : $close;
			$html[] = '<button type="button" class="close" data-dismiss="' . $close . '">&times;</button>';
		}

		$html[] = '<div class="ic-clearfix"></div>';
		$html[] = '<div' . $class . '>';
//		$html[] = !empty($title) ? '<' . $heading . '>' . JText::_($title) . '</' . $heading . '>' : '';
		$html[] = ! empty($description) ? JText::_($description) : '';
		$html[] = '</div>';

		$html[] = '<div id="ic-sortable" class="span6" style="margin-bottom: 24px">';

		$fieldnames = isset($this->value) ? $this->value : (string) $this->element['default'];
		$fieldnames = explode(',', $fieldnames);

		$group      = $isMenu ? 'params' : '';

		foreach ($fieldnames AS $k => $fieldname)
		{
			$fieldname = trim($fieldname);
			$field  = $this->form->getField($fieldname, $group);

			// Make sure the selected field is hidden
			if ( ! isset($field->element['type']) || $field->element['type'] !== 'hidden')
			{
				$this->form->setFieldAttribute($fieldname, 'type', 'hidden');
			}

			// Settings attributes
			$name = isset($field->element['name']) ? $field->element['name'] : false;

			if ($name)
			{
				$rendertype		= isset($field->element['rendertype'])	? $field->element['rendertype']		: 'text';
				$label			= isset($field->element['label'])		? $field->element['label']			: '';
				$description	= isset($field->element['description'])	? $field->element['description']	: '';
				$class			= isset($field->element['class'])		? $field->element['class']			: '';
				$labelclass		= isset($field->element['labelclass'])	? $field->element['labelclass']		: '';
				$default		= isset($field->element['default'])		? $field->element['default']		: '';

				// Add new field to the sortable list
				$type_field = new SimpleXMLElement('<field />');
				$type_field->addAttribute('name', $fieldname);
				$type_field->addAttribute('type', $rendertype);
				$type_field->addAttribute('label', $label);
				$type_field->addAttribute('description', $description);
				$type_field->addAttribute('class', $class);
				$type_field->addAttribute('labelclass', $labelclass);
				$type_field->addAttribute('default', $default);
//				$type_field->addAttribute('fieldset', 'list');

				if (isset($field->element->option))
				{
					$values = (array) $field->element->xpath('option');
					$options = (array) $field->element->option;
					unset($values['@attributes']);
					unset($options['@attributes']);


					// Add 'Use Global' with value
					if ($field->element['useglobal'])
					{
						$tmp        = new stdClass;
						$tmp->value = '';
						$tmp->text  = JText::_('JGLOBAL_USE_GLOBAL');
						$component  = JFactory::getApplication()->input->getCmd('option');

						// Get correct component for menu items
						if ($component == 'com_menus')
						{
							$link      = $this->form->getData()->get('link');
							$uri       = new JUri($link);
							$component = $uri->getVar('option', 'com_menus');
						}

						$params = JComponentHelper::getParams($component);
						$value  = $params->get($fieldname);

						// Try with global configuration
						if (is_null($value))
						{
							$value = JFactory::getConfig()->get($fieldname);
						}

						// Try with menu configuration
						if (is_null($value) && JFactory::getApplication()->input->getCmd('option') == 'com_menus')
						{
							$value = JComponentHelper::getParams('com_menus')->get($fieldname);
						}

						if (!is_null($value))
						{
							$value = (string) $value;

							foreach ($values as $key => $option)
							{
								if ((string) $values[$key]['value'] === $value)
								{
									$value = JText::_($option);

									break;
								}
							}

							$tmp->text  = (version_compare(JVERSION, '3.7', 'lt'))
										? JText::_('JGLOBAL_USE_GLOBAL') . ' (' . $value . ')'
										: JText::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
						}

						$child = $type_field->addChild('option', $tmp->text);
						$child->addAttribute('value', $tmp->value);
					}


					// Add Options
					foreach ($values as $key => $option)
					{
						$child = $type_field->addChild('option', $option);
						$child->addAttribute('value', $values[$key]['value']);
					}
				}

				$this->form->setField($type_field, $group);

				$label = $this->form->getLabel($fieldname, $group);
				$input = $this->form->getInput($fieldname, $group);

				$html[] = '<div id="' . $fieldname . '" class="ui-state-default">';

				// Switch Joomla 2.5 / 3
				if (version_compare(JVERSION, '3.0', 'lt'))
				{
					$html[] = '<span style="line-height: 36px">&nbsp;' . $label . $input . '</span>';
				}
				else
				{
					$html[] = '<span class="icon-move" style="line-height: 36px; float: left"> </span>';
					$html[] = JLayoutHelper::render('joomla.form.renderfield',
								array('input' => $input, 'label' => $label, 'options' => array('rel' => ''))); // 'rel' kept for older J3 version (3.2.5...)
				}

				$html[] = '</div>';

				$label_suffix = '';

				if ($fieldname == 'filter_search')
				{
					$label_suffix = Jtext::_('IC_FIELD_TYPE_TEXT');
				}
				elseif (in_array($fieldname, array('filter_from', 'filter_to')))
				{
					$label_suffix = Jtext::_('IC_FIELD_TYPE_CALENDAR');
				}
				elseif (in_array($fieldname, array('filter_category', 'filter_month', 'filter_year')))
				{
					$label_suffix = Jtext::_('IC_FIELD_TYPE_LIST');
				}

				$document->addScriptDeclaration('
					jQuery(document).ready(function($) {
						var label = $("#jform_' . $menuParams . $fieldname . '-lbl").text();
						$("#jform_' . $menuParams . $fieldname . '-lbl").html("<strong>"+label+"</strong> <small>(' . $label_suffix . ')</small>");
					});
				');
			}
		}

		$html[] = '</div>';

		$document->addScriptDeclaration('
			jQuery(document).ready(function($) {
				$( "#ic-sortable" ).sortable({
					placeholder: "ui-state-highlight",
					cursor: "crosshair",
					update: function(event, ui) {
						var order = $("#ic-sortable").sortable("toArray");
						$("#' . $this->id . '").val(order.join(","));
					}
				});
				$( "#ic-sortable" ).disableSelection();

				// Will hide control-group field, if input type is hidden
//				$("input[type=hidden]").parents(".control-group").css("margin-bottom", "0");
			});
		');

		$document->addStyleDeclaration('
			.ui-state-default {
				background: #f7f7f9;
				border: 1px solid #e1e1e8;
				padding: 3px 5px;
				margin-bottom: 3px;
			}
			.ui-sortable-helper{
				/*
				background: #449d44;
				background: rgba(68, 157, 68, 0.5);
				*/
				/* @brand-info BT3 */
				background; #5bc0de;
				background: rgba(91, 192, 222, 0.5);

				border: none;
				color: #fff;
				padding: 3px 5px;
				margin-bottom: 3px;
			}
			.ui-state-default .control-group {
				margin-bottom: 5px;
			}
			.icon-move:hover,
			.ui-state-default:hover {
				cursor: move;
			}
			.ui-state-default .control-label:hover {
				cursor: move;
			}
			.ui-state-highlight {
				/*
				background: #449d44;
				background: rgba(68, 157, 68, 0.7);
				border: 1px dotted #398439;
				*/
				/* @brand-info BT3 */
				background; #5bc0de;
				background: rgba(91, 192, 222, 0.5);
				border: 1px dotted #2aa8ce;

				height: 44px;
				margin-bottom: 3px;
			}
		');
//		JHtml::_('bootstrap.framework');
//		JHtml::_('jquery.framework');

		// Change jQuery UI version from 1.9.2 to 1.8.23 to prevent a conflict in tooltip that appeared since Joomla 3.1.4
//		$document->addScript('https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js');
//		JHtml::stylesheet( 'com_icagenda/jquery-ui-1.8.17.custom.css', false, true );

		// Joomla 2.5
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			JHtml::stylesheet( 'com_icagenda/jquery-ui-1.8.17.custom.css', false, true );
			$document->addScript('//code.jquery.com/ui/1.11.4/jquery-ui.js');
		}
		else
		{
			JHtml::_('jquery.ui', array('core', 'sortable'));
		}

		return '</div><div>' . implode('', $html);
	}

	public function getInput()
	{
//		return '';
		return '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value . '" />';
	}
}
