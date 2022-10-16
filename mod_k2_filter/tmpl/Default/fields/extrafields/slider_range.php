<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	
	var <?php echo "slider_range".$module->id.$field->id; ?>_values = new Array(
	<?php 
			echo '"';
			echo implode('", "', $field->content);
			echo '"';
	?>
	);
	
	jQuery(document).ready(function() {
					var length = <?php echo (count($field->content) - 1); ?>;
		
				<?php 
					if (JRequest::getVar('slider_range'.$field->id)) {
						$vals = explode(" - ", JRequest::getVar('slider_range'.$field->id));							
						echo "jQuery(\"#amount".$module->id.$field->id."\").val('" . $vals[0] . " - " . $vals[1] . "');\n";
						echo "jQuery(\"input#slider_range" . $module->id.$field->id . "_val\").val('" . $vals[0] . " - " . $vals[1] . "');\n";
					}
					else {
						echo "jQuery(\"#amount".$module->id.$field->id."\").val(slider_range".$module->id.$field->id."_values[0] + \" - \" + slider_range".$module->id.$field->id."_values[length]);\n";						
					}
				?>
				
		jQuery("#slider_range<?php echo $module->id.$field->id;?>")[0].slide = null;
		jQuery("#slider_range<?php echo $module->id.$field->id;?>").slider({
			<?php 
					if (JRequest::getVar('slider_range'.$field->id)) {
						$vals = explode(" - ", JRequest::getVar('slider_range'.$field->id));	
						
						echo "values: [";
						
						$values = Array(); 
						$jk = 0;
						foreach ($field->content as $which=>$value) {
							$values[$jk] = $value;
							$jk++;
							}
						for($jj=0; $jj<sizeof($vals); $jj++) {
							$vall = "0";
							for($jk=0; $jk<sizeof($values); $jk++) {
								if(($vals[$jj]) == $values[$jk]) {
									$vall = $jk;
								}
							}
							echo $vall;
							if($jj+1 < sizeof($vals))
								echo ", ";
						}
						
						echo "],";
					}
					else {
						echo "values: [ 0, length ],";
					}
			?>
			range: true,
			min: 0,
			max: <?php echo (sizeof($field->content) - 1); ?>,
			slide: function(event, ui) {
				if(<?php echo "slider_range".$module->id.$field->id; ?>_values[ui.values[0]] == "")
					jQuery("#amount<?php echo $module->id.$field->id;?>").val("0 - " + <?php echo "slider_range".$module->id.$field->id; ?>_values[ui.values[1]]);
				else 
					jQuery("#amount<?php echo $module->id.$field->id;?>").val(<?php echo "slider_range".$module->id.$field->id; ?>_values[ui.values[0]] + " - " + <?php echo "slider_range".$module->id.$field->id; ?>_values[ui.values[1]]);	
				
				if (<?php echo "slider_range".$module->id.$field->id; ?>_values[ui.values[1]] == "")
					jQuery("#amount<?php echo $module->id.$field->id;?>").val("");
				
				var vals = jQuery("#amount<?php echo $module->id.$field->id;?>").val();
				jQuery("input#slider_range<?php echo $module->id.$field->id;?>_val").val(vals);
			},
			stop: function(event, ui) {
				jQuery("form[name=K2Filter<?php echo $module->id; ?>]").trigger("change");
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>();
				<?php endif; ?>
			}
		});
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider_range<?php echo $module->id.$field->id;?>_wrapper">

		<input type="text" disabled id="amount<?php echo $module->id.$field->id;?>" class="k2filter-slider-amount" />

		<div id="slider_range<?php echo $module->id.$field->id;?>"></div>
		<input id="slider_range<?php echo $module->id.$field->id;?>_val" class="slider_val" type="hidden" name="slider_range<?php echo $field->id;?>" value="">
		</div>
	</div>

