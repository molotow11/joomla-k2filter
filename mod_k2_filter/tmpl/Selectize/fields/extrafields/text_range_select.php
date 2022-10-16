<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-text-range select">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<select name="searchword<?php echo $field->id;?>-from" type="text" style="width: 100px;">
			<option value="">From</option>
			<option value="1" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "1") echo 'selected="selected"'; ?>>1</option>
			<option value="2" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "2") echo 'selected="selected"'; ?>>2</option>
			<option value="3" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "3") echo 'selected="selected"'; ?>>3</option>
			<option value="4" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "4") echo 'selected="selected"'; ?>>4</option>
			<option value="5" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "5") echo 'selected="selected"'; ?>>5</option>
			<option value="6" <?php if (JRequest::getVar('searchword'.$field->id.'-from') == "6") echo 'selected="selected"'; ?>>6</option>
		</select>
		-
		<select name="searchword<?php echo $field->id;?>-to" type="text" style="width: 100px;">
			<option value="">To</option>
			<option value="1" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "1") echo 'selected="selected"'; ?>>1</option>
			<option value="2" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "2") echo 'selected="selected"'; ?>>2</option>
			<option value="3" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "3") echo 'selected="selected"'; ?>>3</option>
			<option value="4" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "4") echo 'selected="selected"'; ?>>4</option>
			<option value="5" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "5") echo 'selected="selected"'; ?>>5</option>
			<option value="6" <?php if (JRequest::getVar('searchword'.$field->id.'-to') == "6") echo 'selected="selected"'; ?>>6</option>>
		</select>
	</div>
