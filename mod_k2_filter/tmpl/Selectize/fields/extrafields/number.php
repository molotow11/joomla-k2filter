<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$values = modK2FilterHelper::getExtraValues($field->id, $params);

$values = implode(",", $values);
$values = explode(",", $values);
natsort($values);

?>

<style>
	.field-number-<?php echo $module->id . '-' . $k; ?> input { width: 27px; padding: 5px 3px; text-align: center; }
	.field-number-<?php echo $module->id . '-' . $k; ?> span { cursor: pointer; user-select: none; padding: 4px; }
</style>

<script>
	var field_number_<?php echo $module->id . '_' . $k; ?>_values = [
		<?php foreach($values as $value) {
			echo "'" . $value . "',";
		}
		?>
	];
	jQuery(document).ready(function($) {
		$('div.field-number-<?php echo $module->id . '-' . $k; ?> span').on('click', function() {
			var input = $(this).parent().find("input");
			if($(this).hasClass("plus")) {
				input.val(change_value_<?php echo $module->id . '_' . $k; ?>('plus', input.val())).trigger("change");
			}
			else {
				input.val(change_value_<?php echo $module->id . '_' . $k; ?>('minus', input.val())).trigger("change");
			}
		});
	});
	function change_value_<?php echo $module->id . '_' . $k; ?>(mode, curr_val) {
		//trigger selectable values
		if(field_number_<?php echo $module->id . '_' . $k; ?>_values.length) {
			if(curr_val == '') {
				if(mode == 'plus') {
					return field_number_<?php echo $module->id . '_' . $k; ?>_values[0];
				}
				else {
					return '';
				}
			}
			else {
				if(curr_val == field_number_<?php echo $module->id . '_' . $k; ?>_values[field_number_<?php echo $module->id . '_' . $k; ?>_values.length - 1] && mode == 'plus') { 
					return field_number_<?php echo $module->id . '_' . $k; ?>_values[field_number_<?php echo $module->id . '_' . $k; ?>_values.length - 1]; //disable for the latest value 
				}
				var tmp = '';
				field_number_<?php echo $module->id . '_' . $k; ?>_values.each(function(val, key) {
					if(curr_val == val) {
						if(mode == 'plus') {
							tmp = field_number_<?php echo $module->id . '_' . $k; ?>_values[key + 1];
						}
						else {
							tmp = field_number_<?php echo $module->id . '_' . $k; ?>_values[key - 1];
						}
					}
				});
				return tmp;
			}
		}
		//trigger integer values
		else {
			if(mode == 'plus') {
				if(curr_val == '') { 
					curr_val = 0;
				}
				return parseInt(curr_val) + 1;
			}
			else {
				if(curr_val > 1) {
					return parseInt(curr_val) - 1;
				}
				else {
					return '';
				}
			}
		}
	}
</script>

	<div class="k2filter-field-number field-number-<?php echo $module->id . '-' . $k; ?>">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<div class='field-wrapper'>
			<span class='minus'>-</span>
			<input name="searchword<?php echo $field->id; ?>" type="text" value="<?php echo JRequest::getVar("searchword{$field->id}"); ?>" />
			<span class='plus'>+</span>
		</div>
	</div>

