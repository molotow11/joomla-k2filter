<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$values_count = count($field->content) - 1;
$field->content[0] = floor($field->content[0]);
$field->content[$values_count] = ceil($field->content[$values_count]);

?>

<script type="text/javascript">
	
	<?php 
		$from = JRequest::getVar("price-fromj2", $field->content[0]);
		$to = JRequest::getVar("price-toj2", $field->content[$values_count]);
		$value = $from. " - " .$to;
	?>
	
	jQuery(document).ready(function() {
	
		jQuery("#slider<?php echo $module->id.$k;?>")[0].slide = null;
		jQuery("#slider<?php echo $module->id.$k;?>").slider({
			range: true,
			min: <?php echo $field->content[0]; ?>,
			max: <?php echo $field->content[$values_count]; ?>,
			step: 1,
			values: [ <?php echo $from; ?>, <?php echo $to; ?> ],
			slide: function(event, ui) {
				jQuery("#amount<?php echo $module->id.$k;?>").val(ui.values[0] + " - " + ui.values[1]);
				jQuery("input#price<?php echo $k; ?>-from").val(ui.values[0]);
				jQuery("input#price<?php echo $k; ?>-to").val(ui.values[1]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
		jQuery("#amount<?php echo $module->id.$k;?>").val("<?php echo $value; ?>");
		
		jQuery("#amount<?php echo $module->id.$k;?>").keyup(function() {
			var min = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[0]);
			var max = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[1]);
			jQuery("#slider<?php echo $module->id.$k;?>").slider("option", "values", [min, max]);
			jQuery("input#price<?php echo $k; ?>-from").val(min);
			jQuery("input#price<?php echo $k; ?>-to").val(max);
		});
	});
</script>
	
	<div class="k2filter-field-price-range">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_PRICE'); ?>
		</h3>
		
		<div class="slider<?php echo $module->id.$k;?>_wrapper">
			<input type="text" disabled id="amount<?php echo $module->id.$k;?>" class="k2filter-slider-amount" />

			<div id="slider<?php echo $module->id.$k;?>"></div>
			
			<input id="price<?php echo $k; ?>-from" class="slider_val" type="hidden" name="price-fromj2" value="<?php echo JRequest::getVar("price-fromj2") != 0 ? $from : ''; ?>">
			<input id="price<?php echo $k; ?>-to" class="slider_val" type="hidden" name="price-toj2" value="<?php echo JRequest::getVar("price-toj2") != 0 ? $to : ''; ?>">
		
		</div>
	</div>
