
jQuery.noConflict();
jQuery(document).ready(function() {
	var filtersVal = jQuery("#FiltersListVal");	
	var type_select = 
			"<select class='field_type_select'>" + 
				"<option value=''>" + MOD_K2_FILTER_SELECT_FIELD_TYPE + "</option>" +			
				"<option value='text'>" + MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD + "</option>" +			
				"<option value='text_range'>" + MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_RANGE + "</option>" +			
				"<option value='text_date'>" + MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE + "</option>" +			
				"<option value='text_date_range'>" + MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE_RANGE + "</option>" +
				"<option value='text_az'>" + MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_AZ + "</option>" +
				"<option value='select'>" + MOD_K2_FILTER_FILTER_TYPE_DROPDOWN + "</option>" +
				"<option value='select_autofill'>" + MOD_K2_FILTER_FILTER_TYPE_DROPDOWN_AUTOFILL + "</option>" +
				"<option value='multi'>" + MOD_K2_FILTER_FILTER_TYPE_MULTI_CHECKBOX + "</option>" +
				"<option value='multi_select'>" + MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT + "</option>" +
				"<option value='multi_select_autofill'>" + MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT_AUTOFILL + "</option>" +
				"<option value='slider'>" + MOD_K2_FILTER_FILTER_TYPE_SLIDER + "</option>" +
				"<option value='slider_range'>" + MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE + "</option>" +
				"<option value='slider_range_autofill'>" + MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE_AUTOFILL + "</option>" +
				"<option value='radio'>" + MOD_K2_FILTER_FILTER_TYPE_RADIO + "</option>" +
				"<option value='label'>" + MOD_K2_FILTER_FILTER_TYPE_LABEL_MANUAL + "</option>" +
				"<option value='number'>Number</option>" +
			"</select>";
	
	if(filtersVal.val() != '') {
		var filterValues = filtersVal.val().split("\n");
		for(var i = 0; i < filterValues.length; i++) {
			var title = ''; 
			jQuery('select.FilterSelect').find("option").each(function () {
				if(filterValues[i].split(":")[0] == "extrafield") {
					if(jQuery(this).val().split(":")[1] == filterValues[i].split(":")[1]) {
						title = jQuery(this).text();
					}
				}
				else {
					if(jQuery(this).val() == filterValues[i]) {
						title = jQuery(this).text();
					}					
				}
			});
			
			// adds type select for extrafields
			var type_selected = jQuery(type_select);
			if(filterValues[i].split(":")[0] == "extrafield") {
				var selected = filterValues[i].split(":")[2];
				type_selected.find("option").each(function() {
					if(jQuery(this).val() == selected) {
						jQuery(this).attr("selected", "selected");
					}
				});
				type_selected = "<select class='field_type_select'>" + type_selected.html() + "</select>";
			}
			else {
				type_selected = '';
			}
			jQuery("#sortableFields").append("<li><span class='val' rel='"+filterValues[i]+"'>" + 
			title + "</span><span class='sortableRightBlock'>" + type_selected + "<span class='deleteFilter'>x</span></span></li>");
		}
	}
	
	jQuery("#sortableFields").sortable({update: updateFiltersVal});
	
	jQuery("body").on('click', '#sortableFields .deleteFilter', function() {
		jQuery(this).parent().parent().remove();
		updateFiltersVal();
	});
	
	jQuery("body").on('change', '#sortableFields .field_type_select', function() {
		var selected = jQuery(this).find("option:selected").val();
		var value = jQuery(this).parent().siblings(".val").attr("rel").split(":");
		jQuery(this).parent().siblings(".val").attr("rel", value[0] + ":" + value[1] + ":" + selected)
		updateFiltersVal();
	});
	
	jQuery('.FilterSelect').on('change', function() {
		var selected = jQuery(this).find('option:selected');
		if(selected.val() != '' && selected.val() != 0) {
			var type_selected = type_select;
			if(selected.val().split(":")[0] != "extrafield") {
				type_selected = '';
			}	
			jQuery("#sortableFields").append("<li><span class='val' rel='"+selected.val()+"'>"+ 
			selected.text() +"</span><span class='sortableRightBlock'>" + type_selected + "<span class='deleteFilter'>x</span></span></li>");
			
			updateFiltersVal();
		}		
		jQuery('.FilterSelect').val(0).trigger('liszt:updated');		
	});
});

function updateFiltersVal() {
	var FiltersVal = '';
	jQuery("#sortableFields li span.val").each(function(count) {
		if(count > 0) {
			FiltersVal = FiltersVal + "\r\n";
		}
		FiltersVal = FiltersVal + jQuery(this).attr("rel");
	});
	jQuery("#FiltersListVal").val(FiltersVal);
	//console.log("---------------------");
	//console.log(jQuery("#FiltersListVal").val());
}