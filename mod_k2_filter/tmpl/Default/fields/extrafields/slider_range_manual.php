<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	
	<?php 
		$from = JRequest::getVar("searchword".$field->id."-from", 0);
		$to = JRequest::getVar("searchword".$field->id."-to", 0);

		if($from != 0 && $to != 0)
			$value = $from. " - " .$to;
		if($from == 0 && $to != 0)
			$value = "0 - " .$to;
		if($from == 0 && $to == 0)
			$value = "0 - 1.000.000";
	?>
	
	jQuery(document).ready(function() {
	
		jQuery("#slider<?php echo $field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $field->id;?>").slider({
			range: true,
			min: 0,
			max: 1000000,
			step: 1,
			values: [<?php if($from != 0) echo $from; else echo "0" ?>, <?php if($to != 0) echo $to; else echo "1000000" ?>],
			slide: function(event, ui) {
				jQuery("#amount<?php echo $field->id;?>").val(ui.values[0] + " - " + ui.values[1]);
				jQuery("input#slider<?php echo $field->id;?>_val_from").val(ui.values[0]);
				jQuery("input#slider<?php echo $field->id;?>_val_to").val(ui.values[1]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
		jQuery("#amount<?php echo $field->id;?>").val("<?php echo $value; ?>");
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider<?php echo $field->id;?>_wrapper">

			<input type="text" disabled id="amount<?php echo $field->id;?>" class="k2filter-slider-amount" />

			<div id="slider<?php echo $field->id;?>"></div>
			
			<input id="slider<?php echo $field->id;?>_val_from" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-from" value="<?php if($from != 0) echo $from; ?>">
			
			<input id="slider<?php echo $field->id;?>_val_to" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-to" value="<?php if($to != 0) echo $to; ?>">
		
		</div>
	</div>

