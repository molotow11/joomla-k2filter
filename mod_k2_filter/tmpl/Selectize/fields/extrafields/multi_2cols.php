<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$search2 = JRequest::getVar('array'.$field->id, null);
$search = array();

(is_array($search2) == false) ?
	$search[] = $search2 :
	$search = $search2 ;
?>

	<div class="k2filter-field-multi">
		<h3>
			<?php echo $field->name; ?>
		</h3>
		<div>
			<?php
				foreach ($field->content as $which=>$value) {
					echo '<input name="array'.$field->id.'[]" type="checkbox" value="'.$value.'" id="'.$value.'"';
					foreach ($search as $searchword) {
						if ($searchword == $value) echo 'checked="checked"';
					}
					echo ' /><label style="display: inline-block; width: 20px;" for="'.$value.'">'.$value.'</label>';
					if(($which + 1) % 2 == 0) {
						echo "<br />";
					}
				}
			?>
		</div>
	</div>

