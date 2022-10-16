<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<style>
.slider-rating { width: 125px !important; height: 25px !important; overflow: hidden !important; cursor: pointer; }
.slider-rating .rating-stars { width: 100%; height: 100%; background: transparent url('<?php echo JURI::root(); ?>/modules/mod_k2_filter/assets/images/transparent_star.gif') left 0px repeat-x; }
.slider-rating .ui-widget-header { margin-top: 1px; background: transparent url('<?php echo JURI::root(); ?>/modules/mod_k2_filter/assets/images/transparent_star.gif') left center repeat-x !important; }
.slider-rating .ui-slider-handle { display: none !important; }
</style>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(".slider-rating<?php echo $module->id.$field->id;?>")[0].slide = null;
		jQuery(".slider-rating<?php echo $module->id.$field->id;?>").slider({
			min: 0,
			max: 5,
			step: 1,
			values: [<?php echo JRequest::getInt('rating-from', 0); ?>, <?php echo JRequest::getInt('rating-to', 5) ; ?>],
			range: true,
			min: 0,
			max: 5,
			slide: function(event, ui) {
				jQuery(".slider-rating<?php echo $module->id.$field->id;?>-from").val(ui.values[0]);
				jQuery(".slider-rating<?php echo $module->id.$field->id;?>-to").val(ui.values[1]);
			},
			stop: function(event, ui) {
				<?php if($onchange) : ?>
				submit_form_<?php echo $module->id; ?>();
				<?php endif; ?>
				<?php if($acounter) : ?>
				acounter<?php echo $module->id; ?>();
				<?php endif; ?>
			}
		});
	});
</script>

	<div class="k2filter-field-rating">
	
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_ITEM_RATING'); ?>
		</h3>
		
		<div class="slider-rating slider-rating<?php echo $module->id.$field->id;?>">
			<div class="rating-stars"></div>
		</div>
		
		<input class="slider-rating<?php echo $module->id.$field->id;?>-from" type="hidden" name="rating-from" value="<?php echo JRequest::getVar('rating-from', ''); ?>" />
		<input class="slider-rating<?php echo $module->id.$field->id;?>-to" type="hidden" name="rating-to" value="<?php echo JRequest::getVar('rating-to', ''); ?>" />
	</div>

