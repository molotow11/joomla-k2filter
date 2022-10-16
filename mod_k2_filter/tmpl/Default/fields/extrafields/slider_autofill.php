<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$field->content = modK2FilterHelper::getExtraValues($field->id, $params);
if($field->content) {
	foreach($field->content as $val_k=>$value) {
		$value = preg_replace('~[^0-9,.]~','',$value);
		$value = str_replace(",", ".", $value);
		$field->content[$val_k] = floatval($value);
		if(floatval($value) == 0) {
			unset($field->content[$val_k]);
		}
	}
	sort($field->content);
}

$values_count = count($field->content) - 1;
$field->content[0] = floor($field->content[0]);
$field->content[$values_count] = ceil($field->content[$values_count]);

?>

<script type="text/javascript">
	
	<?php 
		$to = JRequest::getVar("searchword".$field->id."-to", $field->content[$values_count]);
		$value = $to;
	?>
	
	jQuery(document).ready(function() {	
		jQuery("#slider<?php echo $module->id.$field->id;?>")[0].slide = null;
		jQuery("#slider<?php echo $module->id.$field->id;?>").slider({
			range: "min",
			min: 0,
			max: <?php echo $field->content[$values_count]; ?>,
			step: 1,
			value: <?php echo $to; ?>,
			slide: function(event, ui) {
				jQuery("#amount<?php echo $module->id.$field->id;?>").val(ui.value);
				jQuery("input#slider<?php echo $module->id.$field->id;?>_val_to").val(ui.value);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>()
				<?php endif; ?>
			}
		});
		jQuery("#amount<?php echo $module->id.$field->id;?>").val("<?php echo $value; ?>");
	});
</script>

	<div class="k2filter-field-slider">
		<h3>
			<?php echo $field->name; ?>
		</h3>

		<div class="slider<?php echo $module->id.$field->id;?>_wrapper">

			<input type="text" disabled id="amount<?php echo $module->id.$field->id;?>" class="k2filter-slider-amount" />

			<div id="slider<?php echo $module->id.$field->id;?>"></div>
			
			<input id="slider<?php echo $module->id.$field->id;?>_val_to" class="slider_val" type="hidden" name="searchword<?php echo $field->id;?>-to" value="<?php echo JRequest::getVar("searchword".$field->id."-to") != 0 ? $to : ''; ?>">
		
		</div>
	</div>

