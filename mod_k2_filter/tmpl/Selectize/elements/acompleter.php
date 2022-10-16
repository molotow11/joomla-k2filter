	<script type="text/javascript">
		$ = jQuery.noConflict();
		$(document).ready(function() {
			var fields = $("#K2FilterBox<?php echo $module->id; ?> input[name=fitem_all], #K2FilterBox<?php echo $module->id; ?> input[name=ftitle], #K2FilterBox<?php echo $module->id; ?> input[name=ftag]"); //works only for Keyword, Tiitle or Tag text fields
			
			fields.each(function(k, field) {
				field = $(field);
				var field_cell = field.parent().parent();
				field_cell.prepend("<div class='dynoloader' style='display: none; z-index: 1000;'><img alt='loading' src='<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/images/loading.png' style='width: 20px;' /></div>");
		
				field.autocomplete({
					<?php if($acounter) : ?>
					select: function(event, ui) {
						$(this).val(ui.item.value);
						acounter<?php echo $module->id; ?>()
					},
					<?php endif; ?>
					source: function(request, response) {
						getSuggestions(field, field_cell, response);
					},
				});
			});
		});
		
		function getSuggestions(field, field_cell, response) {
			$ = jQuery.noConflict();
			field_cell.find('.dynoloader').show();
			var url = "<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter'); ?>";
			var data = $("#K2FilterBox<?php echo $module->id; ?> form").find(":input").filter(function () {
							return $.trim(this.value).length > 0
						}).serialize();
			var suggestion_type = field.attr('name'); 
			$.ajax({
				data: data + "&format=suggestions&suggestion_type=" + suggestion_type,
				dataType: "jsonp",
				type: "get",
				url: url,
				success: function(res) {				
					var filteredArray = $.map(res, function(item) {
						return item;
					});
					field_cell.find('.dynoloader').hide();
					response(filteredArray);
				},
			});
		}
	</script>