<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<style>

#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.active {
	font-weight: bold;
}

</style>	
	
<script>
	jQuery(document).ready(function() {
		var search_az<?php echo $field->id; ?> = jQuery("input[name=search_az<?php echo $field->id; ?>]").val().split('');
		
		if(search_az<?php echo $field->id; ?>.length > 0) {
			jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").each(function() {
				var link_char = jQuery(this).text();
				var link = jQuery(this);
				search_az<?php echo $field->id; ?>.each(function(search_az) {
					if(search_az == link_char) {
						link.addClass("active");
					}
				});
			});
		}
	
		jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").on('click', function() {
			if(jQuery(this).hasClass("active")) {
				jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").removeClass("active");
			}
			else {
				jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").removeClass("active");
				jQuery(this).addClass("active");
			}
			
			var value = '';
			jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.active").each(function() {
				value += jQuery(this).text();
			});
			jQuery("input[name=search_az<?php echo $field->id; ?>]").val(value);
			
			<?php if($onchange) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			
			return false;
		});		
		
		jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az_all").on('click', function() {
			if(jQuery(this).hasClass("activeAll")) {
				jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").removeClass("active");
				jQuery(this).removeClass("activeAll");
			}
			else {
				jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $k; ?> a.search_az").addClass("active");
				jQuery(this).addClass("activeAll");
			}
			
			var value = '';
			jQuery("#K2FilterBox<?php echo $module->id; ?> .k2filter-field-<?php echo $field->id; ?> a.active").each(function() {
				value += jQuery(this).text();
			});
			jQuery("input[name=search_az<?php echo $field->id; ?>]").val(value);
			
			<?php if($onchange) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			
			return false;
		});
	});
	
<?php //Added for dynamic letters 

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

$letters = Array();
require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter'.DS.'models'.DS.'itemlistfilter.php');
$current_vals = Array();
foreach($_GET as $param=>$val) {
	preg_match('/^search_az([0-9]+)$/', $param, $matches);
	if(count($matches)) {
		$current_vals[$param] = $val;
		JRequest::setVar($param, '');
	}
}

JRequest::setVar("task", "filter");
JRequest::setVar("moduleId", $module->id);
JRequest::setVar("restcata", $restcata);

foreach(range('A', 'Z') as $j=>$letter) {
	$letters[$j][0] = $letter;
	JRequest::setVar("search_az".$field->id, $letter);
	$letters[$j][1] = (int)K2ModelItemlistfilter::getTotal();
	JRequest::setVar("search_az".$field->id, '');
}
if(count($current_vals)) {
	foreach($current_vals as $param=>$val) {
		JRequest::setVar($param, $val);
	}
}

?>
	
</script>

	<div class="k2filter-field-text-az k2filter-field-<?php echo $k; ?>">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<?php endif; ?>
		
		<a class="search_az_all" href="#" style="font-size: 12px; text-decoration: none;">ALL</a>
		
		<?php foreach($letters as $letter) : ?>
			<?php if($letter[1] > 0) : ?>
			<a class="search_az" href="#" style="font-size: 12px; text-decoration: none;"><?php echo $letter[0]; ?></a>
			<?php else : ?>
			<a class="search_az" href="#" style="font-size: 12px; text-decoration: none; opacity: 0.4;"><?php echo $letter[0]; ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
		
		<input name="search_az<?php echo $field->id; ?>" type="hidden" <?php if (JRequest::getVar('search_az'.$field->id)) echo ' value="'.JRequest::getVar('search_az'.$field->id).'"'; ?> />
	</div>

