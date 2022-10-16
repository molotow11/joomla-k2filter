<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$selected = JRequest::getVar('ftag');

if(JRequest::getVar("task") == "tag") {
	$selected = JRequest::getVar("tag");
}

?>

	<div class="k2filter-field-tag-select">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>
		</h3>
		<?php endif; ?>
		
		<select name="ftag" <?php if($onchange) : ?>onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?>>
			<option value="" class="empty"><?php echo JText::_('MOD_K2_FILTER_FIELD_TAG_DEFAULT'); ?></option>
			<?php
				foreach ($tags as $tag) {
					echo '<option ';
					if ($selected == $tag->tag) { echo 'selected="selected"'; }
					echo '>'.$tag->tag.'</option>';
				}
			?>
		</select>
    </div>