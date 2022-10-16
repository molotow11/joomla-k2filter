		<script type="text/javascript">
			<!--
			function clearSearch_<?php echo $module->id; ?>() {
				isClearSearch = 1;
				jQuery("#K2FilterBox<?php echo $module->id; ?> form select").each(function () {
					jQuery(this).find("option").eq(0).prop("selected", "selected");
				});
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input.inputbox").each(function () {
					jQuery(this).val("");
				});		

				jQuery(".ui-slider").each(function() {
					var slider_min = jQuery(this).slider("option", "min");
					var slider_max = jQuery(this).slider("option", "max");
					jQuery(this).slider("option", "values", [slider_min, slider_max]);
					jQuery(this).parent().find(".k2filter-slider-amount").val(slider_min + ' - ' + slider_max);
				});
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input.slider_val").each(function () {
					jQuery(this).val("");
				});
						
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[type=checkbox]").each(function () {
					jQuery(this).removeAttr('checked');
				});

				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[name*=flabel]").val("");				
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> form input[type=radio]").each(function () {
					jQuery(this).removeAttr('checked');
				});
				
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").css("font-weight", "normal").removeClass("active");
				jQuery("#K2FilterBox<?php echo $module->id; ?> input[name=ftitle_az]").val("");
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.search_az").css("font-weight", "normal").removeClass("active");
				jQuery("#K2FilterBox<?php echo $module->id; ?> input.search_az").val("");
				
				jQuery(".k2filter-field-multi select").each(function() {
					jQuery(this).multiselect("uncheckAll").multiselect("refresh");
				});		
						
				jQuery(".k2filter-field-tag-multi-select select").multiselect("uncheckAll").multiselect("refresh");
				jQuery(".k2filter-field-category-select-multiple select").multiselect("uncheckAll").multiselect("refresh");
				jQuery(".k2filter-field-author-multi select").multiselect("uncheckAll").multiselect("refresh");
				
				jQuery("<?php echo $ajax_container; ?>").html("");
				
				setTimeout(function() {
					<?php if($acounter) : ?>
					acounter<?php echo $module->id; ?>();
					<?php endif; ?>
					
					<?php if($onchange) : ?>
					submit_form_<?php echo $module->id; ?>();
					<?php endif; ?>
					
					<?php if($dynobox) : ?>
					dynobox<?php echo $module->id; ?>(jQuery("#K2FilterBox<?php echo $module->id; ?>").find("select:eq(0)")[0]);
					<?php endif; ?>
				}, 100);
				
				isClearSearch = 0;
			}
			//-->
		</script>	

		<input type="button" value="<?php echo JText::_('MOD_K2_FILTER_BUTTON_CLEAR'); ?>" class="btn btn-warning button reset <?php echo $moduleclass_sfx; ?>" onclick="clearSearch_<?php echo $module->id; ?>()" />