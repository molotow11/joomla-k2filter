<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<script>
	jQuery(document).ready(function() {
		var ftitle_az = jQuery("input[name=ftitle_az]").val();
		
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").each(function() {
			if(ftitle_az == jQuery(this).text()) {
				jQuery(this).css("font-weight", "bold").addClass("active");
			}
			if(ftitle_az == "num" && jQuery(this).text() == "#") {
				jQuery(this).css("font-weight", "bold").addClass("active");
			}
		});
	
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").click(function() {
			jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").css("font-weight", "normal");

			if(jQuery(this).hasClass("active") == 0) {
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").removeClass("active");
				jQuery(this).css("font-weight", "bold").addClass("active");
				
				var value = jQuery(this).html();
				if(value == '#') {
					jQuery("input[name=ftitle_az]").val("num");
				}
				else {
					jQuery("input[name=ftitle_az]").val(value);
				}
			}
			else {
				jQuery(this).css("font-weight", "normal").removeClass("active");
				jQuery("input[name=ftitle_az]").val("");
			}
			
			<?php if($onchange) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			
			return false;
		});
	});
</script>

	<div class="k2filter-field-title-az">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TITLE_AZ'); ?>
		</h3>
		<?php endif; ?>
			
			<a class="title_az" href="#">#</a>
		<?php foreach(range('a', 'z') as $letter) : ?>
			<a class="title_az" href="#"><?php echo strtoupper($letter); ?></a>
		<?php endforeach; ?>
		
		<input name="ftitle_az" type="hidden" <?php if (JRequest::getVar('ftitle_az')) echo ' value="'.JRequest::getVar('ftitle_az').'"'; ?> />
	</div>

