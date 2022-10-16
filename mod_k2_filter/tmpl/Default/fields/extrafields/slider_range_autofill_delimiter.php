<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$from = JRequest::getVar("searchword".$field->id."-from", (float)$field->content[0]);
$values_count = count($field->content) - 1;
$to = JRequest::getVar("searchword".$field->id."-to", (float)$field->content[$values_count]);
$value = number_format($from, 0, '', ','). " - " .number_format($to, 0, '', ',');

?>

<script type="text/javascript">
	function addCommas(nStr) {
		nStr += '';
		x = nStr.split(',');
		x1 = x[0];
		x2 = x.length > 1 ? ',' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

	jQuery(document).ready(function() {
	
		jQuery("#slider<?php echo $module->id.$field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $module->id.$field->id;?>").slider({
			range: true,
			min: <?php echo (float)$field->content[0]; ?>,
			max: <?php echo (float)$field->content[$values_count]; ?>,
			step: 1,
			values: [ <?php echo $from; ?>, <?php echo $to; ?> ],
			slide: function(event, ui) {
				jQuery("#amount<?php echo $module->id.$field->id;?>").val(addCommas(ui.values[0]) + " - " + addCommas(ui.values[1]));
				jQuery("input#slider<?php echo $module->id.$field->id;?>_val_from").val(ui.values[0]);
				jQuery("input#slider<?php echo $module->id.$field->id;?>_val_to").val(ui.values[1]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
		jQuery("#amount<?php echo $module->id.$field->id?>").val("<?php echo $value; ?>");
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>: <input type="text" disabled id="amount<?php echo $module->id.$field->id;?>" class="k2filter-slider-amount" />
		</h3>

		<div class="slider<?php echo $field->id;?>_wrapper">

			<div id="slider<?php echo $module->id.$field->id;?>"></div>
			
			<input id="slider<?php echo $module->id.$field->id;?>_val_from" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-from" value="<?php echo $from; ?>">
			
			<input id="slider<?php echo $module->id.$field->id;?>_val_to" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-to" value="<?php echo $to; ?>">
		
		</div>
	</div>

