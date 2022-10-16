<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-tag-select">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>
		</h3>
		<?php endif; ?>

		<?php
			foreach ($tags as $tag) {
				echo "<a href='#' onClick='document.K2Filter.ftag.value=this.text; submit_form_<?php echo $module->id; ?>(); return false;'>".$tag->tag."</a>";
				echo "<br />";
			}
		?>
		
		<input name="ftag" value="<?php echo JRequest::getVar('ftag'); ?>" type="hidden" />
		
    </div>