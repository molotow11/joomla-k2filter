<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<script type="text/javascript">
		jQuery(document).ready(function() {
			//multi select box
			jQuery(".k2filter-field-<?php echo $i; ?> select").multiselect({
				selectedList: 4,
				checkAllText: '<?php echo JText::_("MOD_K2_FILTER_CHECK_ALL_TEXT"); ?>',
				uncheckAllText: '<?php echo JText::_("MOD_K2_FILTER_UNCHECK_ALL_TEXT"); ?>',
				noneSelectedText: '<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_CATEGORY_DEFAULT'); ?>',
				selectedText: '# <?php echo JText::_("MOD_K2_FILTER_MULTIPLE_SELECTED_TEXT"); ?>'
			}).multiselectfilter({
				label: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER"); ?>', 
				placeholder: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER_KEYWORDS"); ?>'
			});
		});
	</script>

	<div class="k2filter-field-category-select-multiple k2filter-field-<?php echo $i; ?>">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_CATEGORY_HEADER'); ?>
		</h3>
		<?php endif; ?>
		
		<select name="category[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
			<?php echo $category_options; ?>
		</select>
    </div>