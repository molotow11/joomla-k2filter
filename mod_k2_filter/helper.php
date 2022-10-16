<?php 

/*
// mod for K2 Extra fields Filter and Search module by Piotr Konieczny
// piotr@smartwebstudio.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');


class modK2FilterHelper {

	// pulls out specified information about extra fields from the database
	public static function pull($field_id,$what) {
		$query = 'SELECT t.id, t.name as name, t.value as value, t.type as type FROM #__k2_extra_fields AS t WHERE t.published = 1 AND t.id = "'.$field_id.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if($result) {
			$extra_fields = get_object_vars($result);
		
			switch ($what) {
				case 'name' :
					$output = $extra_fields['name']; break;
				case 'type' :
					$output = $extra_fields['type']; break;
				case 'value' :
					$output = $extra_fields['value']; break;
				default:
					$output = $extra_fields['value']; break;
			}
		}
		else {
			$output = "";
		}
		
		return $output;
	}
	
	public static function pullNotTranslated($field_id,$what) {
		$query = 'SELECT t.name as name, t.value as value, t.type as type FROM #__k2_extra_fields AS t WHERE t.published = 1 AND t.id = "'.$field_id.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if($result) {
			$extra_fields = get_object_vars($result);
		
			switch ($what) {
				case 'name' :
					$output = $extra_fields['name']; break;
				case 'type' :
					$output = $extra_fields['type']; break;
				case 'value' :
					$output = $extra_fields['value']; break;
				default:
					$output = $extra_fields['value']; break;
			}
		}
		else {
			$output = "";
		}
		
		return $output;
	}
	
	// pulls out extra fields of specified item from the database
	public static function pullItem($itemID) {
		$query = 'SELECT t.id, t.extra_fields FROM #__k2_items AS t WHERE t.published = 1 AND t.id = "'.$itemID.'"';
		$db = &JFactory::getDBO();
		$db->setQuery($query);
		$extra_fields = get_object_vars($db->loadObject());
		$output = $extra_fields['extra_fields'];
		return $output;
	}
	
	// extracts info from JSON format
	public static function extractExtraFields($extraFields) {		
		$jsonObjects = json_decode($extraFields);

		if (count($jsonObjects)<1) return NULL;

		// convert objects to array
		foreach ($jsonObjects as $object){
			if (isset($object->name)) {
				$objects[$object->value] = $object->name;
			}
			else if (isset($object->id)) {
				$objects[$object->id] = $object->value;
			}
			else return;
		}
		return $objects;
	}
	
	// from thaderweb.com
	public static function getExtraField($id){
		$db	=	JFactory::getDBO();
		$query	=	"SELECT id, name, value FROM #__k2_extra_fields WHERE id = $id";
		$db->setQuery($query);
		$rows	=	$db->loadObject();

		return $rows;
	}
	
	public static function getTitles(&$params, $restcata = 0) {
		
		$mainframe = &JFactory::getApplication();
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();

		$query = "SELECT i.id, i.title, i.alias, c.id as catid, c.alias as calias FROM #__k2_items as i";
		$query .= " LEFT JOIN #__k2_categories c ON c.id = i.catid";
		$query .= " WHERE i.published=1 ";
		$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) ";
		$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
		$query .= " AND i.trash=0 ";

		$query .= " AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		$query .= " AND c.published=1 ";
		$query .= " AND c.trash=0 ";

		$query .= " AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$tagCategory = $params->get('restcat');
				$tagCategory = str_replace(" ", "", $tagCategory);
				$tagCategory = explode(",", $tagCategory);
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories)==1){
						$query .= " AND i.catid={$categories[0]}";
					}
					else {
						$query .= " AND i.catid IN(".implode(',', $categories).")";
					}
				}
			}
			
			else if($params->get('restmode') == 1) {
			
				$tagCategory = $restcata;
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					if(count($categories)==1){
						$query .= " AND i.catid={$categories[0]}";
					}
					else {
						$query .= " AND i.catid IN(".implode(',', $categories).")";
					}
				}
			
			}
		}
		

		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}
		
		$query .= " ORDER BY i.title ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
	
	public static function getTags(&$params, $restcata = 0) {
		
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();

		$query = "SELECT i.id FROM #__k2_items as i";
		$query .= " LEFT JOIN #__k2_categories c ON c.id = i.catid";
		//added for additional categories plugin
		if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
			$query .= " LEFT JOIN #__k2_additional_categories AS ca ON ca.itemID = i.id";
		}
		$query .= " WHERE i.published=1 ";
		$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) ";
		$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
		$query .= " AND i.trash=0 ";

		$query .= " AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		$query .= " AND c.published=1 ";
		$query .= " AND c.trash=0 ";

		$query .= " AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";

		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$tagCategory = $params->get('restcat');
				$tagCategory = str_replace(" ", "", $tagCategory);
				$tagCategory = explode(",", $tagCategory);
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					$query .= " AND (i.catid IN(".implode(',', $categories).")";
					//added for additional categories plugin
					if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
						$query .= " OR ca.catid IN(".implode(',', $categories).")";
					}
					$query .= ")";
				}
			}
			
			else if($params->get('restmode') == 1) {
			
				$tagCategory = $restcata;
				if(is_array($tagCategory)) {
					$tagCategory = array_filter($tagCategory);
				}
				if ($tagCategory) {
					if(!is_array($tagCategory)){
						$tagCategory = (array)$tagCategory;
					}
					foreach($tagCategory as $tagCategoryID){
						$categories[] = $tagCategoryID;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($tagCategoryID);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					$query .= " AND (i.catid IN(".implode(',', $categories).")";
					//added for additional categories plugin
					if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
						$query .= " OR ca.catid IN(".implode(',', $categories).")";
					}
					$query .= ")";
				}
			
			}
		}
		

		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$db->setQuery($query);
		$IDs = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
		
		$tags = Array();
		if($IDs) {
			$query = "SELECT DISTINCT tag.name as tag, tag.id
			FROM #__k2_tags as tag
			LEFT JOIN #__k2_tags_xref AS xref ON xref.tagID = tag.id 
			WHERE xref.itemID IN (".implode(',', $IDs).") 
			AND tag.published = 1 ORDER BY tag.name ASC";
			
			$db->setQuery($query);
			$tags = $db->loadObjectList();
		}
		return $tags;
	}
	
	public static function getCategoryChildren($catid) {

		static $array = array();
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$catid = (int) $catid;
		$db = &JFactory::getDBO();
		$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0 ";

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering ";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		foreach ($rows as $row) {
			array_push($array, $row->id);
			if (modK2FilterHelper::hasChildren($row->id)) {
				modK2FilterHelper::getCategoryChildren($row->id);
			}
		}
		return $array;
	}
	
	public static function hasChildren($id) {

		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$id = (int) $id;
		$db = &JFactory::getDBO();
		$query = "SELECT * FROM #__k2_categories  WHERE parent={$id} AND published=1 AND trash=0 ";

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		if (count($rows)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function treeselectbox(&$params, $id = 0, $level = 0, $i, $moduleId) {
		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$application = JFactory::getApplication();
					$menus = $application->getMenu();
					$menu = $menus->getActive();
					if($menu) {
						if(count($menu->params->get('categories'))) {
							$root_id = $menu->params->get('categories');
							$root_id = $root_id[0];
						}
					}
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int) $id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && ($level == 0)) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0 ";
		} else {
			$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
		}		
		
		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&ndash; ';
		}
		
		foreach ($rows as $k => $row) {
			if (($option == 'com_k2') && ($category == $row->id)) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox($params, $row->id, $level + 1, $i, $moduleId);
				}
			} else {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
			}
		}
	}
	
	public static function treeselectbox_multi(&$params, $id = 0, $level = 0, $i, $elems, $moduleId) {

		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$root_id = JRequest::getVar("restcata", 1);
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int)$id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && $level == 0) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0";
		} else {
			// //tag listing view
			// if($task == "tag") {
				// if($id) {
					// $query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
				// }
				// else {
					// $query = "SELECT * FROM #__k2_categories WHERE published = 1 AND trash = 0 ";
				// }
				
				// $tag = JRequest::getVar("tag");
				// $items = modK2FilterHelper::getTagItems($tag);
				// $catids = Array();
				// foreach((array)$items as $item) {
					// $catids[] = $item->catid;
				// }
				
				// if(count($catids)) {
					// $catids = array_unique($catids);
					// $query .= " AND id IN(" . implode(",", $catids) . ")";
				// }
			// }
			// else {
				$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0";
			//}
		}
		
		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY ordering";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&nbsp&ndash;';
		}
		
		foreach ($rows as $k => $row) {
		
			if($elems > 0 && ($k+1) > $elems && @$cat_switch == 0 && $level == 0) {
				echo "<div class='filter_cat_hidden'>";
				$cat_switch = 1;
			}
		
			if ($option == 'com_k2') {
				
				$category_search = JRequest::getVar("category");
				$selected = '';
				
				if(is_array($category_search) == true) {
					foreach($category_search as $cat) {
						if($cat == $row->id)
							$selected = ' checked="checked"';
					}
				}
				else {
					if($category_search == $row->id)
							$selected = ' checked="checked"';
				}
			} else {
				$selected = '';
			}
			
			$onchange = $params->get('onchange', 0);
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo '<label for="'.$row->name.$row->id.'"><input name="category[]" type="checkbox" value="'.$row->id.'"'.$selected.' id="'.$row->name . $row->id . '"';
				if($onchange) {
					echo " onchange='submit_form_".$moduleId."()'";
				}
				echo ' />';
				echo '<span>' . $indent . ' ' . $row->name . '</span></label>';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox_multi($params, $row->id, $level + 1, $i, $elems, $moduleId);
				}
			} else {
				echo '<label for="'.$row->name.$row->id.'"><input name="category[]" type="checkbox" value="'.$row->id.'"'.$selected.' id="'.$row->name . $row->id . '"';
				if($onchange) {
					echo " onchange='submit_form_".$moduleId."()'";
				}
				echo '/>';
				echo '<span>' . $indent . ' ' . $row->name . '</span></label>';
			}
		}
	}
	
	public static function treeselectbox_multi_select(&$params, $id = 0, $level = 0, $i, $moduleId) {

		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		$root_id = Array();
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$root_id = $params->get('restcat');
				$root_id = str_replace(" ", "", $root_id);
			}
			else if($params->get('restmode') == 1) {
				if($view == "itemlist" && $task == "category") 
					$root_id = JRequest::getInt("id");
				else if($view == "item") {
					$id = JRequest::getInt("id");
					$root_id = modK2FilterHelper::getParent($id);
				}
				else {
					$root_id = JRequest::getVar("restcata", 1);
				}
			}
			$root_id = explode(",", $root_id);
		}
		
		$category = JRequest::getInt('category');
		if($category == 0 && $option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		$id = (int) $id;
		$user = JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = JFactory::getDBO();
		
		if (count($root_id) && ($level == 0)) {
			$query = "SELECT * FROM #__k2_categories WHERE id IN(" . implode(",", $root_id) . ") AND published = 1 AND trash = 0 ";
		} else {
			$query = "SELECT * FROM #__k2_categories WHERE parent = {$id} AND published = 1 AND trash = 0 ";
		}

		$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$query .= " AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") ";
		}

		$query .= " ORDER BY name ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$indent = "";
		for ($i = 0; $i < $level; $i++) {
			$indent .= '&ndash; ';
		}
		
		if($option == "com_k2" && $task == "category") {
			$category = JRequest::getInt('id');
		}
		
		foreach ($rows as $k => $row) {
			$selected = '';
			if ($category == $row->id) {
				$selected = ' selected="selected"';
			} else {
				$category_search = JRequest::getVar("category");
				foreach($category_search as $catid) {
					if($catid == $row->id) {
						$selected = ' selected="selected"';
					}
				}
			}
			
			if (modK2FilterHelper::hasChildren($row->id)) {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
				if($params->get('restsub') == 1) {
					modK2FilterHelper::treeselectbox_multi_select($params, $row->id, $level + 1, $i, $moduleId);
				}
			} else {
				echo '<option class="level'.$level.'" value="'.$row->id.'"'.$selected.'>'.$indent.$row->name.'</option>';
			}
		}
	}
	
	public static function getParent($id) {
		$db = &JFactory::getDBO();
		
		$query = "SELECT * FROM #__k2_items WHERE id = {$id}";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result->catid;
	}
	
	public static function getModuleParams($id) {
		$db =& JFactory::getDBO();
		
		$query = "SELECT * FROM #__modules WHERE id = {$id}";
		$db->setQuery($query);
		$result = $db->loadObject();
		
		$moduleParams = json_decode($result->params);
		return $moduleParams;
	}
	
	public static function getAuthors(&$params) {
		$mainframe = &JFactory::getApplication();
		$componentParams = &JComponentHelper::getParams('com_k2');
		$where = '';
		
		if($params->get('restrict')) {
			if($params->get('restmode') == 0 && trim($params->get('restcat')) != "") {
				$catids = $params->get('restcat');
				$catids = str_replace(" ", "", $catids);
				$catids = explode(",", $catids);
				
				if(is_array($catids)) {
					$catids = array_filter($catids);
				}
				
				if ($catids) {
					if(!is_array($catids)){
						$catids = (array)$catids;
					}
					foreach($catids as $catid){
						$categories[] = $catid;
						if($params->get('restsub')){
							$children = modK2FilterHelper::getCategoryChildren($catid);
							$categories = @array_merge($categories, $children);
						}
					}
					$categories = @array_unique($categories);
					JArrayHelper::toInteger($categories);
					
					if(count($categories) == 1){
						$where = " catid={$categories[0]} AND ";
					}
					else {
						$where = " catid IN(".implode(',', $categories).") AND";
					}
				}
			}
			else if($params->get('restmode') == 1) {
				$catid = (array)JRequest::getVar("restcata");	
				if(!$catid) {
					$application = JFactory::getApplication();
					$menus = $application->getMenu();
					$menu = $menus->getActive();
					if($menu) {
						if(count($menu->params->get('categories'))) {
							$catid = $menu->params->get('categories');
						}
					}
				}
				if(!$catid) {
					$catid = Array(1);
				}
				$where = " catid IN(".implode(",", $catid).") AND ";			
			}
		}
				
		$user = &JFactory::getUser();
		$aid = (int) $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();


		$languageCheck = '';
		if($mainframe->getLanguageFilter()) {
			$languageTag = JFactory::getLanguage()->getTag();
			$languageCheck = "AND language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
		}
		$query = "
			SELECT DISTINCT created_by FROM #__k2_items
				WHERE {$where} published=1 
					AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= ".$db->Quote($now)." ) 
					AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= ".$db->Quote($now)." ) 
					AND trash=0 
					AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") 
					AND created_by_alias='' 
					{$languageCheck}
					AND EXISTS (SELECT * FROM #__k2_categories WHERE id= #__k2_items.catid AND published=1 AND trash=0 AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") {$languageCheck})
		";        	

		$db->setQuery($query);
		$user_ids = $db->loadColumn();

		$authors = array();
		if (count($user_ids)) {
			$query = "SELECT id, name FROM #__users WHERE id IN(".implode(",", $user_ids).")";
			$query .= " ORDER BY TRIM(name) ASC";
			$authors = $db->setQuery($query)->loadObjectList();
		}
		
		return $authors;
	}
	
	public static function getChildsCount($extras, $name) {
		$count = 0;
		foreach($extras as $title) {
			if(stripos($title, trim($name)) !== FALSE) {
				$count++;
			}
		}
		return $count;
	}
	
	public static function getExtraValues($id, $moduleParams) {
		$user = JFactory::getUser();
		require_once(JPATH_BASE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter'.DS.'models'.DS.'itemlistfilter.php');
		$mydb = JFactory::getDBO();
		$myquery = "SELECT i.*";
		$myquery .=" FROM #__k2_items as i LEFT JOIN #__k2_categories AS c ON c.id = i.catid";
		$myquery .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
		." AND i.trash = 0"
		." AND c.published = 1"
		." AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
		." AND c.trash = 0";
		
		if ($moduleParams->get("restrict") == 1) {
			if ($moduleParams->get("restmode") == 0 && $moduleParams->get("restcat") != '') {
				$restcat = $moduleParams->get("restcat");
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
						
				$catids = array();
				if($moduleParams->get("restsub") == 1) {
					foreach($restcat as $category_id) {
						$restsubs = K2ModelItemListFilter::getCategoryTree($category_id);
						$catids = array_merge($catids, $restsubs);
					}
				}
				else {
					$catids = $restcat;
				}
				$myquery .= " AND i.catid IN(".implode(',', $catids).")";
			}	
			else if ($moduleParams->get("restmode") == 1) {
				$category_id = JRequest::getInt('restcata', 0);
				$category_id = JRequest::getInt('category', $category_id);			
				
				if(JRequest::getVar("task") == "category") {
					$category_id = JRequest::getInt('id');
				}
				
				if($category_id == 0) {
					$application = JFactory::getApplication();
					$menus = $application->getMenu();
					$menu = $menus->getActive();
					if($menu) {
						if(count($menu->params->get('categories'))) {
							$categories_menu = $menu->params->get('categories');
							$category_id = $categories_menu[0];
						}
					}
				}
				
				$catids = array($category_id);
				if($moduleParams->get("restsub") == 1) {
					$restsubs = K2ModelItemListFilter::getCategoryTree($category_id);
					$catids = array_merge($catids, $restsubs);
				}

				if($categories_menu) {
					$catids = array_merge($catids, $categories_menu);
				}
				
				$myquery .= " AND i.catid IN(".implode(',', $catids).")";
			}
		}

		//Featured flag
		if (JRequest::getInt('featured', 1) == '0') {
				$myquery .= " AND i.featured != 1";
		} else if (JRequest::getInt('featured') == '2') {
				$myquery .= " AND i.featured = 1";
		}

		$mydb->setQuery($myquery);
		$items = $mydb->loadObjectList();
		
		$values = Array();
		$field_type = modK2FilterHelper::pull($id, 'type');

		foreach($items as $item) {
			if($item->extra_fields) {
				$extras = json_decode($item->extra_fields);				
				foreach($extras as $field) {
					if($field->id == $id && trim($field->value) != '') {
						if($field_type == 'multipleSelect' || $field_type == 'select') {
							$extraVals = modK2FilterHelper::getExtraValsByIndexes($id, $field->value);
							$values = array_merge($values, $extraVals);
						}
						else {
							$thousands_check = explode(",", $field->value);
							if(count($thousands_check) == 1) {
								$thousands_check = explode(".", $field->value);
							}	
							if(@strlen($thousands_check[1]) == 3) {
								$field->value = str_replace(".", "", $field->value);
								$field->value = str_replace(",", "", $field->value);
							}
							array_push($values, trim($field->value));
						}
					}
				}
			}
		}

		natsort($values);
		return array_values(array_unique($values));
	}
	
	public static function getExtraValsByIndexes($extra_id, $indexes) {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__k2_extra_fields WHERE id = {$extra_id}";
		$db->setQuery($query);
		$extra = $db->loadObject();
				
		$values = json_decode($extra->value);
		
		$result = Array();
		if(!is_array($indexes)) {
			$indexes = Array($indexes);
		}
		
		foreach($values as $value) {
			if(in_array($value->value, $indexes)) {
				$result[] = $value->name;
			}
		}
		
		return $result;	
	}
	
	public static function getK2StorePriceValues($moduleParams) {

		$mydb = JFactory::getDBO();
		$myquery = "SELECT ks.item_price FROM #__k2_items as i ";
		$myquery .= "LEFT JOIN #__k2store_products as ks ON i.id = ks.product_id ";
		$myquery .= "WHERE published = 1 AND trash = 0 AND ks.product_id != ''";
		
		if ($moduleParams->get("restrict") == 1) {
			if ($moduleParams->get("restmode") == 0 && $moduleParams->get("restcat") != '') {
				$restcat = $moduleParams->get("restcat");
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
						
				$restsub = $moduleParams->get("restsub");
						
				if($restsub == 1) {
					$myquery .= " AND ( ";
					require_once(JPATH_SITE . '/plugins/system/k2filter/K2Filter/models/itemlistfilter.php');
					foreach($restcat as $kr => $restcatid) {
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcatid);
						foreach($restsubs as $k => $rests) {
							$myquery .= "i.catid = " . $rests;
							if($k+1 < sizeof($restsubs))
								$myquery .= " OR ";
						}
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
				else {
					$myquery .= " AND ( ";
					foreach($restcat as $kr => $restcatid) {
						$myquery .= "i.catid = " . $restcatid;
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
			}	
			else if ($moduleParams->get("restmode") == 1) {
				$restcata = 0;					
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
				$restsub = $moduleParams->get("restsub");
				
				if($restcata != 0) {
					if($restsub == 1) {
						$myquery .= " AND ( ";
						require_once(JPATH_SITE . '/plugins/system/k2filter/K2Filter/models/itemlistfilter.php');
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcata);
						foreach($restsubs as $k => $rests) {
							$myquery .= "i.catid = " . $rests;
							if($k+1 < sizeof($restsubs))
								$myquery .= " OR ";
						}
						$myquery .= " )";
					}
					else { 
						$myquery .= " AND i.catid = " . $restcata;
					}
				}
			}
		}

		$mydb->setQuery($myquery);
		$values = version_compare(JVERSION, '3.0', 'ge') ? $mydb->loadColumn() : $mydb->loadResultArray();

		natsort($values);
		return array_values(array_unique($values));
	}

	public static function getJ2StorePriceValues($moduleParams) {

		$mydb = JFactory::getDBO();
		$myquery = "SELECT jp.price FROM #__k2_items as i ";
		$myquery .= "LEFT JOIN #__j2store_products as js ON i.id = js.product_source_id ";
		$myquery .= "LEFT JOIN #__j2store_variants as jp ON jp.product_id = js.j2store_product_id ";
		$myquery .= "WHERE i.published = 1 AND i.trash = 0 AND js.product_source = 'com_k2'";

		if ($moduleParams->get("restrict") == 1) {
			if ($moduleParams->get("restmode") == 0 && $moduleParams->get("restcat") != '') {
				$restcat = $moduleParams->get("restcat");
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
						
				$restsub = $moduleParams->get("restsub");
						
				if($restsub == 1) {
					$myquery .= " AND ( ";
					require_once(JPATH_SITE . '/plugins/system/k2filter/K2Filter/models/itemlistfilter.php');
					foreach($restcat as $kr => $restcatid) {
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcatid);
						foreach($restsubs as $k => $rests) {
							$myquery .= "i.catid = " . $rests;
							if($k+1 < sizeof($restsubs))
								$myquery .= " OR ";
						}
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
				else {
					$myquery .= " AND ( ";
					foreach($restcat as $kr => $restcatid) {
						$myquery .= "i.catid = " . $restcatid;
						if($kr+1 < sizeof($restcat))
							$myquery .= " OR ";			
					}
					$myquery .= " )";
				}
			}	
			else if ($moduleParams->get("restmode") == 1) {
				$restcata = 0;					
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
				$restsub = $moduleParams->get("restsub");
				
				if($restcata != 0) {
					if($restsub == 1) {
						$myquery .= " AND ( ";
						require_once(JPATH_SITE . '/plugins/system/k2filter/K2Filter/models/itemlistfilter.php');
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcata);
						foreach($restsubs as $k => $rests) {
							$myquery .= "i.catid = " . $rests;
							if($k+1 < sizeof($restsubs))
								$myquery .= " OR ";
						}
						$myquery .= " )";
					}
					else { 
						$myquery .= " AND i.catid = " . $restcata;
					}
				}
			}
		}

		$mydb->setQuery($myquery);
		$values = version_compare(JVERSION, '3.0', 'ge') ? $mydb->loadColumn() : $mydb->loadResultArray();

		natsort($values);
		$values = array_values(array_unique($values));
		return $values;
	}
	
	public static function getTagItems($tag) {
		$db = JFactory::getDBO();
		$query = "SELECT DISTINCT i.id, i.catid FROM #__k2_items AS i";
		$query .= " LEFT JOIN #__k2_tags_xref tags_xref ON tags_xref.itemID = i.id LEFT JOIN #__k2_tags tags ON tags.id = tags_xref.tagID";
		$query .= " WHERE i.published = 1 AND i.trash = 0 AND tags.name = '{$tag}'";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}
}
