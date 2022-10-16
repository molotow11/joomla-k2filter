<?php 

/*
// K2 Multiple Extra fields Filter and Search module by Andrey M
// molotow11@gmail.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$language = JFactory::getLanguage();
$currentLang = $language->getTag();
list($shortLang) = explode("-", $currentLang);

$path = JModuleHelper::getLayoutPath('mod_k2_filter', $params->get('getTemplate', $params->get('getTemplate', 'Default') . '') . '/template');
if(strpos($path, "modules/") !== false) {
	$path = explode("modules/", $path);
	$path = explode("/template.php", $path[1]);
	$path = JURI::root(true) . '/modules/' . $path[0] . '/assets/filter.css';
}
if(strpos($path, "templates/") !== false) {
	$path = explode("templates/", $path);
	$path = explode("/template.php", $path[1]);
	$path = JURI::root(true) . '/templates/' . $path[0] . '/assets/filter.css';
}

$document = JFactory::getDocument();
$document->addStylesheet($path);

?>
<script type="text/javascript">
	if (typeof jQuery == 'undefined') {
		document.write('<scr'+'ipt type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></scr'+'ipt>');
		document.write('<scr'+'ipt>jQuery.noConflict();</scr'+'ipt>');
	}
</script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<link type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/<?php echo $params->get('uiTheme', 'ui-lightness'); ?>/jquery-ui.css" rel="stylesheet" />

<script type="text/javascript">
	<?php require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/basic_scripts')); ?>
</script>

<script type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.js"></script>
<link type="text/css" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.filter.js"></script>
<link type="text/css" href="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.multiselect.filter.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo JURI::root(); ?>modules/mod_k2_filter/assets/js/jquery.ui.touch-punch.min.js"></script>

<div id="K2FilterBox<?php echo $module->id; ?>" class="K2FilterBlock default <?php echo $params->get('moduleclass_sfx'); ?><?php if($cols == "0") echo ' k2filter-responsive'; ?>">
	<?php if($params->get('descr') != "") : ?>
	<p><?php echo $params->get('descr'); ?></p>
	<?php endif; ?>
	<form action="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&task=filter&Itemid='.$itemid); ?>" name="K2Filter<?php echo $module->id; ?>" method="get">
  		<?php $app = JFactory::getApplication(); if (!$app->getCfg('sef')): ?>
		<input type="hidden" name="option" value="com_k2" />
		<input type="hidden" name="view" value="itemlist" />
		<input type="hidden" name="task" value="filter" />
		<?php endif; ?>
		
	  <div class="k2filter-table">

<?php for($k = 0; $k < count($field_types); $k++) { 
		$field = $field_types[$k];
?>
		
		<div class="k2filter-cell k2filter-cell<?php echo $k; ?>"<?php if($cols) echo ' style="width: '. (100/$cols - 2) .'%;"'?>>
		
		<?php			
			switch($field->type) {
			
				case 'text' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/text'));
				break;
				
				case 'text_range' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/text_range'));
				break;			
			
				case 'text_date' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/text_date'));
				break;
				
				case 'text_date_range' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/text_date_range'));
				break;
				
				case 'text_az' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/text_az'));
				break;
				
				case 'select' :	
					if(count($connected_fields) && $connected_fields != "" && $connected_fields_type == "mass") {
						foreach($connected_fields as $key=>$connected) {
							if($connected[0] == $field->name) {							
								require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/select_connected_parent'));
								
								echo "</div>";
								if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
									echo '<div class="clear" style="clear: both;"></div>';
								}
															
								for($n = 1; $n < count($connected); $n++) {
									
									$k++;
									
									?>
									<div class="k2filter-cell k2filter-cell<?php echo $k; ?>"<?php if($cols) echo ' style="width: '. (100/$cols - 2) .'%;"'?>>
									<?php

									$connected_name = $connected[$n];
									$last_child = '';
									if(($n+1) == count($connected)) {
										$last_child = ' lastchild';
									}
									require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/select_connected_child'));
									
									echo "</div>";
									if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
										echo '<div class="clear" style="clear: both;"></div>';
									}
								}
								
								unset($connected_fields[$key]);
								continue 3;
							}
							else {
								require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/select'));
							}
						}						
					}
					else {
						require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/select'));
					}
				break;
				
				case 'select_autofill' :
					$values = modK2FilterHelper::getExtraValues($field->id, $params);
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/select_autofill'));
				break;
		
				case 'multi' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/multi'));
				break;
		
				case 'multi_select' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/multi_select'));
				break;
				
				case 'multi_select_autofill' :
					$field->content = modK2FilterHelper::getExtraValues($field->id, $params);
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/multi_select_autofill'));
				break;
		
				case 'slider' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/slider'));
				break;
				
				case 'slider_range' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/slider_range'));
				break;
				
				case 'slider_range_autofill' :
					$field->content = modK2FilterHelper::getExtraValues($field->id, $params);
					if($field->content) {
						foreach($field->content as $val_k=>$value) {
							$value = preg_replace('~[^0-9,.]~','',$value);
							$value = str_replace(",", ".", $value);
							$field->content[$val_k] = floatval($value);
							if(floatval($value) == 0) {
								unset($field->content[$val_k]);
							}
						}
						sort($field->content);
					}
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/slider_range_autofill'));
				break;
		
				case 'radio' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/radio'));
				break;
				
				case 'label' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/label'));
				break;
				
				case 'number' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/extrafields/number'));
				break;

				case 'title' :			
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/title'));
				break;
				
				case 'title_az' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/title_az'));
				break;
				
				case 'title_select' :	
					$restcata = 0;					
					if($restmode == 1) {	
						$view = JRequest::getVar("view");
						$task = JRequest::getVar("task");
						
						if($view == "itemlist" && $task == "category") 
							$restcata = JRequest::getInt("id");
						else if($view == "item") {
							$id = JRequest::getInt("id");
							$restcata = modK2FilterHelper::getParent($id);
						}
						else {
							$restcata = JRequest::getVar("restcata");
						}
					}
					$titles = modK2FilterHelper::getTitles($params, $restcata);
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/title_select'));
				break;
				
				case 'item_text' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/item_text'));
				break;

				case 'item_all' :			
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/item_all'));
				break;
				
				case 'item_rating' :			
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/item_rating'));
				break;
				
				case 'item_id' :			
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/item_id'));
				break;				
				
				case 'tag_text' :
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/tag_text'));
				break;		
				
				case 'tag_select' :
					$restcata = 0;					
					if($restmode == 1) {	
						$view = JRequest::getVar("view");
						$task = JRequest::getVar("task");
						
						if($view == "itemlist" && $task == "category") 
							$restcata = JRequest::getInt("id");
						else if($view == "item") {
							$id = JRequest::getInt("id");
							$restcata = modK2FilterHelper::getParent($id);
						}
						else {
							$restcata = JRequest::getVar("restcata");
						}
					}
					$tags = modK2FilterHelper::getTags($params, $restcata);
					if(count($tags)) {
						require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/tag_select'));
					}
				break;
				
				case 'tag_multi' :
					$restcata = 0;					
					if($restmode == 1) {	
						$view = JRequest::getVar("view");
						$task = JRequest::getVar("task");						
						if($view == "itemlist" && $task == "category") 
							$restcata = JRequest::getInt("id");
						else if($view == "item") {
							$id = JRequest::getInt("id");
							$restcata = modK2FilterHelper::getParent($id);
						}
						else {
							$restcata = JRequest::getVar("restcata");
						}
					}
					$tags = modK2FilterHelper::getTags($params, $restcata);					
					if(count($tags)) {
						require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/tag_multi'));
					}
				break;
				
				case 'tag_multi_select' :
					$restcata = 0;					
					if($restmode == 1) {	
						$view = JRequest::getVar("view");
						$task = JRequest::getVar("task");
						
						if($view == "itemlist" && $task == "category") 
							$restcata = JRequest::getInt("id");
						else if($view == "item") {
							$id = JRequest::getInt("id");
							$restcata = modK2FilterHelper::getParent($id);
						}
						else {
							$restcata = JRequest::getVar("restcata");
						}
					}
					$tags = modK2FilterHelper::getTags($params, $restcata);					
					if(count($tags)) {
						require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/tag_multi_select'));
					}
				break;
				
				case 'category_select' :
					ob_start();
					modK2FilterHelper::treeselectbox($params, 0, 0, 0, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/category_select'));
				break;			
			
				case 'category_multiple' :
					ob_start();
					modK2FilterHelper::treeselectbox_multi($params, 0, 0, 0, $elems, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/category_checkbox'));
				break;
				
				case 'category_multiple_select' :
					ob_start();
					modK2FilterHelper::treeselectbox_multi_select($params, 0, 0, 0, $module->id);
					$category_options = ob_get_contents();
					ob_end_clean();
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/category_select_multiple'));
				break;
				
				case 'authors_select' :		
					$authors = modK2FilterHelper::getAuthors($params);
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/authors_select'));
				break;
				
				case 'authors_select_multiple' :		
					$authors = modK2FilterHelper::getAuthors($params);
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/authors_select_multiple'));
				break;
				
				case 'created' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/created'));
				break;
				
				case 'created_range' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/created_range'));
				break;
				
				case 'publish_up' :			
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/publish_up'));
				break;
				
				case 'publish_up_range' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/publish_up_range'));
				break;
				
				case 'publish_down' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/publish_down'));
				break;
				
				case 'publish_down_range' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/publish_down_range'));
				break;
				
				case 'price_range' :
					$field->content = modK2FilterHelper::getK2StorePriceValues($params);
					if($field->content) {
						foreach($field->content as $val_k=>$value) {
							$value = preg_replace('~[^0-9,.]~','',$value);
							$value = str_replace(",", ".", $value);
							$field->content[$val_k] = floatval($value);
							if(floatval($value) == 0) {
								unset($field->content[$val_k]);
							}
						}
						sort($field->content);
					}				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/price_range'));
				break;

				case 'price_range_j2store' :
					$field->content = modK2FilterHelper::getJ2StorePriceValues($params);
					if($field->content) {
						foreach($field->content as $val_k=>$value) {
							$value = preg_replace('~[^0-9,.]~','',$value);
							$value = str_replace(",", ".", $value);
							$field->content[$val_k] = floatval($value);
							if(floatval($value) == 0) {
								unset($field->content[$val_k]);
							}
						}
						sort($field->content);
					}				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/price_range_j2store'));
				break;
				
				case 'filter_match' :				
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/filter_match'));
				break;
				
				default:
					require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'fields/basic/select'));
			}
		?>
		</div>
		<?php
		if($cols && ($k+1) % $cols == 0 && ($k+1) != count($field_types)) {
			echo '<div class="clear" style="clear: both;"></div>';
		}
	}
?>		<div class="clear" style="clear: both;"></div>
	</div><!--/k2filter-table-->
	
	<?php if($restrict == 1) : ?>
		<?php if($restmode == 1) : ?>			
			<?php 
				$restcata = "";
				$view = JRequest::getVar("view");
				
				if($view == "itemlist") { 
					$restcata = JRequest::getInt("id");
				}
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$restcata = modK2FilterHelper::getParent($id);
				}
				if($restcata == 0) {
					$application = JFactory::getApplication();
					$menus = $application->getMenu();
					$menu = $menus->getActive();
					if($menu) {
						if(count($menu->params->get('categories'))) {
							$restcata = $menu->params->get('categories');
							$restcata = $restcata[0];
						}
					}
				}
			?>
			
			<?php if($restcata != "") : ?>
				<input type="hidden" name="restcata" value="<?php echo $restcata; ?>" />
			<?php endif; ?>
			
			<?php $restauto = JRequest::getInt("restcata"); ?>
			<?php if($restauto != "" && $restcata == "") : ?>
				<input type="hidden" name="restcata" value="<?php echo $restauto; ?>" />
			<?php endif; ?>
			
		<?php endif; ?>
	<?php endif; ?>
	
	<?php
		$orderby_active = JRequest::getVar("orderby");
		if($orderby_active == "asc"
			|| $orderby_active == "desc"
		) {
			$orderby_active = JRequest::getVar("extraorder"); // k2extraorder compatibility fix
		}
	?>
	<input type="hidden" name="orderby" value="<?php echo $orderby_active; ?>" />
	<input type="hidden" name="orderto" value="<?php echo JRequest::getVar("orderto"); ?>" />
	
	<input type="hidden" name="flimit" value="<?php echo JRequest::getVar("flimit"); ?>" />
	
	<input type="hidden" name="template_id" value="<?php echo JRequest::getVar("template_id"); ?>" />
	
	<input type="hidden" name="moduleId" value="<?php echo $module->id; ?>" />

	<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
	
	<?php echo JHtml::_( 'form.token' ); ?>
	
	<div class="buttons">
		<?php if ($button):?>
		<input type="submit" value="<?php echo $button_text; ?>" class="btn btn-primary button submit <?php echo $moduleclass_sfx; ?>" />
		<?php endif; ?>
		<?php if($clear_btn) {
				require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/clear_btn')); 
			} 
		?>
	
	</div>
  </form>
  
  <?php if($acounter) require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/acounter')); ?>

  <?php if($ajax_results) require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/ajax_results')); ?>
  
  <?php if($connected_fields != "" && $connected_fields_type == "mass") require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/connected_mass')); ?>
  
  <?php if($connected_fields != "" && $connected_fields_type == "single") require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/connected_single')); ?>
  
  <?php if($acompleter) require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/acompleter')); ?>
  
  <?php if($dynobox) require(JModuleHelper::getLayoutPath('mod_k2_filter', $getTemplate.DS.'elements/dynobox')); ?>
  
  <div style="clear:both;"></div>
</div><!-- k2-filter-box -->