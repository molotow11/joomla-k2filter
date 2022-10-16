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
		
		<select class="connected" name="searchword<?php echo $field->id; ?>" <?php if($onchange) : ?>onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> rel="<?php echo $field->name; ?>">
			<option value=""><?php echo '-- '.JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.$field->name.' --'; ?></option>
			<?php
				foreach ($field->content as $value) {
					echo '<option ';
					if (JRequest::getVar('searchword'.$field->id) == $value) {echo 'selected="selected"';}
					echo '>'.$value.'</option>';
				}
			?>
		</select>
	</div>
    


