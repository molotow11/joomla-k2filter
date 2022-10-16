<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$search2 = JRequest::getVar('array'.$field->id, null);
$search = array();

(is_array($search2) == false) ?
	$search[] = $search2 :
	$search = $search2 ;
	
if($restmode == 1) {	
	$view = JRequest::getVar("view");
	$task = JRequest::getVar("task");
					
	if($view == "itemlist" && $task == "category") 
		$restcata = JRequest::getInt("id");
	else if($view == "item") {
		$id = JRequest::getInt("id");
		$restcata = modK2FilterHelper::getParent($id);
	}
	else {
		$restcata = JRequest::getVar("restcata");
	}
}

require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter'.DS.'models'.DS.'itemlistfilter.php');

JRequest::setVar("task", "filter");
JRequest::setVar("moduleId", $module->id);
JRequest::setVar("restcata", $restcata);

///////////// GET GLOBAL VALUE COUNTER
//reset selected value
$tmp = Array();
foreach($_GET as $param=>$value) {
	if(preg_match('/searchword.*/', $param)) {
		$tmp[$param] = $value;
		JRequest::setVar($param, '');
	}
	if(preg_match('/array.*/', $param)) {
		$tmp[$param] = $value;
		JRequest::setVar($param, '');
	}	
}

foreach ($field->content as $key=>$value) {
	JRequest::setVar("searchword".$field->id, $value);
	$counter[$field->id . "-" . $key][0] = (int)K2ModelItemlistfilter::getTotal();
}

//////////// GET VALUES MAP
if($field->name == "Hersteller") {
?>
	<script>
		if (typeof values_map === 'undefined') {
			var values_map = [];
		}
<?php
	foreach($field_types as $key=>$filter) {
		//current filter
		if(count($filter->content)) {
			foreach($filter->content as $key2=>$value) {

				if($counter[$filter->id . "-" . $key2][0] == 0) continue;
			
				JRequest::setVar("searchword".$filter->id, $value);
				foreach($field_types as $key2=>$filter2) {
					if($key == $key2) continue;
					//iterate another fields
					if(count($filter2->content)) {
						foreach($filter2->content as $key3=>$value2) {
						
							if($counter[$filter2->id . "-" . $key3][0] == 0) continue;
						
							JRequest::setVar("searchword".$filter2->id, $value2);
							$counter2 = (int)K2ModelItemlistfilter::getTotal();
							//echo "console.log('" . $value . " + " . $value2 . " = " . $counter2 . "');";
							echo "values_map.push(['{$value}', '{$value2}', {$counter2}]);";
						}
						JRequest::setVar("searchword".$filter2->id, "");
					}
				}
			}
			JRequest::setVar("searchword".$filter->id, "");
		}
	}
?>		
		jQuery(document).ready(function() {
			jQuery(".k2filter-field-multi input").on("change", function() {
				//console.log(jQuery(this).val());
				//console.log(jQuery(this).prop("checked"));
				
				//check event
				if(jQuery(this).prop("checked")) {
					//disable for category checkboxes
					if(jQuery(this).parents('div.Unterkategorien').length) {
						return false;
					}
				
					var clicked = jQuery(this);
					values_map.each(function(val) {
						if(val[0] == clicked.val()) {
							if(val[2] != 0) {
								jQuery(".k2filter-field-multi input").each(function() {
									if(jQuery(this).val() == val[1]) {
										jQuery(this).attr("checked", "checked");
									}
								});
							}
							else {
								//uncheck some values
							}
						}
					});
				}
				//uncheck event
				else {
					//enable only for category checkboxes
					if(jQuery(this).parents('div.Unterkategorien').length == 0) {
						return false;
					}	
			
					var checked = jQuery(this).val();
					values_map.each(function(val) {
						if(val[0] == checked && val[2] != 0) {
							jQuery(".k2filter-field-multi input").each(function() {
								if(jQuery(this).val() == val[1]) {
									jQuery(this).removeAttr("checked");
								}
							});						
						}
					});
				}
			});
		});
	</script>
<?php
}
/////////////////

//reset selected value
JRequest::setVar("searchword".$field->id, '');
if(count($tmp)) {
	foreach($tmp as $param=>$value) {
		JRequest::setVar($param, $value);
	}
}

///////////// GET VALUE COUNTER BASED ON CURRENT SEARCH
JRequest::setVar('array'.$field->id, '');
foreach ($field->content as $key=>$value) {
	JRequest::setVar("searchword".$field->id, $value);
	$counter[$field->id . "-" . $key][1] = (int)K2ModelItemlistfilter::getTotal();
	$counter[$field->id . "-" . $key][2] = $value;
}

// return the values
JRequest::setVar("searchword".$field->id, '');
if(count($tmp)) {
	foreach($tmp as $param=>$value) {
		JRequest::setVar($param, $value);
	}
}

?>

	<script type="text/javascript">	
		jQuery(document).ready(function () {
			<?php if($elems > 0) : ?>
			jQuery("div.filter<?php echo $field->id; ?>_hidden").hide();
			jQuery("a.expand_filter<?php echo $field->id; ?>").click(function() {
				jQuery("div.filter<?php echo $field->id; ?>_hidden").slideToggle("fast");
				return false;
			});
			<?php endif; ?>
		});
	</script>
	
	<style>
		.k2filter-field-multi .entry.grayed { opacity: 0.3; }
		#K2FilterBox<?php echo $module->id; ?> .k2filter-cell<?php echo $k; ?> .entry.disabled { display: none; }
		.Hersteller .entry { position: relative; }
		.Hersteller .counter { position: absolute; top: -11px; left: 0px; text-indent: ;-21px; }
	</style>

	<div class="k2filter-field-multi">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<div class="<?php echo $field->name; ?>">
			<?php
				$switch = 0;
				foreach ($field->content as $which=>$value) {
					if($elems > 0 && ($which+1) > $elems && $switch != 1) {
						echo "<div class='filter".$field->id."_hidden'>";
						$switch = 1;
					}
					$checked = 0;
					foreach ($search as $searchword) {
						if($searchword == $value) {
							$checked = 1;
						}
					}					
					
					$class = $value;
					if($counter[$field->id . "-" . $which][0] == 0) $class .= " disabled";
					if($counter[$field->id . "-" . $which][1] == 0) $class .= " grayed";
					
					echo '<div class="entry '.$class.'"><input name="array'.$field->id.'[]" type="checkbox" value="'.$value.'" id="field'.$module->id.$k.$which.'"';
					if($checked) echo " checked='checked'";
					if($onchange) {
						echo " onchange='submit_form_".$module->id."()'";
					}
					echo ' /><label class="'.$class.'" for="field'.$module->id.$k.$which.'">'.$value.' <span class="counter">('.$counter[$field->id . "-" . $which][0].')</span></label></div>';
				}
				if($elems > 0 && $switch == 1) echo "</div>";
			?>
		</div>
		<?php if($elems > 0 && count($field->content) > $elems) : ?>
		<p>
			<a href="#" class="button expand expand_filter<?php echo $field->id; ?>"><?php echo JText::_("MOD_K2_FILTER_MORE"); ?></a>
		</p>			
		<?php endif; ?>
	</div>

