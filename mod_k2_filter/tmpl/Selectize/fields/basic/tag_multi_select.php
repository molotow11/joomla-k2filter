<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$checked = JRequest::getVar('taga');
if(JRequest::getVar("task") == "tag") {
	$checked = Array(JRequest::getVar("tag"));
}
?>

	<div class="k2filter-field-tag-multi-select k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>
		</h3>
		<?php endif; ?>

		<select name="taga[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> placeholder="<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>">
		<?php
		if($tags) {
			foreach ($tags as $tag) {
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $tag->id) $selected = ' selected="selected"';
					}
				}
				echo "<option value='".$tag->id."'".$selected.">".$tag->tag."</option>";
			}
		}
		?>	
		</select>		
	</div>

