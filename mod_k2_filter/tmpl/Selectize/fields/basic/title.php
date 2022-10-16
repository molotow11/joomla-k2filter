<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-title">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TITLE'); ?>
		</h3>
		
		<input class="inputbox" name="ftitle" type="text" <?php if (JRequest::getVar('ftitle')) echo ' value="'.JRequest::getVar('ftitle').'"'; ?> />
	</div>
