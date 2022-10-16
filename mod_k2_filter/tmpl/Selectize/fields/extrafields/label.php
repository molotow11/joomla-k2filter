<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$values = $field->content;

if(!$values) {
	$values = modK2FilterHelper::getExtraValues($field->id, $params);
	$values = implode(",", $values);
	$values = explode(",", $values);
}
$selected = JRequest::getVar("flabel");
?>
	
<script>
	jQuery(document).ready(function() {
		jQuery(".k2filter-field-label<?php echo $field->id; ?> a.flabel").on('click', function() {
			var val = jQuery(this).text().trim();
			if(jQuery(".k2filter-field-label<?php echo $field->id; ?> input").val() == val) {
				jQuery(".k2filter-field-label<?php echo $field->id; ?> input").val("");
				submit_form_<?php echo $module->id; ?>();
				return false;
			}
			jQuery(".k2filter-field-label<?php echo $field->id; ?> input").val(val);
			submit_form_<?php echo $module->id; ?>();
		});
	});
</script>

	<div class="k2filter-field-label<?php echo $field->id; ?>">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<div>
			<?php 
				if(count($values)) {
					foreach($values as $value) {
			?>
					<a class="flabel label label-primary" href="javascript: return false;"
						<?php 
							foreach($selected as $val) {
								if($val == $value) echo " style='text-decoration: underline;'"; 
							}
						?>
					>
						<?php echo $value; ?>
					</a>
			<?php
					}
				}
			?>
			<div class="K2FilterClear"></div>
		</div>
		<input name="flabel[]" type="hidden"
			<?php
				foreach($selected as $val) {
					foreach($values as $value) {
						if($val == $value) echo " value='{$value}'"; 
					}
				}
			?>
		/>
	</div>

