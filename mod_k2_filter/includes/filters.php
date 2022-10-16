<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// Define the DS constant under Joomla! 3.0
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class JFormFieldFilters extends JFormField {	
	var $_name = 'filters';

	var	$type = 'filters';

	function getInput(){
		return JFormFieldFilters::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
	
	function fetchElement($name, $value, &$node, $control_name) {
	
	        $document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root(true).'/modules/mod_k2_filter/assets/filter.css');
			
			$mitems[] = JHTML::_('select.option',  '', "-- ".JText::_('MOD_K2_FILTER_SELECT_FIELDS')." -- ");
			
			///////////
			$db = JFactory::getDBO();
			
			$query = "SELECT t.*, g.name AS group_name ";
			$query .= "FROM #__k2_extra_fields AS t ";
			$query .= "LEFT JOIN #__k2_extra_fields_groups AS g ON g.id = t.group ";
			$query .= "WHERE t.published = 1 ";
			$query .= "ORDER BY group_name, t.ordering ";
			
			$db->setQuery($query);
			$list = $db->loadObjectList();
			
			$group = @$list[0]->group_name;
			array_splice( $list, 0, 0, $group );
			
			for($i = 1; $i < count($list); $i++) {
				$new_group = $list[$i]->group_name;
				if($new_group != $group) {
					array_splice( $list, $i, 0, $new_group );
					$group = $new_group;
				}
			}

			foreach ($list as $item) {
				if(is_object($item)) {
					$mitems[] = JHTML::_('select.option',  'extrafield:'.$item->id, $item->name." [".$item->id."]");
				}
				else {
					$mitems[] = JHTML::_('select.option',  '', '--------- '.$item.' ---------');
				}
			}
			
			///basic filters
			$mitems[] = JHTML::_('select.option', '', '--------- '.JText::_('MOD_K2_FILTER_SELECT_FIELDS_BASIC').' ---------');
			
			$mitems[] = JHTML::_('select.option', 'title', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_TITLE_TEXT'));
			$mitems[] = JHTML::_('select.option', 'title_az', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_TITLE_AZ'));
			$mitems[] = JHTML::_('select.option', 'title_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_TITLE_SELECT'));
			$mitems[] = JHTML::_('select.option', 'item_text', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_TEXT'));
			$mitems[] = JHTML::_('select.option', 'item_all', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_ALL'));
			$mitems[] = JHTML::_('select.option', 'item_rating', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_RATING'));
			$mitems[] = JHTML::_('select.option', 'item_id', JText::_('MOD_K2_FILTER_FILTER_TYPE_ITEM_ID'));
			$mitems[] = JHTML::_('select.option', 'tag_text', JText::_('MOD_K2_FILTER_FILTER_TYPE_TAG_TEXT'));
			$mitems[] = JHTML::_('select.option', 'tag_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_TAG_SELECT'));
			$mitems[] = JHTML::_('select.option', 'tag_multi', JText::_('MOD_K2_FILTER_FILTER_TYPE_TAG_MULTI'));
			$mitems[] = JHTML::_('select.option', 'tag_multi_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_TAG_MULTI_SELECT'));
			$mitems[] = JHTML::_('select.option', 'category_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_CAT_SELECT'));
			$mitems[] = JHTML::_('select.option', 'category_multiple', JText::_('MOD_K2_FILTER_FILTER_TYPE_CAT_MULTI'));
			$mitems[] = JHTML::_('select.option', 'category_multiple_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_CAT_MULTI_SELECT'));
			$mitems[] = JHTML::_('select.option', 'authors_select', JText::_('MOD_K2_FILTER_FILTER_TYPE_AUTHORS_SELECT'));
			$mitems[] = JHTML::_('select.option', 'authors_select_multiple', JText::_('MOD_K2_FILTER_FILTER_TYPE_AUTHORS_SELECT_MULTI'));
			$mitems[] = JHTML::_('select.option', 'created', JText::_('MOD_K2_FILTER_FILTER_TYPE_CREATE_DATE'));
			$mitems[] = JHTML::_('select.option', 'created_range', JText::_('MOD_K2_FILTER_FILTER_TYPE_CREATE_DATE_RANGE'));
			$mitems[] = JHTML::_('select.option', 'publish_up', JText::_('MOD_K2_FILTER_FILTER_TYPE_PUBLISH_UP_DATE'));
			$mitems[] = JHTML::_('select.option', 'publish_up_range', JText::_('MOD_K2_FILTER_FILTER_TYPE_PUBLISH_UP_DATE_RANGE'));
			$mitems[] = JHTML::_('select.option', 'publish_down', JText::_('MOD_K2_FILTER_FILTER_TYPE_PUBLISH_DOWN_DATE'));
			$mitems[] = JHTML::_('select.option', 'publish_down_range', JText::_('MOD_K2_FILTER_FILTER_TYPE_PUBLISH_DOWN_DATE_RANGE'));
			$mitems[] = JHTML::_('select.option', 'price_range', JText::_('MOD_K2_FILTER_FILTER_TYPE_PRICE_RANGE'));
			$mitems[] = JHTML::_('select.option', 'price_range_j2store', JText::_('MOD_K2_FILTER_FILTER_TYPE_PRICE_RANGE_J2STORE'));
			$mitems[] = JHTML::_('select.option', 'filter_match', JText::_('MOD_K2_FILTER_FILTER_TYPE_FILTER_MATCH'));

			$output = JHTML::_('select.genericlist',  $mitems, '', 'class="FilterSelect inputbox"', 'value', 'text', '0');		
			$output .= "<div class='clear'></div><ul id='sortableFields'></ul>";
			$output .= "<div class='clear'></div>";
			$output .= "<textarea style='display: none;' name='".$name."' id='FiltersListVal'>".$value."</textarea>";
			$output .= "
			
			<script type='text/javascript'>
				
				var FilterPath = '".JURI::root(true)."/modules/mod_k2_filter/assets/';
				var MOD_K2_FILTER_SELECT_FIELD_TYPE = '".JText::_("MOD_K2_FILTER_SELECT_FIELD_TYPE")."';
				
				//extrafield types
				var MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD")."\";
				var MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_RANGE = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_RANGE")."\";
				var MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE")."\";
				var MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE_RANGE = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_DATE_RANGE")."\";
				var MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_AZ = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_TEXT_FIELD_AZ")."\";
				var MOD_K2_FILTER_FILTER_TYPE_DROPDOWN = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_DROPDOWN")."\";
				var MOD_K2_FILTER_FILTER_TYPE_DROPDOWN_AUTOFILL = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_DROPDOWN_AUTOFILL")."\";
				var MOD_K2_FILTER_FILTER_TYPE_MULTI_CHECKBOX = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_MULTI_CHECKBOX")."\";
				var MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT")."\";
				var MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT_AUTOFILL = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_MULTI_SELECT_AUTOFILL")."\";
				var MOD_K2_FILTER_FILTER_TYPE_SLIDER = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_SLIDER")."\";
				var MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE")."\";
				var MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE_AUTOFILL = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_SLIDER_RANGE_AUTOFILL")."\";
				var MOD_K2_FILTER_FILTER_TYPE_RADIO = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_RADIO")."\";
				var MOD_K2_FILTER_FILTER_TYPE_LABEL_MANUAL = \"".JText::_("MOD_K2_FILTER_FILTER_TYPE_LABEL_MANUAL")."\";				
				
				if(typeof jQuery == 'undefined') {
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.src = 'https://code.jquery.com/jquery-1.11.3.min.js';
					document.getElementsByTagName('head')[0].appendChild(script);
				   
					if (script.readyState) { //IE
						script.onreadystatechange = function () {
							if (script.readyState == 'loaded' || script.readyState == 'complete') {
								script.onreadystatechange = null;
								load_ui();
							}
						};
					} else { //Others
						script.onload = function () {
							load_ui();
						};
					}
				}
				else {
					load_ui();
				}
				
				function load_ui() {				
					if(typeof jQuery.ui == 'undefined') {
					   var script = document.createElement('script');
					   script.type = 'text/javascript';
					   script.src = 'https://code.jquery.com/ui/1.11.4/jquery-ui.min.js';
					   document.getElementsByTagName('head')[0].appendChild(script);
										   
					   var style = document.createElement('link');
					   style.rel = 'stylesheet';
					   style.type = 'text/css';
					   style.href = 'https://code.jquery.com/ui/1.11.4/themes/ui-lightness/jquery-ui.css';
					   document.getElementsByTagName('head')[0].appendChild(style);
					   
						if (script.readyState) { //IE
							script.onreadystatechange = function () {
								if (script.readyState == 'loaded' || script.readyState == 'complete') {
									script.onreadystatechange = null;
									load_base();
								}
							};
						} else { //Others
							script.onload = function () {
								load_base();
							};
						}		   
					}
					else {
						load_base();
					}
				}
				
				function load_base() {			
					var base_script = document.createElement('script');
					base_script.type = 'text/javascript';
					base_script.src = FilterPath+'js/filter.admin.js';
					document.getElementsByTagName('head')[0].appendChild(base_script);					
				}
			</script>
			
			";

			return $output;
	}
}

?>