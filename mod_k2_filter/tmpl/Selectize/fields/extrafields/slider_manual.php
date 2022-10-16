<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">

	<?php if($onchange) : ?>
	jQuery(document).ready(function() {
		jQuery("#slider<?php echo $field->id;?>").bind('mouseup', function() {
			jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit();
		});
	});
	<?php endif; ?>
	
	<?php 
		if(JRequest::getVar("searchword".$field->id) != "")
			$value = JRequest::getVar("searchword".$field->id);
		else 
			$value = 0;
	?>
	
	jQuery(document).ready(function() {

		jQuery("#slider<?php echo $field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $field->id;?>").slider({
			value: <?php echo $value; ?>,
			range: "min",
			min: 0,
			max: 10000,
			step: 100,
			slide: function(event, ui) {
				jQuery( "#amount<?php echo $field->id;?>" ).val( "$" + ui.value );
				jQuery("input#slider<?php echo $field->id;?>_val").val( ui.value );
			}
		});
		jQuery("#amount<?php echo $field->id;?>").val("<?php if($value != 0) echo "$".$value; ?>");
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider<?php echo $field->id;?>_wrapper">

		<input type="text" disabled id="amount<?php echo $field->id;?>" class="k2filter-slider-amount" />

		<div id="slider<?php echo $field->id;?>"></div>
		<input id="slider<?php echo $field->id;?>_val" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>" value="">
		</div>
	</div>

