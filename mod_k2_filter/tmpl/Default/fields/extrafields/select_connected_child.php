<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="k2filter-field-select k2filter-field-connected">
		
		<?php if($showtitles) : ?>
		<h3>
			<?php echo $connected_name; ?>
		</h3>
		<?php endif; ?>
		
		<select class="connected child<?php echo $last_child; ?>" disabled <?php if($onchange) : ?>onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> rel="<?php echo $connected_name; ?>">
			<option value=""><?php echo '-- '.JText::_('MOD_K2_FILTER_FIELD_SELECT_DEFAULT').' '.$connected_name.' --'; ?></option>
		</select>
	</div>
    


