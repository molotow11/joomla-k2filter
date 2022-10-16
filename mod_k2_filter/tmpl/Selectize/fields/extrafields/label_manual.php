<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<script>
	jQuery(document).ready(function() {
		jQuery("a.flabel").click(function() {
			var val = jQuery(this).text();
			jQuery("input[name=flabel]").val(val);
			submit_form_<?php echo $module->id; ?>();
		});
	});
</script>

	<div class="k2filter-field-label">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<div>
			<a class="flabel" href="javascript: return false;">Your label 1</a>
			<a class="flabel" href="javascript: return false;">Your label 2</a>
			<div class="K2FilterClear"></div>
		</div>
		<input name="flabel" type="hidden" <?php if (JRequest::getVar('flabel')) echo ' value="'.JRequest::getVar('flabel').'"'; ?> />
	</div>

