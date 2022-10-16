<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.framework');
JHtml::_('jquery.ui');

$m = (int)date("n") - 1;
$today = date("Y,{$m},j");
$today_val = $today;
if($_GET['created-from']) {
	$date = DateTime::createFromFormat('Y-m-d', $_GET['created-from']);
	$m = (int)$date->format("n") - 1;
	$today_val = $date->format("Y,{$m},j");
}

$m_offset = $_GET['date-range-months'] ? (int)$_GET['date-range-months'] : 3;
$m = $m + $m_offset;
$offset = date("Y,{$m},j");
$offset_val = $offset;
if($_GET['created-to']) {
	$date = DateTime::createFromFormat('Y-m-d', $_GET['created-to']);
	$m = (int)$date->format("n") - 1;
	$offset_val = $date->format("Y,{$m},j");
}

?>

<link rel="stylesheet" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jQRangeSlider/iThing-min.css" />
<script src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jQRangeSlider/jQDateRangeSlider-min.js"></script>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("#created-date-slider").dateRangeSlider({
			bounds: {min:new Date(<?php echo $today; ?>), max:new Date(<?php echo $offset; ?>)},
			defaultValues : {min:new Date(<?php echo $today_val; ?>), max:new Date(<?php echo $offset_val; ?>)}
		});
		$("#created-date-slider").bind("valuesChanged", function(e, data) {
			//min val
			var date = new Date(data.values.min);
			var day = date.getDate();
			var month = date.getMonth() + 1;
			if (month.toString().length < 2) 
				month = '0' + month;
			if (day.toString().length < 2) 
				day = '0' + day;
			var year = date.getFullYear();
			$(this).parent().find("input[name=created-from]").val(year + "-" + month + "-" + day);
			
			//max val
			var date = new Date(data.values.max);
			var day = date.getDate();
			var month = date.getMonth() + 1;
			if (month.toString().length < 2) 
				month = '0' + month;
			if (day.toString().length < 2) 
				day = '0' + day;
			var year = date.getFullYear();
			$(this).parent().find("input[name=created-to]").val(year + "-" + month + "-" + day);
		});
	});
</script>

	<div class="k2filter-field-created">	
		<h3>
			<?php echo JText::_('MOD_K2_FILTER_FIELD_CREATED'); ?>
		</h3>
		
		<div id="created-date-slider"></div>
		
		<input name="created-from" type="hidden" <?php if (JRequest::getVar('created-from')) echo ' value="'.JRequest::getVar('created-from').'"'; ?> />
		<input name="created-to" type="hidden" <?php if (JRequest::getVar('created-to')) echo ' value="'.JRequest::getVar('created-to').'"'; ?> />
	</div>

