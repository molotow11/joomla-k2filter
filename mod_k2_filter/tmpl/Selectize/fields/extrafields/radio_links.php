<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-radio">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		
		<div>
			<?php
				foreach ($field->content as $which=>$value) {
					echo "<a href='#'";
					if($value == JRequest::getVar('searchword'.$field->id)) {
						echo " onClick='document.K2Filter".$module->id.".searchword".$field->id.".value=\"\"; submit_form_".$module->id."(); return false;' class='active' style='font-weight: bold;'";
					}
					else {
						echo " onClick='document.K2Filter".$module->id.".searchword".$field->id.".value=jQuery(this).text(); submit_form_".$module->id."(); return false;'";
					}
					echo ">".$value."</a>";
					echo "<br />";
				}
			?>	
			
		<input name="searchword<?php echo $field->id; ?>" value="<?php echo JRequest::getVar('searchword'.$field->id); ?>" type="hidden" />
		
		</div>
	</div>

