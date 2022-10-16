		<script type="text/javascript">			
			function acounter<?php echo $module->id; ?>() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .data").hide();
				jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .loader").show();

				var url = "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&format=count'); ?>";
				var data = jQuery("#K2FilterBox<?php echo $module->id; ?> form").find(":input").filter(function () {
							return jQuery.trim(this.value).length > 0
						}).serialize();
							
				jQuery.ajax({
					data: data + "&format=count",
					type: "get",
					url: url,
					success: function(response) {
						jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .loader").hide();
						jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter .data").html("<p>"+response+" <?php echo JText::_("MOD_K2_FILTER_ACOUNTER_TEXT"); ?></p>").show();
					}
				});
			}
			jQuery(document).ready(function() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> form").change(function(event) {
					setTimeout(function() {
						acounter<?php echo $module->id; ?>();
					}, 200);
				});
			});
		</script>
		
		<div class="acounter">
			<div class="data"></div>
			<div class="loader" style="display: none;"><img alt='loading' src='<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/images/loading.png' style='width: 20px;' /></div>
		</div>