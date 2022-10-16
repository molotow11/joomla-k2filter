<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JRequest::getVar('array'.$field->id);

?>

	<script type="text/javascript">
		
		jQuery(document).ready(function() {
		
			//multi select box
			jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> select").multiselect({
				selectedList: 4,
				checkAllText: "<?php echo JText::_("MOD_K2_FILTER_CHECK_ALL_TEXT"); ?>",
				uncheckAllText: "<?php echo JText::_("MOD_K2_FILTER_UNCHECK_ALL_TEXT"); ?>",
				noneSelectedText: "<?php echo '-- '.JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.$field->name.' --'; ?>",
				selectedText: "# <?php echo JText::_("MOD_K2_FILTER_MULTIPLE_SELECTED_TEXT"); ?>"
			}).multiselectfilter({
				label: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER"); ?>', 
				placeholder: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER_KEYWORDS"); ?>'
			});
			
		});
	
	</script>

	<div class="k2filter-field-multi k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<?php endif; ?>

		<select name="array<?php echo $field->id; ?>[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
				
			<?php if($field->name == "Your field name") : ?>
				<option <?php if (in_array("Option 1", $checked)) {echo 'selected="selected"';} ?>>Option 1</option>';
				<option <?php if (in_array("Option 2", $checked)) {echo 'selected="selected"';} ?>>Option 2</option>';
				<option <?php if (in_array("Option 3", $checked)) {echo 'selected="selected"';} ?>>Option 3</option>';
			<?php endif; ?>
				
		</select>		
		
	</div>

