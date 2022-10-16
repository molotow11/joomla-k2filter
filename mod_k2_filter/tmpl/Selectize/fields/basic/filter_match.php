<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-filter-match">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FILTER_TYPE_FILTER_MATCH'); ?>
		</h3>

		<label><input type='radio' name='filter_match' value='all' <?php if(JRequest::getVar("filter_match") == "all") echo 'checked=checked'; ?>/> <?php echo JText::_('MOD_K2_FILTER_FILTER_ALL'); ?></label>
		<label><input type='radio' name='filter_match' value='any' <?php if(JRequest::getVar("filter_match") == "any") echo 'checked=checked'; ?>/> <?php echo JText::_('MOD_K2_FILTER_FILTER_ANY'); ?></label>
	</div>