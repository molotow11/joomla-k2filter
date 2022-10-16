<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$checked = JRequest::getVar('array'.$field->id);
?>

	<div class="k2filter-field-multi-select k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<?php endif; ?>

		<select name="array<?php echo $field->id; ?>[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> placeholder="<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.JText::_($field->name); ?>" placeholder="<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.$field->name; ?>">
				
			<?php if($field->name == "Your field name") : ?>
				<option <?php if (in_array("Option 1", $checked)) {echo 'selected="selected"';} ?>>Option 1</option>';
				<option <?php if (in_array("Option 2", $checked)) {echo 'selected="selected"';} ?>>Option 2</option>';
				<option <?php if (in_array("Option 3", $checked)) {echo 'selected="selected"';} ?>>Option 3</option>';
			<?php endif; ?>
		</select>		
	</div>

