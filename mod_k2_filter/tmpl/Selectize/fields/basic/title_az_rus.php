<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<script>
	jQuery(document).ready(function() {
		var ftitle_az = jQuery("input[name=ftitle_az]").val();
		
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").each(function() {
			if(ftitle_az == jQuery(this).text() || (ftitle_az == "num" && jQuery(this).text() == "#")) {
				jQuery(this).css("font-weight", "bold").addClass("active");
			}
		});
	
		jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").click(function() {
			jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").css("font-weight", "normal");

			if(jQuery(this).hasClass("active") == 0) {
				jQuery("#K2FilterBox<?php echo $module->id; ?> a.title_az").removeClass("active");
				jQuery(this).css("font-weight", "bold").addClass("active");
				jQuery("input[name=ftitle_az]").val(jQuery(this).html());
				if(jQuery(this).html() == "#") {
					jQuery("input[name=ftitle_az]").val("num");
				}
			}
			else {
				jQuery(this).css("font-weight", "normal").removeClass("active");
				jQuery("input[name=ftitle_az]").val("");
			}
			
			<?php if($onchange) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			
			return false;
		});
	});
</script>

	<div class="k2filter-field-title-az">
	
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_TITLE_AZ'); ?>
		</h3>
		<?php endif; ?>

		<a class="title_az" href="#">#</a>
		<a class="title_az" href="#">А</a>
		<a class="title_az" href="#">Б</a>
		<a class="title_az" href="#">В</a>
		<a class="title_az" href="#">Г</a>
		<a class="title_az" href="#">Д</a>
		<a class="title_az" href="#">Е</a>
		<a class="title_az" href="#">Ж</a>
		<a class="title_az" href="#">З</a>
		<a class="title_az" href="#">И</a>
		<a class="title_az" href="#">К</a>
		<a class="title_az" href="#">Л</a>
		<a class="title_az" href="#">М</a>
		<a class="title_az" href="#">Н</a>
		<a class="title_az" href="#">О</a>
		<a class="title_az" href="#">П</a>
		<a class="title_az" href="#">Р</a>
		<a class="title_az" href="#">С</a>
		<a class="title_az" href="#">Т</a>
		<a class="title_az" href="#">У</a>
		<a class="title_az" href="#">Ф</a>
		<a class="title_az" href="#">Х</a>
		<a class="title_az" href="#">Ц</a>
		<a class="title_az" href="#">Ч</a>
		<a class="title_az" href="#">Ш</a>
		<a class="title_az" href="#">Щ</a>
		<a class="title_az" href="#">Э</a>
		<a class="title_az" href="#">Ю</a>
		<a class="title_az" href="#">Я</a>
		
		<input name="ftitle_az" type="hidden" <?php if (JRequest::getVar('ftitle_az')) echo ' value="'.JRequest::getVar('ftitle_az').'"'; ?> />
	</div>

