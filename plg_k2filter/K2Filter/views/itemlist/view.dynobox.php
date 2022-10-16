<?php
/**
 * @version		$Id: view.raw.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewItemlist extends K2View {

	function display($tpl = null) {

		/// Added K2FSM
		if($joomlaVersion < 1.6) {
			$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'K2Filter';
		}
		else {
			$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'K2Filter'.DS.'K2Filter';
		}
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'itemlist.php');
		$controller = new K2ControllerItemList;							
	
		$controller->addModelPath($pluginPath.DS."models");
		
		$model = $controller->getModel('ItemListFilter');			

		//Set limit for model
		JRequest::setVar('limitstart', 0);
		JRequest::setVar('limit', 99999999);
		
		$items = $model->getData();

		if(count($items)) {
			$filter_fields = JRequest::getVar("field_type");
			$filter_ids = JRequest::getVar("field_id");
			$filter_vals = Array();
			
			$model = $controller->getModel('ItemFilter');	
			foreach($items as $key=>$item) { 
				//get extrafields values
				$extras = $model->getItemExtraFields($item->extra_fields, $item);
				foreach($extras as $extra_field) {
					foreach($filter_ids as $k=>$id) {
						if($extra_field->id == $id) {
							$vals = explode(";;", $extra_field->value);
							
							//added for additional extrafields plugin fix
							if (JPluginHelper::isEnabled('k2', 'incptvk2multipleextrafieldgroups')) {
								$vals = explode(", ", $extra_field->value);
							}
							
							foreach($vals as $val) {
								$val = strip_tags(trim($val));
								$filter_vals[$k][] = $val;
							}
						}
						$filter_vals[$k] = array_values(array_unique($filter_vals[$k]));
						natsort($filter_vals[$k]);
					}
				}
				
				//get categories and tags
				$tags = $model->getItemTags($item->id);
				foreach($filter_fields as $k=>$field) {
					if($field == 'category_select' || $field == 'category_multiple_select') {
						$filter_vals['categories'][] = $item->catid;
						//added for additional categories plugin
						if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
							$db = JFactory::getDBO();
							$query = "SELECT catid FROM #__k2_additional_categories WHERE itemID = {$item->id}";
							$db->setQuery($query);
							$add_cats = $db->loadColumn();
							if(count($add_cats)) {
								foreach($add_cats as $add_cat) {
									$filter_vals['categories'][] = $add_cat;
								}
							}
						}
						$filter_vals['categories'] = array_values(array_unique($filter_vals['categories']));
					}
					if($field == 'tag_select' || $field == 'tag_multi_select') {
						foreach($tags as $tag) {
							$filter_vals['tags'][] = $tag->name;
						}
						$filter_vals['tags'] = array_values(array_unique($filter_vals['tags']));
					}
				}
			}
			
			$fields = Array();
			foreach($filter_fields as $k=>$field) {
				$fields[$k] = new JObject();
				$fields[$k]->id = $filter_ids[$k];
				$fields[$k]->name = $field;
				if($filter_fields[$k] == 'category_select' || $filter_fields[$k] == 'category_multiple_select') {
					$fields[$k]->values = $filter_vals['categories'];
				}
				else if($filter_fields[$k] == 'tag_select' || $filter_fields[$k] == 'tag_multi_select') {
					$fields[$k]->values = $filter_vals['tags'];
				}
				else {
					$fields[$k]->values = $filter_vals[$k];
				}
			}
			
			echo json_encode($fields);
		}
		else {
			echo "0";
		}
		exit;
	}
}
