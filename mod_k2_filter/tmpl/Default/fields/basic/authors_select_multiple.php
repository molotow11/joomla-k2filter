<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JRequest::getVar('fauthor');

?>

	<script type="text/javascript">
		
		jQuery(document).ready(function() {
		
			//multi select box
			jQuery(".k2filter-field-<?php echo $k; ?> select").multiselect({
				selectedList: 4,
				checkAllText: '<?php echo JText::_("MOD_K2_FILTER_CHECK_ALL_TEXT"); ?>',
				uncheckAllText: '<?php echo JText::_("MOD_K2_FILTER_UNCHECK_ALL_TEXT"); ?>',
				noneSelectedText: '<?php echo JText::_('MOD_K2_FILTER_SELECT_AUTHOR_DEFAULT'); ?>',
				selectedText: '# <?php echo JText::_("MOD_K2_FILTER_MULTIPLE_SELECTED_TEXT"); ?>'
			}).multiselectfilter({
				label: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER"); ?>', 
				placeholder: '<?php echo JText::_("MOD_K2_FILTER_FIELD_MULTI_FILTER_KEYWORDS"); ?>'
			});
			
		});
	
	</script>

	<div class="k2filter-field-author-multi k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3><?php echo JText::_('MOD_K2_FILTER_SELECT_AUTHOR_HEADER'); ?></h3>
		<?php endif; ?>
		
		<select name="fauthor[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
		<?php
		if($authors) {
			foreach ($authors as $author) {
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $author->id) $selected = ' selected="selected"';
					}
				}
				echo "<option value='".$author->id."'".$selected.">".$author->name."</option>";
			}
		}
		?>			
		</select>		
		
	</div>

