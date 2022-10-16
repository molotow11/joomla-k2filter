<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$values_count = count($field->content) - 1;
$field->content[0] = floor($field->content[0]);
$field->content[$values_count] = ceil($field->content[$values_count]);

?>

<script type="text/javascript">
	
	<?php 
		$from = JRequest::getVar("searchword".$field->id."-from", $field->content[0]);
		$to = JRequest::getVar("searchword".$field->id."-to", $field->content[$values_count]);
		$value = $from. " - " .$to;
	?>
	
	jQuery(document).ready(function() {
	
		jQuery("#slider<?php echo $module->id.$field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $module->id.$field->id;?>").slider({
			range: true,
			min: <?php echo $field->content[0]; ?>,
			max: <?php echo $field->content[$values_count]; ?>,
			step: 1,
			values: [ <?php echo $from; ?>, <?php echo $to; ?> ],
			slide: function(event, ui) {
				jQuery("#amount<?php echo $module->id.$field->id;?>").val(ui.values[0] + " - " + ui.values[1]);
				jQuery("input#slider<?php echo $module->id.$field->id;?>_val_from").val(ui.values[0]);
				jQuery("input#slider<?php echo $module->id.$field->id;?>_val_to").val(ui.values[1]);
			},
			stop: function(event, ui) {
				jQuery("form[name=K2Filter<?php echo $module->id; ?>]").trigger("change");
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
		jQuery("#amount<?php echo $module->id.$field->id;?>").val("<?php echo $value; ?>");
		
		jQuery("#amount<?php echo $module->id.$field->id;?>").keyup(function() {
			var min = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[0]);
			var max = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[1]);
			jQuery("#slider<?php echo $module->id.$field->id;?>").slider("option", "values", [min, max]);
			jQuery("input#slider<?php echo $module->id.$field->id;?>_val_from").val(min);
			jQuery("input#slider<?php echo $module->id.$field->id;?>_val_to").val(max);
		});
	});
</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider<?php echo $module->id.$field->id;?>_wrapper">

			<input type="text" disabled id="amount<?php echo $module->id.$field->id;?>" class="k2filter-slider-amount" />

			<div id="slider<?php echo $module->id.$field->id;?>"></div>
			
			<input id="slider<?php echo $module->id.$field->id;?>_val_from" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-from" value="<?php echo JRequest::getVar("searchword".$field->id."-from") != 0 ? $from : ''; ?>">
			
			<input id="slider<?php echo $module->id.$field->id;?>_val_to" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-to" value="<?php echo JRequest::getVar("searchword".$field->id."-to") != 0 ? $to : ''; ?>">
		
		</div>
	</div>

