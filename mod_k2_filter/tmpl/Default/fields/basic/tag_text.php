<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-tag-text k2filter-field-<?php echo $i; ?>">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>
		</h3>
		<input class="inputbox" name="ftag" type="text" <?php if (JRequest::getVar('ftag')) echo ' value="'.JRequest::getVar('ftag').'"'; ?> />
	</div>
