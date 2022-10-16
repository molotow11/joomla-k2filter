<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-select">
		
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_($field->name); ?>
		</h3>
		<?php endif; ?>
		
		<select name="searchword<?php echo $field->id; ?>" <?php if($onchange) : ?>onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> placeholder="<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.JText::_($field->name); ?>">
			<option value="" class="empty"><?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.JText::_($field->name); ?></option>
			<?php
			
				if($values) {
					foreach ($values as $value) {
						echo '<option ';
						if (JRequest::getVar('searchword'.$field->id) == $value) {echo 'selected="selected"';}
						echo '>'.$value.'</option>';
					}
				}
			?>
		</select>
	</div>
    


