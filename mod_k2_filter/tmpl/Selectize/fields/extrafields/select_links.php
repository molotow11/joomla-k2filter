<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-select">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<?php endif; ?>

		<?php
			sort($field->content);
			
			foreach ($field->content as $value) {
				echo "<a ";
				if(JRequest::getVar('searchword'.$field->id) == $value) {
				echo "style='font-weight: bold;' ";
				}
				echo "href='#' onClick='document.K2Filter".$module->id.".searchword".$field->id.".value=this.text; submit_form_".$module->id."(); return false;'>".$value."</a>";
				echo "<br />";
			}
		?>
		
		<input name="searchword<?php echo $field->id; ?>" value="<?php echo JRequest::getVar('searchword'.$field->id); ?>" type="hidden" />
	</div>
    


