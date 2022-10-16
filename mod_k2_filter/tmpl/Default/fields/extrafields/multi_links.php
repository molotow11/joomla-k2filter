<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$search2 = JRequest::getVar('array'.$field->id, null);
$search = array();

(is_array($search2) == false) ?
	$search[] = $search2 :
	$search = $search2 ;
?>

	<div class="k2filter-field-multi">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<div>
			<?php
				foreach ($field->content as $which=>$value) {
					echo "<a href='#' onClick='document.K2Filter".$module->id.".array".$field->id.".value=this.text; submit_form_".$module->id."(); return false;'>".$value."</a>";
				echo "<br />";
				}
			?>
		</div>
		
		<input name="array<?php echo $field->id; ?>[]" type="hidden" value="<?php echo JRequest::getVar("array".$field->id); ?>" />
	</div>

