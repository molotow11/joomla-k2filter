	<script type="text/javascript">
		
		function ajax_results<?php echo $module->id; ?>() {	
			jQuery("html, body").animate({
				scrollTop: jQuery("<?php echo $ajax_container; ?>").offset().top - 70
			}, 500);
		
			jQuery("<?php echo $ajax_container; ?>").html("<p><img alt='loading' src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
		
			var url = "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&tmpl=component'); ?>";
			var data = jQuery("#K2FilterBox<?php echo $module->id; ?> form").find(":input").filter(function () {
							return jQuery.trim(this.value).length > 0
						}).serialize();
						
			jQuery.ajax({
				data: data + "&tmpl=component",
				type: "get",
				url: url,
				success: function(response) {	
					var result = jQuery(response).filter("#k2Container").html();
					result = "<div id='k2Container'>" + result + "</div>";
					jQuery("<?php echo $ajax_container; ?>").html(result);				
					
					jQuery("#K2FilterBox<?php echo $module->id; ?> div.acounter").hide();
					history.pushState({}, '', "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter'); ?>" + "?" + data);
				}
			});
		}

		jQuery(document).ready(function() {
			
			jQuery('body').on("click", "<?php echo $ajax_container; ?> div.k2Pagination a, <?php echo $ajax_container; ?> .pagination a", function() {
				jQuery("<?php echo $ajax_container; ?>").html("<p><img alt='loading' src='<?php echo JURI::root(); ?>media/k2/assets/images/system/loader.gif' /></p>");
				
				var module_pos = jQuery("<?php echo $ajax_container; ?>").offset();
				window.scrollTo(module_pos.left, module_pos.top - 70);
				
				var url = jQuery(this).attr('href');
				
				jQuery.ajax({
					type: "GET",
					url: url + "&tmpl=component",
					success: function(response) {
						var result = jQuery(response).filter("#k2Container").html();
						result = "<div id='k2Container'>" + result + "</div>";
					
						jQuery("<?php echo $ajax_container; ?>").html(result);					
					}
				});
				return false;
			});
			
		});
		
	</script>
  
	<?php if($ajax_container == '.results_container') : ?>
    <div class="results_container"></div>
	<?php endif; ?>