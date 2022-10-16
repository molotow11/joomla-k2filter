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
	
	var <?php echo "slider".$field->id; ?>_values = new Array("", 
	<?php 
			echo '"';
			echo implode('", "', $field->content);
			echo '"';
	?>
	);
	
	jQuery(document).ready(function() {
		
	<?php 
					if (JRequest::getVar('slider'.$field->id)) {
						$values = Array(); 
						$values[0] = "";
							$jk = 1;
							foreach ($field->content as $which=>$value) {
								$values[$jk] = $value;
								$jk++;
							}
						for($jk=0; $jk<sizeof($values); $jk++) {
							if((JRequest::getVar('slider'.$field->id)) == $values[$jk]) {
								echo "jQuery(\"#amount".$field->id."\").val(slider".$field->id."_values[".$jk."]);\n";
								echo "jQuery(\"#slider".$field->id."_val\").val(slider".$field->id."_values[".$jk."]);\n";
							}
						}	
					}
					?>
		jQuery("#slider<?php echo $field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $field->id;?>").slider({
			 <?php 
					if (JRequest::getVar('slider'.$field->id)) {
						echo "value: ";
						$values = Array(); 
						$values[0] = "";
						$jk = 1;
						foreach ($field->content as $which=>$value) {
							$values[$jk] = $value;
							$jk++;
						}
						for($jk=0; $jk<sizeof($values); $jk++) {
							if((JRequest::getVar('slider'.$field->id)) == $values[$jk]) {
								echo $jk;
							}
						}	
					echo ",\n";
					}
					?>
			range: "min",
			min: 0,
			max: <?php echo (sizeof($field->content)); ?>,
			slide: function(event, ui) {
				jQuery("#amount<?php echo $field->id;?>").val(<?php echo "slider".$field->id; ?>_values[ui.value]);
				jQuery("#slider<?php echo $field->id;?>_val").val(<?php echo "slider".$field->id; ?>_values[ui.value]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider<?php echo $field->id;?>_wrapper">

		<input type="text" disabled id="amount<?php echo $field->id;?>" class="k2filter-slider-amount" />

		<div id="slider<?php echo $field->id;?>"></div>
		<input id="slider<?php echo $field->id;?>_val" class="slider_val" type="hidden" name="slider<?php echo $field->id;?>" value="">
		</div>
	</div>

