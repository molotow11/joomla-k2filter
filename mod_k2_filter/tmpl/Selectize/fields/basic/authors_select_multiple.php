<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$checked = JRequest::getVar('fauthor');
?>
	<div class="k2filter-field-author-multi k2filter-field-<?php echo $k; ?>">
		<?php if($showtitles) : ?>
		<h3><?php echo JText::_('MOD_K2_FILTER_SELECT_AUTHOR_HEADER'); ?></h3>
		<?php endif; ?>
		
		<select name="fauthor[]" multiple="multiple"<?php if($onchange) : ?> onchange="submit_form_<?php echo $module->id; ?>()"<?php endif; ?> placeholder="<?php echo JText::_('MOD_K2_FILTER_SELECT_AUTHOR_HEADER'); ?>">
		<?php
		if($authors) {
			foreach ($authors as $author) {
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $author->id) $selected = ' selected="selected"';
					}
				}
				echo "<option value='".$author->id."'".$selected.">".$author->name."</option>";
			}
		}
		?>			
		</select>		
	</div>

