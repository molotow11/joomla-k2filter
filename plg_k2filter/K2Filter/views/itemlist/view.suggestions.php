<?php
/**
 * @version     $Id: view.json.php 1728 2012-10-09 10:32:46Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.application.component.view');

class K2ViewItemlist extends K2View {
	
    function display($tpl = null) {
		jimport( 'joomla.application.module.helper' );
		require_once (JPATH_SITE.'/modules/mod_k2_filter/helper.php');
		require_once (JPATH_SITE.'/plugins/system/k2filter/K2Filter/models/itemlistfilter.php');
		$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
		$db = JFactory::getDBO();
		
		//get categories for restriction
		$catids = Array();
		if ($moduleParams->restrict == 1) {
			if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
				$restcat = $moduleParams->restcat;
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
				
				$restsub = $moduleParams->restsub;			
				if($restsub == 1) {
					foreach($restcat as $kr => $restcatid) {
						$restsubs = K2ModelItemListFilter::getCategoryTree($restcatid);
						$catids = array_merge($catids, $restsubs);			
					}
				}				
				else {
					$catids = array_merge($catids, $restcat);;
				}
			}				
			else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
				$restcata = JRequest::getVar('restcata');
				$restsub = $moduleParams->restsub;
				
				if($restsub == 1) {
					$restsubs = K2ModelItemListFilter::getCategoryTree($restcata);
					$catids = array_merge($catids, $restsubs);
				}				
				else {
					$catids[] = $restcata;
				}
			}
		}
		
		if (JRequest::getVar('category')) {
			$catids = Array();
			$catid = JRequest::getVar('category');
			if(!is_array($catid)) {
				$catid = Array($catid);
			}			
			foreach($catid as $k=>$cid) {						
				array_push($catids, (int)$cid);
				if($moduleParams->restsub) {
					$restsubs = K2ModelItemListFilter::getCategoryTree($catid);
					if($restsubs) {
						$catids = array_merge($catids, $restsubs);
					}
				}
			}
		}
		$catids = array_unique($catids);
		
		$query = "SELECT i.id, i.catid, i.title, i.introtext";
		$query .= ", GROUP_CONCAT(t.name) as tags";
		$query .= " FROM #__k2_items as i"; 
		//added for additional categories plugin
		if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
			$query .= " LEFT JOIN #__k2_additional_categories AS ca ON ca.itemID = i.id";
		}
		$query .= " LEFT JOIN #__k2_tags_xref AS tx ON tx.itemID = i.id";
		$query .= " LEFT JOIN #__k2_tags AS t ON t.id = tx.tagID AND t.published = 1";
		
		$jnow = JFactory::getDate();
		$now = $jnow->toSQL();
		$nullDate = $db->getNullDate();
		$user = JFactory::getUser();
		$query .= " WHERE i.published = 1"
					. " AND i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
					. " AND i.trash = 0";
		
		$query .= " AND (i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now).")";
		$query .= " AND (i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now).")";
		
		if(count($catids)) {
			$query .= " AND (i.catid IN (".implode(',', $catids).")";
			//added for additional categories plugin
			if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
				$query .= " OR ca.catid IN (".implode(',', $catids).")";
			}
			$query .= ")";
		}
		$suggestion_type = JRequest::getVar("suggestion_type");
		$q = JRequest::getVar($suggestion_type);
		if(!$q) return;
		$query .= " AND (
						   i.title LIKE '%{$q}%' 
						OR t.name LIKE '%{$q}%'
						OR i.introtext LIKE '%{$q}%'
					)";
		$query .= " GROUP BY i.id";
		$items = JFactory::getDBO()->setQuery($query)->loadObjectList();
				
		$suggestions = Array();
		foreach($items as $item) {
			switch($suggestion_type) {
				case "ftitle" :
					$suggestions[] = $item->title . "::" . $item->id; //show titles, add item id for redirect to item
				break;
				case "ftag" :
					if($item->tags) {
						$suggestions = array_merge($suggestions, explode(",", $item->tags)); //show only tags
					}					
				break;
				case "fitem_all" :
				default : 
					$suggestions[] = $item->title . "::" . $item->id; //show titles, add item id for redirect to item
					if($item->tags) {
						$suggestions = array_merge($suggestions, explode(",", $item->tags)); //mix titles and tags
					}
					if($item->introtext != "") {
						$text = strip_tags($item->introtext);
						$words = preg_split("/[\s,]+/", $text);
						foreach($words as $word) {
							if(strlen($word) >= 3) {
								$suggestions[] = $word;
							}
						}
					}
			}
		}
		$suggestions = array_unique($suggestions);
		natsort($suggestions);
		$suggestions = array_values($suggestions);
		
		$result = array();
		foreach($suggestions as $k=>$word) {
			$p = "/(^|\s+){$q}.*/i";
			if(!preg_match($p, $word)) continue; //do not add titles or tags without this query
			$result[$k] = new stdClass;
			if(count(explode("::", $word)) > 1) {
				list($title, $item_id) = explode("::", $word);
				$oItem = JFactory::getDBO()->setQuery("SELECT * FROM #__k2_items WHERE id = {$item_id}")->loadObject();
				$result[$k]->id = $item_id;
				$result[$k]->label = $title;
				$result[$k]->value = $title;
				$result[$k]->item_link = JRoute::_("index.php?option=com_k2&view=item&id={$item_id}:{$oItem->alias}");
			}
			else {
				$result[$k]->id = $k+1;
				$result[$k]->label = $word;
				$result[$k]->value = $word;			
			}
			
			if($k == 20) break; //limit for 20 suggestions
		}
				
		// Output
        $json = json_encode($result);

        echo JRequest::getVar("callback") . "(" . $json . ")";
		die();
    }

}
