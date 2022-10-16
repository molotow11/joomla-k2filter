<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<link type="text/css" href="modules/mod_k2_filter/assets/js/jquery.ui.slider-rtl.css" rel="stylesheet"> 
<script type="text/javascript" src="modules/mod_k2_filter/assets/js/jquery.ui.slider-rtl.js"></script>
<script type="text/javascript">
	
	var <?php echo "slider_range".$field->id; ?>_values = new Array(
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
						echo "jQuery(\"#amount".$field->id."\").val('" . $vals[1] . " - " . $vals[0] . "');\n";
						echo "jQuery(\"input#slider_range" . $field->id . "_val\").val('" . $vals[0] . " - " . $vals[1] . "');\n";
					}
					else {
						echo "jQuery(\"#amount".$field->id."\").val(slider_range".$field->id."_values[length] + \" - \" + slider_range".$field->id."_values[0]);\n";						
					}
				?>
				
		jQuery("#slider_range<?php echo $field->id;?>")[0].slide = null;
		jQuery("#slider_range<?php echo $field->id;?>").slider({
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
				jQuery("#amount<?php echo $field->id;?>").val(<?php echo "slider_range".$field->id; ?>_values[ui.values[1]] + " - " + <?php echo "slider_range".$field->id; ?>_values[ui.values[0]]);
				
				jQuery("input#slider_range<?php echo $field->id;?>_val").val(<?php echo "slider_range".$field->id; ?>_values[ui.values[0]] + " - " + <?php echo "slider_range".$field->id; ?>_values[ui.values[1]]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			},
			isRTL: true
		});
	});
	</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider_range<?php echo $field->id;?>_wrapper">

		<input type="text" disabled id="amount<?php echo $field->id;?>" class="k2filter-slider-amount" />

		<div id="slider_range<?php echo $field->id;?>"></div>
		<input id="slider_range<?php echo $field->id;?>_val" class="slider_val" type="hidden" name="slider_range<?php echo $field->id;?>" value="">
		</div>
	</div>

