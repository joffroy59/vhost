<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.4.7
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined ( '_JEXEC' ) or die ();

$layout = $params->get('layout_config', array());

$module_DOM_id = $options['id'];

// Add dynamic styles for units spacing. We use module ID to apply styles to correct module
// instance if there are various counters on the same page.
$margin_horz = $layout['units_spacing_horz'] != '' ? $layout['units_spacing_horz'] :  '0 0.8em';
$margin_vert = $layout['units_spacing_vert'] != '' ? $layout['units_spacing_vert'] :  '0.5em 0';
$margin_styles = "
	#{$module_DOM_id} .scd-unit-horz {
		margin: {$margin_horz};
	}
	#{$module_DOM_id} .scd-unit-vert {
		margin: {$margin_vert};
	}
";
JFactory::getDocument()->addStyleDeclaration($margin_styles);
?>
<div class="smartcountdown" id="<?php echo $module_DOM_id; ?>">
	<div id="<?php echo $module_DOM_id ?>-loading" class="spinner"></div>
	<div class="scd-all-wrapper"<?php echo $layout['module_style']; ?>>
		<div class="<?php echo $layout['text_class']; ?>" id="<?php echo $module_DOM_id; ?>-title-before"<?php echo $layout['title_before_style']; ?>></div>
		<div class="<?php echo ($layout['counter_class']); ?>">
			<?php foreach(modSmartCountdown3Helper::$assets as $asset) : ?>
				<div id="<?php echo $module_DOM_id; ?>-<?php echo $asset; ?>" class="<?php echo $layout['units_class']; ?>"<?php echo (!empty($options['units'][$asset]) ? '' : ' style="display:none;"'); ?>>
				<?php if($options['labels_pos'] == 'left' || $options['labels_pos'] == 'top') : ?>
					<div class="<?php echo $layout['labels_class']; ?>" id="<?php echo $module_DOM_id; ?>-<?php echo $asset; ?>-label"<?php echo $layout['labels_style']; ?>></div>
					<div class="<?php echo $layout['digits_class']; ?>" id="<?php echo $module_DOM_id; ?>-<?php echo $asset; ?>-digits"<?php echo $layout['digits_style']; ?>></div>
				<?php else : ?>
					<div class="<?php echo $layout['digits_class']; ?>" id="<?php echo $module_DOM_id; ?>-<?php echo $asset; ?>-digits"<?php echo $layout['digits_style']; ?>></div>
					<div class="<?php echo $layout['labels_class']; ?>" id="<?php echo $module_DOM_id; ?>-<?php echo $asset; ?>-label"<?php echo $layout['labels_style']; ?>></div>
				<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="<?php echo $layout['text_class']; ?>" id="<?php echo $module_DOM_id; ?>-title-after"<?php echo $layout['title_after_style']; ?>></div>
	</div>
</div>
<div class="clearfix"></div>