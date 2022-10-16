	jQuery(document).ready(function() {	
		jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit(function() {
			<?php if($allrequired) : ?>
			if(!check_required<?php echo $module->id; ?>()) {
				return false;
			}
			<?php endif; ?>
			jQuery(this).find("input, select").each(function() {
				if(jQuery(this).val() == '') {
					jQuery(this).attr("name", "");
				}
			});
		});
		<?php if($ajax_results == 1) : ?>
		jQuery("#K2FilterBox<?php echo $module->id; ?> input[type=submit]").click(function() {
			<?php if($allrequired) : ?>
			if(!check_required<?php echo $module->id; ?>()) {
				return false;
			}
			<?php endif; ?>
			ajax_results<?php echo $module->id; ?>();
			return false;
		});
		<?php endif; ?>
	});
	
	var isClearSearch = 0;
	function submit_form_<?php echo $module->id; ?>() {
		if(isClearSearch) return false;
		<?php if($ajax_results == 1) { ?>
		ajax_results<?php echo $module->id; ?>();
		return false;
		<?php } else { ?>	
		jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit();
		<?php } ?>
	}
	
  <?php if($allrequired) : ?>
	function check_required<?php echo $module->id; ?>() {
		var checker = 1;
		jQuery("#K2FilterBox<?php echo $module->id; ?> select, #K2FilterBox<?php echo $module->id; ?> input.inputbox").each(function() {
			if(jQuery(this).val() == "") {
				checker = 0;
			}
		});
		if(checker == 0) {
			return false;
		}
		return true;
	}
  <?php endif; ?>