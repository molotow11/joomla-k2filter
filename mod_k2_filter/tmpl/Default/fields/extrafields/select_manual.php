<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-select">
		
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<?php endif; ?>
		
		<select name="searchword<?php echo $field->id; ?>" <?php if($onchange) : ?>onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
			<option value=""><?php echo '-- '.JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.$field->name.' --'; ?></option>
			<?php if($field->name == "Your field name") : ?>
				<option <?php if (JRequest::getVar('searchword'.$field->id) == "Option 1") {echo 'selected="selected"';} ?>>Option 1</option>';
				<option <?php if (JRequest::getVar('searchword'.$field->id) == "Option 2") {echo 'selected="selected"';} ?>>Option 2</option>';
				<option <?php if (JRequest::getVar('searchword'.$field->id) == "Option 3") {echo 'selected="selected"';} ?>>Option 3</option>';
			<?php endif; ?>
		</select>
	</div>
    


