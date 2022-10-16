<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$checked = JRequest::getVar('searchword'.$field->id);
?>

	<div class="k2filter-field-multi-select k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_($field->name); ?>
		</h3>
		<?php endif; ?>

		<select name="searchword<?php echo $field->id; ?>[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> placeholder="<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.JText::_($field->name); ?>">
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