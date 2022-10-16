<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<script>
	jQuery(document).ready(function() {
		jQuery('a.uncheck_filter<?php echo $field->id; ?>').click(function () {
			jQuery('input[name=searchword<?php echo $field->id; ?>]').removeAttr('checked');
			<?php if($onchange) : ?>
			jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit();
			<?php endif; ?>
			return false;
		});
		<?php if($elems > 0) : ?>
		jQuery("div.filter<?php echo $field->id; ?>_hidden").hide();
		jQuery("a.expand_filter<?php echo $field->id; ?>").click(function() {
			jQuery("div.filter<?php echo $field->id; ?>_hidden").slideToggle("fast");
			return false;
		});
		<?php endif; ?>

	});
</script>

	<div class="k2filter-field-radio">
		<h3>
			<?php echo $field->name; ?>
		</h3>

			<?php
				if(count($field->content)) {
					foreach ($field->content as $which=>$value) {
						if($elems > 0 && ($which - 1) == $elems) {
							echo "<div class='filter".$field->id."_hidden'>";
							$switch = 1;
						}
						echo '<input name="searchword'.$field->id.'" type="radio" value="'.$value.'" id="'.$module->id.$k.$which.'"';
						if (JRequest::getVar('searchword'.$field->id) == $value) echo ' checked="checked"';
						if($onchange) echo ' onchange="submit_form_'.$module->id.'()"';
						echo ' /><label for="'.$module->id.$k.$which.'">'.$value.'</label>';
					}
					if($elems > 0 && count($field->content) > $elems) echo "</div>";
				}
			?>	
			
			<div class="K2FilterClear"></div>
			
			<?php if (!$clear_btn) : ?>
			<p>
				<a href="#" class="button uncheck uncheck_filter<?php echo $field->id; ?>"><?php echo JText::_("MOD_K2_FILTER_UNCHECK"); ?></a>
			</p>
			<?php endif; ?>
			
			<?php if($elems > 0 && count($field->content) > $elems) : ?>
			<p>
				<a href="#" class="button expand expand_filter<?php echo $field->id; ?>"><?php echo JText::_("MOD_K2_FILTER_MORE"); ?></a>
			</p>			
			<?php endif; ?>

	</div>

