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
				var selectBox = jQuery("#K2FilterBox<?php echo $module->id;?>").find("div.k2filter-cell").eq(index_in_filter).find("select");
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
					if(jQuery(this).hasClass("empty")) return; //first empty value of the select boxes
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
							jQuery(nextSelect).multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
						}
					}
				});
			});
		});
		
		//onchange event
		jQuery("#K2FilterBox<?php echo $module->id;?> select.connected").on("change", function() {
			if(jQuery(this).hasClass("lastchild")) return;
			var selectedVals = jQuery(this).find("option:selected");
			var elemIndex = jQuery('#K2FilterBox<?php echo $module->id; ?> select.connected').index(this);
			var nextAll  = jQuery(this).parents('#K2FilterBox<?php echo $module->id; ?>').find('select.connected:gt('+elemIndex+')');
			nextAll.each(function(index, nextSelect) {
				<?php if(!$connected_show_all) { ?>
				jQuery(nextSelect).attr("disabled", "disabled");
				<?php } ?>
				if(selectedVals.attr("class") == "empty") {
					jQuery(nextSelect).find("option.empty").attr("selected", "selected");
					<?php if($connected_show_all) { ?>
					jQuery(this).find("option").each(function(k) {
						//show
						if(jQuery(this).parent('span').length) {
							jQuery(this).addClass("unwrap");
						}
						jQuery(this).show().removeAttr("hidden");	
					});
					jQuery("#K2FilterBox<?php echo $module->id;?> option.unwrap").unwrap().removeClass("unwrap");
					<?php } ?>
					return;
				}
				
				//clear the values
				jQuery(this).find("option").eq(0).prop("selected", "selected");
				if(jQuery(this).is(":hidden")) {
					jQuery(this).multiselect("uncheckAll").multiselect("refresh");
				}
				jQuery(this).find("option").each(function() {
					if(jQuery(this).parent('span').length) {
						jQuery(this).unwrap();
					}
				});
				//
				if(index == 0) {
					jQuery(this).find("option").each(function(k) {
						if(jQuery(this).hasClass("empty")) return; //first empty value of the select boxes
						var current_option = jQuery(this);
						var checker = 0
						
						jQuery(selectedVals).each(function() {
							//remove parent title from the value
							//current_option.text(current_option.val().split(jQuery(this).val())[1]);
							curr_val = current_option.val().replace(/[,-. ]/g, "");
							next_val = jQuery(this).val().replace(/[,-. ]/g, "");
							if(curr_val.indexOf(next_val) > -1) checker = 1;
						});
						
						if(selectedVals.length == 0) checker = 1;
						if(checker == 0) {
							//hide
							jQuery(this).hide().attr("hidden", "hidden").addClass("wrap");
							if(jQuery(nextSelect).is(":hidden")) { //in this case it is multiselect and need to update
								jQuery(nextSelect).multiselect("widget").find(".ui-multiselect-checkboxes li").eq(k).hide();
							}
						}
						else {
							//show
							if(jQuery(this).parent('span').length) {
								jQuery(this).addClass("unwrap");
							}
							jQuery(this).show().removeAttr("hidden");
						}
					});
					jQuery(nextSelect).removeAttr("disabled");
				}
				//do not touch another chains
				if(jQuery(this).hasClass("lastchild")) return false;
			});
			jQuery("#K2FilterBox<?php echo $module->id;?> option.wrap").wrap("<span style='display: none;'></span>").removeClass("wrap");
			jQuery("#K2FilterBox<?php echo $module->id;?> option.unwrap").unwrap().removeClass("unwrap");
		});
	});
</script>