<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-text">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<input class="inputbox" name="searchword<?php echo $field->id;?>" type="text" <?php if (JRequest::getVar('searchword').$field->id) echo ' value="'.JRequest::getVar('searchword'.$field->id).'"'; ?> />
	</div>

