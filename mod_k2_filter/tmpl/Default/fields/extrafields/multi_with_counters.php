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

//reset selected value
$tmp = JRequest::getVar("array".$field->id);
JRequest::setVar("array".$field->id, '');

$counter = Array();
foreach ($field->content as $value) {
	JRequest::setVar("searchword".$field->id, $value);
	$counter[] = (int)K2ModelItemlistfilter::getTotal();
}
//reset selected value
JRequest::setVar("searchword".$field->id, '');
JRequest::setVar("array".$field->id, $tmp);

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

	<div class="k2filter-field-multi">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<div>
			<?php
				$switch = 0;

				foreach ($field->content as $which=>$value) {
					if($elems > 0 && ($which+1) > $elems && $switch != 1) {
						echo "<div class='filter".$field->id."_hidden'>";
						$switch = 1;
					}
					echo '<input name="array'.$field->id.'[]" type="checkbox" value="'.$value.'" id="field'.$module->id.$k.$which.'"';
					foreach ($search as $searchword) {
						if ($searchword == $value) echo 'checked="checked"';
					}
					if($onchange) {
						echo " onchange='submit_form_".$module->id."()'";
					}
					$w = $which - 1;
					echo ' /><label for="field'.$module->id.$k.$which.'">'.$value.' ('.$counter[$w].')</label><br />';
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

