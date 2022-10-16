<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-text">
	
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_ITEM_TEXT'); ?>
		</h3>
		
		<input class="inputbox" name="ftext" type="text" <?php if (JRequest::getVar('ftext')) echo ' value="'.JRequest::getVar('ftext').'"'; ?> />
	</div>

