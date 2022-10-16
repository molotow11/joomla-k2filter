<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JRequest::getVar('taga', null);
if(JRequest::getVar("task") == "tag") {
	$checked = Array(JRequest::getVar("tag"));
}	

?>

	<?php if($elems > 0) : ?>
	<script type="text/javascript">
	
		jQuery(document).ready(function () {
			jQuery("div.filter_tag_hidden").hide();
			jQuery("a.expand_filter_tag").click(function() {
				jQuery("div.filter_tag_hidden").slideToggle("fast");
				return false;
			});
			
			<?php if($onchange) : ?>
			jQuery("#K2FilterBox<?php echo $module->id; ?> input:checkbox").change(function() {
				jQuery("#K2FilterBox<?php echo $module->id; ?> form").submit();
			});
			<?php endif; ?>
		});
	
	</script>
	<?php endif; ?>
	
	<div class="k2filter-field-tag-multi">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TAG'); ?>
		</h3>
		<div>
		<?php
			$switch = 0;
			foreach ($tags as $which=>$tag) {
				if($elems > 0 && ($which+1) > $elems && $switch != 1) {
					echo "<div class='filter_tag_hidden'>";
					$switch = 1;
				}
				echo '<input name="taga[]" type="checkbox" value="'.$tag->id.'" id="'.str_replace(" ", "_", $tag->tag).'_id"';
				foreach ($checked as $check) {
					if ($check == $tag->id) echo 'checked="checked"';
				}
				if($onchange) {
					echo " onchange='submit_form_".$module->id."()'";
				}
				echo ' /><label for="'.str_replace(" ", "_", $tag->tag).'_id">'.$tag->tag.'</label>';
			}
			if($elems > 0) echo "</div>";
		?>
		</div>
		<?php if($elems > 0 && count($tags) > $elems) : ?>
		<p>
			<a href="#" class="button expand expand_filter_tag"><?php echo JText::_("MOD_K2_FILTER_MORE"); ?></a>
		</p>			
		<?php endif; ?>
	</div>

