<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

	$restcata = 0;					
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
	
	$db = JFactory::getDBO();
	$query = "SELECT extra_fields FROM #__k2_items WHERE published = 1";
	$db->setQuery($query);
	$results = $db->loadObjectList();
	$return = Array();
	if(count($results)) {
		require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
		foreach($results as $item) {
			$extras = json_decode($item->extra_fields);
			foreach($extras as $extra) {
				if($extra->id == $field->id && $extra->value != "") {
					$extra->type = modK2FilterHelper::pull($extra->id, 'type');
					if($extra->type == "text" || $extra->type == "textfield") {
						$return[] = $extra->value;
					}
					else {
						$extravalues = json_decode(modK2FilterHelper::pull($extra->id, 'value'));
						foreach($extravalues as $val) {
							if($val->value == $extra->value) {
								$return[] = $val->name;
							}
						}
					}
				}
			}
		}
		$return = array_unique($return);
		sort($return);
	}

?>

	<div class="k2filter-field-text">
		<h3>
			<?php echo $field->name; ?>
		</h3>	
		<input class="inputbox textfield<?php echo $module->id.$field->id; ?>" name="searchword<?php echo $field->id;?>" type="text" <?php if (JRequest::getVar('searchword').$field->id) echo ' value="'.JRequest::getVar('searchword'.$field->id).'"'; ?> />
	</div>

<?php if($acompleter) { ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		var availableTags<?php echo $module->id.$field->id; ?> = [			
			<?php 
			foreach($return as $a=>$val) {
				echo '"'.htmlspecialchars($val).'"';
				if(($a+1) != count($return)) {
					echo ", ";
				}
			}
			?>
		];
		
		jQuery(".textfield<?php echo $module->id.$field->id; ?>").autocomplete({
			<?php if($acounter) : ?>
			select: function(event, ui) {
				jQuery(this).val(ui.item.value);
				acounter<?php echo $module->id; ?>()
			},
			<?php endif; ?>
			source: function(request, response) {
				var filteredArray = jQuery.map(availableTags<?php echo $module->id.$field->id; ?>, function(item) {
					if(item.toUpperCase().indexOf(request.term.toUpperCase()) == 0){
						return item;
					}
					else{
						return null;
					}
				});
				if(filteredArray.length == 0) {
					response(["No results found."]);
				}
				else {
					response(filteredArray);
				}
			}
		});
	});
	</script>
<?php } ?>