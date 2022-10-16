<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("input.datepicker").datepicker({ 
			closeText: "<?php echo JText::_('Done'); ?>",
			prevText: "<?php echo JText::_('Prev'); ?>",
			nextText: "<?php echo JText::_('Next'); ?>",
			currentText: "<?php echo JText::_('Today'); ?>",
			monthNames: ["<?php echo JText::_('January'); ?>", "<?php echo JText::_('February'); ?>", "<?php echo JText::_('March'); ?>", "<?php echo JText::_('April'); ?>", "<?php echo JText::_('May'); ?>", "<?php echo JText::_('June'); ?>", "<?php echo JText::_('July'); ?>", "<?php echo JText::_('August'); ?>", "<?php echo JText::_('September'); ?>", "<?php echo JText::_('October'); ?>", "<?php echo JText::_('November'); ?>", "<?php echo JText::_('December'); ?>"],
			monthNamesShort: ["<?php echo JText::_('Jan'); ?>", "<?php echo JText::_('Feb'); ?>", "<?php echo JText::_('Mar'); ?>", "<?php echo JText::_('Apr'); ?>", "<?php echo JText::_('May'); ?>", "<?php echo JText::_('Jun'); ?>", "<?php echo JText::_('Jul'); ?>", "<?php echo JText::_('Aug'); ?>", "<?php echo JText::_('Sep'); ?>", "<?php echo JText::_('Oct'); ?>", "<?php echo JText::_('Nov'); ?>", "<?php echo JText::_('Dec'); ?>"],
			dayNames: ["<?php echo JText::_('Sunday'); ?>", "<?php echo JText::_('Monday'); ?>", "<?php echo JText::_('Tuesday'); ?>", "<?php echo JText::_('Wednesday'); ?>", "<?php echo JText::_('Thursday'); ?>", "<?php echo JText::_('Friday'); ?>", "<?php echo JText::_('Saturday'); ?>"],
			dayNamesShort: ["<?php echo JText::_('Sun'); ?>", "<?php echo JText::_('Mon'); ?>", "<?php echo JText::_('Tue'); ?>", "<?php echo JText::_('Wed'); ?>", "<?php echo JText::_('Thu'); ?>", "<?php echo JText::_('Fri'); ?>", "<?php echo JText::_('Sat'); ?>"],
			dayNamesMin: ["<?php echo JText::_('Su'); ?>", "<?php echo JText::_('Mo'); ?>", "<?php echo JText::_('Tu'); ?>", "<?php echo JText::_('We'); ?>", "<?php echo JText::_('Th'); ?>", "<?php echo JText::_('Fr'); ?>", "<?php echo JText::_('Sa'); ?>"],
			weekHeader: "<?php echo JText::_('Wk'); ?>",
			dateFormat: "yy-mm-dd",
		});
	});
</script>

	<div class="k2filter-field-publishing">
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_PUBLISHING_END'); ?>
		</h3>
		
		<input class="datepicker inputbox" name="publish_down" type="text" <?php if (JRequest::getVar('publish_down')) echo ' value="'.JRequest::getVar('publish_down').'"'; ?> />
	</div>

