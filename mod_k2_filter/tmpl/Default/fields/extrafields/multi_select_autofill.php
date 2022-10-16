<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JRequest::getVar('searchword'.$field->id);

?>

	<script type="text/javascript">
		
		jQuery(document).ready(function() {
		
			//multi select box
			jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> select").multiselect({
				selectedList: 4,
				checkAllText: "<?php echo JText::_("MOD_K2_FILTER_CHECK_ALL_TEXT"); ?>",
				uncheckAllText: "<?php echo JText::_("MOD_K2_FILTER_UNCHECK_ALL_TEXT"); ?>",
				noneSelectedText: "<?php echo '-- '.JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.JText::_($field->name).' --'; ?>",
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
			<?php echo JText::_($field->name); ?>
		</h3>
		<?php endif; ?>

		<select name="searchword<?php echo $field->id; ?>[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
		<?php
		if($field->content) {
			foreach ($field->content as $value) {
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $value) $selected = ' selected="selected"';
					}
				}
				echo "<option value=\"".$value."\"".$selected.">".$value."</option>";
			}
		}
		?>			
		</select>		
		
	</div>

