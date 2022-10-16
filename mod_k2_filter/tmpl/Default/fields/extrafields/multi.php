<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$search2 = JRequest::getVar('array'.$field->id, null);
$search = array();

(is_array($search2) == false) ?
	$search[] = $search2 :
	$search = $search2 ;
?>
	
	<?php if($elems > 0) : ?>
	<script type="text/javascript">
		jQuery(document).ready(function () {
			jQuery("div.filter<?php echo $field->id; ?>_hidden").hide();
			jQuery("a.expand_filter<?php echo $field->id; ?>").click(function() {
				jQuery("div.filter<?php echo $field->id; ?>_hidden").slideToggle("fast");
				return false;
			});
		});
	</script>
	<?php endif; ?>
	
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
					echo ' /><label for="field'.$module->id.$k.$which.'">'.$value.'</label>';
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

