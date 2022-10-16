<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

	<div class="k2filter-field-category-checkbox">
		<?php if($showtitles) : ?>
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_SELECT_CATEGORY_HEADER'); ?>
		</h3>
		<?php endif; ?>
		
		<div class="options_container">
		<?php echo $category_options; ?>
		</div>
    </div>