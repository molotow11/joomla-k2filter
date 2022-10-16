<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-price-range">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_PRICE'); ?>
		</h3>
		
		<input class="inputbox" style="width: 40%;" name="price-from" type="text" <?php if (JRequest::getVar('price-from')) echo ' value="'.JRequest::getVar('price-from').'"'; ?> /> - 
		
		<input class="inputbox" style="width: 40%;" name="price-to" type="text" <?php if (JRequest::getVar('price-to')) echo ' value="'.JRequest::getVar('price-to').'"'; ?> />
	</div>
