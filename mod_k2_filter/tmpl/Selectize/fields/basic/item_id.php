<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-text">
	
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_ITEM_ID'); ?>
		</h3>
		
		<input class="inputbox" name="fitem_id" type="text" <?php if (JRequest::getVar('fitem_id')) echo ' value="'.JRequest::getVar('fitem_id').'"'; ?> />
	</div>

