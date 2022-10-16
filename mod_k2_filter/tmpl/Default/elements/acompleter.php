	<script type="text/javascript">
		$ = jQuery.noConflict();
		$(document).ready(function() {
			var suggestion_fields<?php echo $module->id; ?> = $("#K2FilterBox<?php echo $module->id; ?> input[name=fitem_all], #K2FilterBox<?php echo $module->id; ?> input[name=ftitle], #K2FilterBox<?php echo $module->id; ?> input[name=ftag]"); //works only for Keyword, Title or Tag text fields
			
			suggestion_fields<?php echo $module->id; ?>.each(function(k, field) {
				field = $(field);
				var field_cell = field.parent().parent();
				field_cell.prepend("<div class='dynoloader' style='display: none; z-index: 1000;'><img src='<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/images/loading.png' style='width: 20px;' /></div>");
		
				field.autocomplete({
					select: function(event, ui) {
						$(this).val(ui.item.value);
						if(ui.item.item_link) {
							window.location.href = ui.item.item_link;
						}
						<?php if($acounter) : ?>
						acounter<?php echo $module->id; ?>();
						<?php endif; ?>
						<?php if($onchange) : ?>
						if(!ui.item.item_link) {
							submit_form_<?php echo $module->id; ?>();
						}
						<?php endif; ?>
					},					
					source: function(request, response) {
						getSuggestions<?php echo $module->id; ?>(field, field_cell, response);
					},
					appendTo: field_cell,
				});
			});
		});
		
		function getSuggestions<?php echo $module->id; ?>(field, field_cell, response) {
			field_cell.find('.dynoloader').show();
			var url = jQuery("#K2FilterBox<?php echo $module->id; ?> form:eq(0)").attr("action");
			var data = jQuery("#K2FilterBox<?php echo $module->id; ?> form:eq(0)").find(":input").filter(function () {
							return jQuery.trim(this.value).length > 0
						}).serialize();
			var suggestion_type = field.attr('name'); 
			jQuery.ajax({
				data: data + "&format=suggestions&suggestion_type=" + suggestion_type,
				dataType: "jsonp",
				type: "get",
				url: url,
				success: function(res) {				
					var filteredArray = jQuery.map(res, function(item) {
						return item;
					});
					field_cell.find('.dynoloader').hide();
					response(filteredArray);
				},
			});
		}
	</script>