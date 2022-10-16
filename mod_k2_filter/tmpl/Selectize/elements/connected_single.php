<script type="text/javascript">
	<?php 
		$connected = Array();
		$string = '';
		foreach($connected_fields as $connection) {
			$fields = Array();
			foreach($connection as $field) {
				$fields[] = '"'.trim(preg_replace('/\s\s+/', '', $field)).'"';
			}
			$connected[] = "[".implode(", ", $fields)."]";
		}
		$string = implode(", ", $connected);
		
		$fields = Array();
		foreach($field_types as $field) {
			$fields[] = '"'.$field->name.'"';
		}
		$fields = implode(",", $fields);
	?>
	var filter_fields<?php echo $module->id;?> = [<?php echo $fields; ?>];
	var connected<?php echo $module->id;?> = [<?php echo $string; ?>];
	jQuery(document).ready(function() {
		//add classes for parents and childs
		jQuery(connected<?php echo $module->id;?>).each(function(connectionIndex, connection) {
			jQuery(connection).each(function(index, field) {
				var role = index == 0 ? 'parent' : 'child';
				var index_in_filter = filter_fields<?php echo $module->id;?>.indexOf(field);
				var selectBox = jQuery("#K2FilterBox<?php echo $module->id;?>").find("div.k2filter-cell").eq(index_in_filter).find("select:visible");
				selectBox.addClass("connected");
				selectBox.addClass(role);
				if(role == "child") {
					<?php if(!$connected_show_all) { ?>
					selectBox.attr("disabled", "disabled");
					<?php } ?>
				}
				
				if(index == (connection.length - 1)) {
					selectBox.addClass("lastchild");
					return;
				}
				
				//trigger default values
				var nextSelectIndex  = index_in_filter + 1;
				var nextSelect = jQuery("#K2FilterBox<?php echo $module->id;?>").find("div.k2filter-cell").eq(nextSelectIndex).find("select");
				nextSelect.find("option").each(function(k) {
					var checker = 0;
					var current_option = jQuery(this);
					selectBox.find("option:selected").each(function() {
						var selectedVal = jQuery(this).val();
						if(current_option.val().indexOf(selectedVal) > -1) {
							checker = 1;
						}
					});

					if(checker == 0) {
						jQuery(this).hide();
						if(jQuery(nextSelect).is(":hidden")) { //in this case it is multiselect and need to update
							//jQuery(nextSelect).multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
						}
					}
				});
			});
		});
		
		//onchange event
		jQuery("#K2FilterBox<?php echo $module->id;?> select.connected").on("change", function() {
			if(jQuery(this).hasClass("lastchild")) return;
			var select = jQuery(this);
			var selectedVals = jQuery(this)[0].selectize.getValue();
			if(!Array.isArray(selectedVals)) {
				selectedVals = [selectedVals];
			}
			var elemIndex = jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').index(this);
			var nextAll  = jQuery(this).parents('#K2FilterBox<?php echo $module->id; ?>').find('select.connected:gt('+elemIndex+')');
			nextAll.each(function(index, nextSelect) {
				<?php if(!$connected_show_all) { ?>
				jQuery(nextSelect).attr("disabled", "disabled");
				<?php } ?>
				
				//clear the values for all next select boxes
				jQuery(this)[0].selectize.setValue(0);
				
				if(index == 0) { //apply only for first next select
					jQuery.each(jQuery(nextSelect).siblings().find("select.values-backup option"), function(k, current_option) {
						var checker = 0
						current_option.disabled = false; //clear disabled flag
						jQuery(selectedVals).each(function(k, text) {
							next_val = current_option.text.replace(/[,-. ]/g, "");
							curr_val = text.replace(/[,-. ]/g, "");
							if(next_val.indexOf(curr_val) > -1) checker = 1;
						});
						if(selectedVals.length == 0) checker = 1;
						if(checker == 0) {
							//hide
							current_option.disabled = true;
						}
						else {
							//show
							current_option.disabled = false;
						}
					});
				}
				//do not touch another chains
				if(jQuery(this).hasClass("lastchild")) return false;
			});
		});
	});
</script>