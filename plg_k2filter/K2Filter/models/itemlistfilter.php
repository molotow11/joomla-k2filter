<?php
/**
 * @version		$Id: itemlist.php 1379 2011-12-02 16:17:56Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');

///change all Itemlist to Itemlistfilter
class K2ModelItemlistfilter extends K2Model {

		function getData($ordering = NULL) {
			
				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
				$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));

				$user = JFactory::getUser();
				$aid = $user->get('aid');
				$db = JFactory::getDBO();
				$params = K2HelperUtilities::getParams('com_k2');
				$limitstart = JRequest::getInt('limitstart');
				$limit = JRequest::getInt('limit');
				$task = JRequest::getCmd('task');
				if($task=='search' && $params->get('googleSearch'))
					return array();

				$jnow = JFactory::getDate();
				$now = $jnow->toSQL();
				$nullDate = $db->getNullDate();

				if (JRequest::getWord('format') == 'feed')
						$limit = $params->get('feedLimit');

				//added for additional categories plugin
				if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')
					|| JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')
					|| JPluginHelper::isEnabled('k2', 'k2multiplecategories')
				) {
					$query = "SELECT DISTINCT i.*, c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams";
				}
				else {
					$query = "SELECT i.*, c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams";
				} 
				
				$query .= ", CASE WHEN i.modified = 0 THEN i.created ELSE i.modified END as lastChanged";
				
				if(JRequest::getVar("orderby") == "price") {
					$query .= ", vm.product_price AS price";
				}
				
				//added for k2multirate plugin
				if (JPluginHelper::isEnabled('system', 'k2multirate')) {
					$query .= ", SUM(r.rating_sum)/SUM(r.rating_count) AS rating";
				}
				else {
					$query .= ", (r.rating_sum/r.rating_count) AS rating";
				}
				
				$query.=" FROM #__k2_items as i LEFT JOIN #__k2_categories AS c ON c.id = i.catid";
				
				//added for additional categories plugin
				if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
					$query .= " LEFT JOIN #__k2_additional_categories AS ca ON ca.itemID = i.id";
				}
				if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
					$query .= " LEFT JOIN #__k2_multiple_categories AS cac ON cac.itemID = i.id";
				}
				if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
					$query .= " LEFT JOIN #__k2_multiple_categories AS cac ON cac.item_id = i.id";
				}

				//added for k2multirate plugin
				if (JPluginHelper::isEnabled('system', 'k2multirate')) {
					$query .= " LEFT JOIN #__k2_multirating AS r ON r.itemid = i.id";
				}			
				else {		
					$query .= " LEFT JOIN #__k2_rating AS r ON r.itemID = i.id";
				}
				
				if(JRequest::getVar("orderby") == "price") {
					$query .= " LEFT JOIN #__k2mart as mart ON mart.baseID = i.id";
					
					$query .= " LEFT JOIN #__virtuemart_product_prices as vm ON vm.virtuemart_product_id = mart.referenceID";
				}
				
				if($task=='user' && !$user->guest && $user->id==JRequest::getInt('id')){
					$query .= " WHERE ";
				}
				else {
					 $query .= " WHERE i.published = 1 AND ";
				}

				$query .= "i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
				." AND i.trash = 0"
				." AND c.published = 1"
				." AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
				." AND c.trash = 0";
										
				$mainframe = &JFactory::getApplication();
				$languageFilter = $mainframe->getLanguageFilter();
				if($languageFilter) {
					$languageTag = JFactory::getLanguage()->getTag();
					//added
					$query .= " AND i.language IN (".$db->quote($languageTag).",".$db->quote('*').")" ;
				}
				
				if ($moduleParams->restrict == 1) {
					if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
						$restcat = $moduleParams->restcat;
						$restcat = str_replace(" ", "", $restcat);
						$restcat = explode(",", $restcat);
						
						$restsub = $moduleParams->restsub;
						
						if($restsub == 1) {
							$query .= " AND ( ";
							foreach($restcat as $kr => $restcatid) {
								$restsubs = self::getCategoryTree($restcatid);
								foreach($restsubs as $k => $rests) {
									$query .= "i.catid = " . $rests;
									//added for additional categories plugin
									if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
										$query .= " OR ca.catid = " . $rests;
									}
									if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
										$query .= " OR cac.catID = " . $rests;
									}
									if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
										$query .= " OR cac.cat_id = " . $rests;
									}
									if($k+1 < sizeof($restsubs))
										$query .= " OR ";
								}
								if($kr+1 < sizeof($restcat))
									$query .= " OR ";			
							}
							$query .= " )";
						}
						
						else {
							$query .= " AND ( ";
							foreach($restcat as $kr => $restcatid) {
								$query .= "i.catid = " . $restcatid;
								//added for additional categories plugin
								if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
									$query .= " OR ca.itemID = " . $restcatid;
								}
								if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
									$query .= " OR cac.catID = " . $restcatid;
								}
								if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
									$query .= " OR cac.cat_id = " . $restcatid;
								}
								if($kr+1 < sizeof($restcat))
									$query .= " OR ";			
							}
							$query .= " )";
						}
					}	
					
					else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
						$restcata = JRequest::getVar('restcata');
						$restsub = $moduleParams->restsub;
						
						$catids = explode(",", $restcata);
						if($restsub == 1) {
							foreach($restcata as $catid) {
								$restsubs = self::getCategoryTree($catid);
							}
						}
						
						$query .= " AND (i.catid IN(".implode(",", $catids).")";
							//added for additional categories plugin
							if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {							
								$query .= " OR ca.catid IN(".implode(",", $catids).")";
							}
							if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
								$query .= " OR cac.catID IN(".implode(",", $catids).")";
							}
							if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
								$query .= " OR cac.cat_id IN(".implode(",", $catids).")";
							}
						$query .= ")";
					}
				}
				
				if( !($task=='user' && !$user->guest && $user->id==JRequest::getInt('id') )) {
					$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
					$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
				}
				
				$filters_match = (JRequest::getVar("filter_match") == "any") ? "OR" : "AND";
				if($filters_match == 'OR') {
					$query .= " AND (1";
				}
				
				if (JRequest::getVar('category')) {
					$catid = JRequest::getVar('category');
					if(!is_array($catid)) {
						$catid = Array($catid);
					}
					
					$catids = Array();
					foreach($catid as $k=>$cid) {						
						array_push($catids, (int)$cid);
						if($moduleParams->restsub) {
							$restsubs = self::getCategoryTree($catid);
							if($restsubs) {
								$catids = array_merge($catids, $restsubs);
							}
						}
					}
					$catids = array_unique($catids);
					$query .= " {$filters_match} (i.catid IN (".implode(",", $catids).")";
					//added for additional categories plugin
					if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
						$query .= " OR ca.catid IN (".implode(",", $catids).")";
					}
					if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
						$query .= " OR cac.catID IN (".implode(",", $catids).")";
					}
					if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
						$query .= " OR cac.cat_id IN (".implode(",", $catids).")";
					}
					$query .= ")";
				}

				$slider_count = 0;
										
				foreach($_REQUEST as $param=>$value) {
					preg_match('/^searchword([0-9]+)$/', $param, $matches);
					$i = $matches[1];
				
						$badchars = array('#', '>', '<', '\\');
								
								
						$search = JRequest::getVar('searchword'.$i, '');
						if ($search != '') {
							if(!is_array($search)) {
								$search = Array($search);
							}

							require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
							$myfield = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
							
							foreach($search as $k=>$word) {
								if(JRequest::getVar("condition_search".$i) == "AND") {
									$query .= " AND (";
								}
								else {
									if($k == 0) {
										$query .= " {$filters_match} (";
									}
									else {
										$query .= " OR ";
									}
								}
								
								$query .= self::prepareFilterArray($word, $badchars, 0, 0, 0, $myfield, $i, 0);
								
								if(($k+1) == count($search)
									|| JRequest::getVar("condition_search".$i) == "AND"
								) {
									$query .= ")";
								}
							}
						}
						
						//text extrafield from
						preg_match('/^searchword([0-9]+)-from$/', $param, $matches);
						$i = $matches[1];
						$search_from = JRequest::getVar('searchword'.$i.'-from', null);
						
						if(!empty($search_from)) {
						
							$mydb = JFactory::getDBO();
							$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
							
							if ($moduleParams->restrict == 1) {
								if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
									$restcat = $moduleParams->restcat;
									$restcat = str_replace(" ", "", $restcat);
									$restcat = explode(",", $restcat);
									
									$restsub = $moduleParams->restsub;
									
									if($restsub == 1) {
										$myquery .= " AND ( ";
										foreach($restcat as $kr => $restcatid) {
											$restsubs = self::getCategoryTree($restcatid);
											foreach($restsubs as $k => $rests) {
												$myquery .= "catid = " . $rests;
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
											$myquery .= "catid = " . $restcatid;
											if($kr+1 < sizeof($restcat))
												$myquery .= " OR ";			
										}
										$myquery .= " )";
									}
								}	
								
								else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
									$restcata = JRequest::getVar('restcata');
									$restsub = $moduleParams->restsub;
									
									$catids = explode(",", $restcata);
									if($restsub == 1) {
										foreach($restcata as $catid) {
											$restsubs = self::getCategoryTree($catid);
										}
									}
									
									$myquery .= " AND (catid IN(".implode(",", $catids).")";
									$myquery .= ")";
								}
							}
							
							$mydb->setQuery($myquery);
							$items = $mydb->LoadObjectList();
							
							foreach($items as $item) {
								$extra_fields = json_decode($item->extra_fields);
								$value = '';
								foreach($extra_fields as $extra_field) {
									if($extra_field->id == $i) {
										$value = $extra_field->value;
									}
								}
								
								$value = preg_replace('~[^0-9,.-]~','',$value);
								$value = str_replace(",", ".", $value);
								
								if($value >= $search_from) {
									$myids[] = $item->id;
								}											
							}
							
							if($myids) {
								$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
							}
							else {
								$query .= " {$filters_match} i.id = 0";
							}
							
							unset($myids);
							
						}
						
						//text extrafield to
						preg_match('/^searchword([0-9]+)-to$/', $param, $matches);
						$i = $matches[1];	
						$search_to = JRequest::getVar('searchword'.$i.'-to', null);
						
						if(!empty($search_to)) {
						
							$mydb = JFactory::getDBO();
							$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
							
							if ($moduleParams->restrict == 1) {
								if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
									$restcat = $moduleParams->restcat;
									$restcat = str_replace(" ", "", $restcat);
									$restcat = explode(",", $restcat);
									
									$restsub = $moduleParams->restsub;
									
									if($restsub == 1) {
										$myquery .= " AND ( ";
										foreach($restcat as $kr => $restcatid) {
											$restsubs = self::getCategoryTree($restcatid);
											foreach($restsubs as $k => $rests) {
												$myquery .= "catid = " . $rests;
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
											$myquery .= "catid = " . $restcatid;
											if($kr+1 < sizeof($restcat))
												$myquery .= " OR ";			
										}
										$myquery .= " )";
									}
								}	
								
								else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
									$restcata = JRequest::getVar('restcata');
									$restsub = $moduleParams->restsub;
									
									$catids = explode(",", $restcata);
									if($restsub == 1) {
										foreach($restcata as $catid) {
											$restsubs = self::getCategoryTree($catid);
										}
									}
									
									$myquery .= " AND (catid IN(".implode(",", $catids).")";
									$myquery .= ")";
								}
							}
							
							$mydb->setQuery($myquery);
							$items = $mydb->LoadObjectList();
							
							foreach($items as $item) {
								$extra_fields = json_decode($item->extra_fields);
								$value = '';
								foreach($extra_fields as $extra_field) {
									if($extra_field->id == $i) {
										$value = $extra_field->value;
									}
								}
								
								$value = preg_replace('~[^0-9,.-]~','',$value);
								$value = str_replace(",", ".", $value);

								if($value <= $search_to && $value != '') {
									$myids[] = $item->id;
								}											
							}
							
							if($myids) {
								$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
							}
							else {
								$query .= " {$filters_match} i.id = 0";
							}
							
							unset($myids);
							
						}
														
						
							preg_match('/^array([0-9]+)$/', $param, $matches);
							$i = $matches[1];
														
							$search = JRequest::getVar('array'.$i, null);
														if (!empty($search)) {
																$count = sizeof($search);
																require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
																$myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));                                       
							$query .= self::prepareFilterArray($search, $badchars, 0, 1, $count, $myfields, $i, 0);
														}
						
														preg_match('/^slider([0-9]+)$/', $param, $matches);
								$i = $matches[1];
														
						$slider_search = JRequest::getVar('slider'.$i, null);
														if (!empty($slider_search)) {
																		
																		$mydb = &JFactory::getDBO();
																		$myquery = "SELECT * FROM #__k2_extra_fields WHERE id = $i";
																		$mydb->setQuery($myquery);
																		$myresults = $mydb->LoadObjectList();
																		
								foreach($myresults as $myresult) {
																				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
																				$myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
																		}
								
								$range = 0;                                            
																		$sql = self::prepareFilterArray($slider_search, $badchars, 1, 0, 0, $myfields, $i, $range);
																		if (! empty($sql)) {
																						$query .= $sql;
																		} else {
																						$rows = array();
																						return $rows;
																		}
														}
						
						preg_match('/^slider_range([0-9]+)$/', $param, $matches);
								$i = $matches[1];
						
						$slider_range = JRequest::getVar('slider_range'.$i, null);
														if (!empty($slider_range)) {
																		
																		$mydb = JFactory::getDBO();
																		$myquery = "SELECT * FROM #__k2_extra_fields WHERE id = {$i}";
																		$mydb->setQuery($myquery);
																		$myresults = $mydb->LoadObjectList();
																		
								foreach($myresults as $myresult) {
																				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
																				$myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
																		}
																		
								$range = 1;
																		$sql = self::prepareFilterArray($slider_range, $badchars, 1, 0, 0, $myfields, $i, $range);
																		if (!empty($sql)) {
																						$query .= $sql;
																		}
														}
						
						preg_match('/^link([0-9]+)$/', $param, $matches);
								$i = $matches[1];
						
						$link = JRequest::getVar('link'.$i, null);
														if (!empty($link)) {
																$sql = " {$filters_match} ";
																$sql .= " (i.extra_fields ";
							$sql .= "REGEXP '^.*\"{$i}\",\"value(.[^}]*\"{$link}";
																$sql .= ")\".*$')";
							
							$query .= $sql;
						}
						
						//text extrafield a-z
						preg_match('/^search_az([0-9]+)$/', $param, $matches);
								$i = $matches[1];
						
						$search_az = JRequest::getVar('search_az'.$i, null);
						
														if (!empty($search_az)) {
							$search_az = str_split($search_az);
							$sql  = " {$filters_match} (i.extra_fields REGEXP '^.*\"{$i}\",\"value\":\"(".implode("|", $search_az).").*\".*$')";
							$query .= $sql;
						}

														
											} //foreach
					
						///searchable labels
						$flabel = JRequest::getVar('flabel');
						if($flabel) {
							$flabel = implode(" ", $flabel);
							$query .= self::prepareSearch($flabel);
						}
					
						///tag filter
						$tag = JRequest::getString('ftag');
						
						if(!empty($tag)) {
							jimport('joomla.filesystem.file');
							
							if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php')) {
								$registry = JFactory::getConfig();
								$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

								$sql = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
								$sql .= " WHERE jfc.value = ".$db->Quote($tag);
								$sql .= " AND jfc.reference_table = 'k2_tags'";
								$sql .= " AND jfc.reference_field = 'name' AND jfc.published=1";

								$db->setQuery($sql, 0, 1);
								$result = $db->loadResult();
							}
							
							if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php')) {
								$registry = JFactory::getConfig();
								$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

								$sql = " SELECT reference_id FROM #__falang_content as fc LEFT JOIN #__languages as fl ON fc.language_id = fl.lang_id";
								$sql .= " WHERE fc.value = ".$db->Quote($tag);
								$sql .= " AND fc.reference_table = 'k2_tags'";
								$sql .= " AND fc.reference_field = 'name' AND fc.published=1";

								$db->setQuery($sql, 0, 1);
								$result = $db->loadResult();
							}
							
							if (!isset($result) || $result < 1) {
									$sql = "SELECT id FROM #__k2_tags WHERE name=".$db->Quote($tag);
									$db->setQuery($sql, 0, 1);
									$result = $db->loadResult();
							}
							
							$query.=" {$filters_match} i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE tagID=".(int)$result.")";
						
						}
						
						///multi tag filter
						$taga = JRequest::getVar('taga');
						if(!empty($taga)) {
							if(JRequest::getVar("condition_taga") == "AND") {
								foreach($taga as $tag_id) {
									$query .= " AND {$tag_id} IN (SELECT tagID FROM #__k2_tags_xref WHERE itemID = i.id)";
								}
							}
							else {
								$query .= " {$filters_match} i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE (";
									$query .= "tagID IN (".implode(",", $taga).")";
								$query .= "))";
							}
						}
						
						///title filter
						$title = addslashes(JRequest::getString('ftitle'));
						if(!empty($title)) {
							$title = $this->prepareTextInput($title);
							$titles = explode(" ", $title);	
							foreach($titles as $k => $title) {
								$query .= " {$filters_match} UPPER(i.title) REGEXP UPPER('^.*{$title}.*$')";
							}
						}
						
						///item text
						$ftext = addslashes(JRequest::getString('ftext'));
						if(!empty($ftext)) {
							$ftext = $this->prepareTextInput($ftext);
							$query .= " {$filters_match} (UPPER(i.introtext) REGEXP UPPER('^.*{$ftext}.*$') OR UPPER(i.fulltext) REGEXP UPPER('^.*{$ftext}.*$'))";
						}								
						
						///title A-Z filter
						$title_az = JRequest::getString('ftitle_az');
						if(!empty($title_az)) {
							if($title_az == "num") {
								$query .= " {$filters_match} i.title REGEXP '^[0-9]+.*$'";
							}
							else {
								$query .= " {$filters_match} i.title REGEXP '^{$title_az}.*$'";
							}
						}
						
						///item all in one
						$phrase = addslashes(JRequest::getString('fitem_all'));
						if(!empty($phrase)) {
							$phrase = $this->prepareTextInput($phrase);
							$query .= " {$filters_match} ("; // AND || OR
								$query .= $this->getKeywordQuery($phrase);
							$query .= ")";
						
						}
						
						///item id
						$fitem_id = JRequest::getInt('fitem_id');
						
						if(!empty($fitem_id)) {
						
							$query .= " {$filters_match} i.id = {$fitem_id}";
						
						}
						
						/// author
						$author = JRequest::getVar("fauthor");
						if($author) {
							if(!is_array($author)) {
								$author = Array($author);
							}
							
							foreach($author as $k=>$aid) {
								if($k == 0) {											
									$query .= " {$filters_match} (i.created_by = {$aid}";
								}
								else {
									$query .= " OR i.created_by = {$aid}";
								}
								if(($k+1) == count($author)) {
									$query .= ")";
								}
							}
						}
						
						/// creation date
						$created = JRequest::getString('created');
						
						if(!empty($created)) {
							$query .= " {$filters_match} i.created REGEXP '^{$created}.*$'";
						}	

						$thour = date("H:i:s");
						
						/// creation date range
						$created_from = JRequest::getString('created-from');
						$created_to = JRequest::getString('created-to');
						
						if(!empty($created_from) || !empty($created_to)) {
							if(!empty($created_from) && !empty($created_to)) {
								$query .= " {$filters_match} (i.created >= '{$created_from} 00:00:00' AND i.created <= '{$created_to} 23:59:59')";
							}
							if(!empty($created_from) && empty($created_to)) {
								$query .= " {$filters_match} i.created >= '{$created_from} 00:00:00'";
							}
							if(empty($created_from) && !empty($created_to)) {
								$query .= " {$filters_match} i.created <= '{$created_to} 23:59:59'";
							}
						}								
						
						/// publish up date
						$publish_up = JRequest::getString('publish_up');
						
						if(!empty($publish_up)) {
							$query .= " {$filters_match} i.publish_up REGEXP '^{$publish_up}.*$'";
						}
						
						/// publish up date range
						$publish_up_from = JRequest::getString('publish-up-from');
						$publish_up_to = JRequest::getString('publish-up-to');
						
						if(!empty($publish_up_from) && !empty($publish_up_to)) {
							$query .= " {$filters_match} (i.publish_up >= '{$publish_up_from}' AND i.publish_up <= '{$publish_up_to} {$thour}')";
						}
						
						/// publish down date
						$publish_down = JRequest::getString('publish_down');
						
						if(!empty($publish_down)) {
							$query .= " {$filters_match} i.publish_down REGEXP '^{$publish_down}.*$'";
						}
						
						/// publish down date range
						$publish_down_from = JRequest::getString('publish-down-from');
						$publish_down_to = JRequest::getString('publish-down-to');
						
						if(!empty($publish_down_from) && !empty($publish_down_to)) {
							$query .= " {$filters_match} (i.publish_down >= '{$publish_down_from}' AND i.publish_down <= '{$publish_down_to} {$thour}')";
						}
						
						/// K2Store price range
						$price_from = JRequest::getVar('price-from');
						$price_to = JRequest::getVar('price-to');
						
						if(!empty($price_from) || !empty($price_to)) {
						
							$mydb = &JFactory::getDBO();
							$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
							$mydb->setQuery($myquery);
							$items = $mydb->LoadObjectList();
							
							foreach($items as $item) {
								
								$params = json_decode($item->plugins);
								$price = $params->k2storeitem_price;

								if(!empty($price_from) && !empty($price_to)) {
									if($price >= $price_from && $price <= $price_to) {
										$myids[] = $item->id;
									}
								}
								else if (empty($price_from) && !empty($price_to)) {
									if($price <= $price_to) {
										$myids[] = $item->id;
									}
								}
								else if (!empty($price_from) && empty($price_to)) {
									if($price >= $price_from) {
										$myids[] = $item->id;
									}											
								}
							}
							
							if($myids) {
								$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
							}
							else {
								$query .= " {$filters_match} i.id = 0";
							}
							
						}
						
						/// J2Store price range
						$price_from = JRequest::getVar('price-fromj2');
						$price_to = JRequest::getVar('price-toj2');
						
						if(!empty($price_from) || !empty($price_to)) {
						
							$mydb = &JFactory::getDBO();
							$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
							$mydb->setQuery($myquery);
							$items = $mydb->LoadObjectList();
							
							foreach($items as $item) {
								
								$params = json_decode($item->plugins);
								$price = $params->j2storej2data->price;
								if(!empty($price_from) && !empty($price_to)) {
									if($price >= $price_from && $price <= $price_to) {
										$myids[] = $item->id;
									}
								}
								else if (empty($price_from) && !empty($price_to)) {
									if($price <= $price_to) {
										$myids[] = $item->id;
									}
								}
								else if (!empty($price_from) && empty($price_to)) {
									if($price >= $price_from) {
										$myids[] = $item->id;
									}											
								}
							}
							
							if($myids) {
								$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
							}
							else {
								$query .= " {$filters_match} i.id = 0";
							}
							
						}
						
						//item rating
						$rating_from = JRequest::getInt('rating-from');
						if($rating_from) {
							//added for k2multirate plugin
							if (JPluginHelper::isEnabled('system', 'k2multirate')) {
								$query .= " {$filters_match} (SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												/(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												>= {$rating_from}
											";
							}
							else {
								$query .= " {$filters_match} (r.rating_sum/r.rating_count) >= {$rating_from}";	
							}
						}
						
						$rating_to = JRequest::getInt('rating-to');
						if($rating_to) {
							//added for k2multirate plugin
							if (JPluginHelper::isEnabled('system', 'k2multirate')) {
								$query .= " {$filters_match} ((SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												/(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												<= {$rating_to} 
												OR (SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												/(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
												is null)
											";
							}
							else {
								$query .= " {$filters_match} ((r.rating_sum/r.rating_count) <= {$rating_to} OR r.rating_count is null)";	
							}
						}
						
						if($filters_match == 'OR') {
							$query = str_replace("AND (1 OR ", "AND (", $query);
							$query .= ")";
						}
	
				if(strpos($_SERVER["REQUEST_URI"], "featured=2") !== false) {
					JRequest::setVar("featured", 2); // fix strange issue with get parameter
				}				

				//Featured flag
				if (JRequest::getInt('featured', 1) == '0') {
						$query .= " AND i.featured != 1";
				} else if (JRequest::getInt('featured') == '2') {
						$query .= " AND i.featured = 1";
				}
			
			//added for k2multirate plugin
			if (JPluginHelper::isEnabled('system', 'k2multirate')) {
				$query .= " GROUP BY i.id";
			}
				
			// ADDED k2FSM from here ->>
				$order = JRequest::getVar("orderby");
				if($order == '') {
					$order = $moduleParams->ordering_default; //it is not the same with default parameter in JRequest, because empty parameter not handled
				}				
				$order_method = JRequest::getVar("orderto");
				if($order_method == '') {
					$order_method = $moduleParams->ordering_default_method;
				}
				$order_method = preg_replace("/[^A-Za-z0-9]/", "", $order_method);
			
				//Set ordering			
				switch ($order) {
						//added
						case 'price':
								$orderby = 'price';
								$orderby .= ' '.$order_method;
								break;							
						//added

						case 'date':
								$orderby = 'i.created';
								$orderby .= ' '.$order_method;
								break;						
								
						case 'cat':
								$orderby = 'c.name';
								$orderby .= ' '.$order_method;
								break;	
								
						case 'intro':
								$orderby = 'i.introtext';
								$orderby .= ' '.$order_method;
								break;

						case 'alpha':
								$orderby = 'i.title';
								$orderby .= ' '.$order_method;
								break;

						case 'order':
								if (JRequest::getInt('featured') == '2') {
									$orderby = 'i.featured_ordering';
									$orderby .= ' '.$order_method;
								}
								else {
									$orderby = 'c.ordering, i.ordering';
									$orderby .= ' '.$order_method;
								}
								break;
								
						case 'featured':
								$featured_dir = ($order_method == "asc" ? "desc" : "asc");
								$orderby = 'i.featured ' . $featured_dir . ', i.created ' . $order_method;
								break;

						case 'hits':
								$orderby = 'i.hits';
								$orderby .= ' '.$order_method;
								break;

						case 'rand':
								$currentSession = JFactory::getSession();    
								$sessionNum = substr(preg_replace('/[^0-9]/i','',$currentSession->getId()),2,3); 
								$orderby = 'RAND('.$sessionNum.')'; 
								break; 

						case 'best':
								$orderby = 'rating';
								$orderby .= ' '.$order_method;
								break;
								
						case 'publishUp':
								$orderby = 'i.publish_up';
								$orderby .= ' '.$order_method;
								break;
								
						case 'modified' :
								$orderby = 'lastChanged';
								$orderby .= ' '.$order_method;
								break;

						case 'id':		
						default:
								$orderby = 'i.id';
								$orderby .= ' '.$order_method;
								break;
				}	

			$query .= " ORDER BY ".$orderby;

			if($order != "k2store" && $order != "j2store") {
				$order = (int)$order;
			}
			
			if($order === "k2store" || $order === "j2store" || $order != 0) {
				$db->setQuery($query);
			}
			else {
				$db->setQuery($query, $limitstart, $limit);
			}
			
			if(isset($_GET["debug"])) {
				echo $query . "<br /><hr />";
			}
			
			$rows = $db->loadObjectList();

			if(JRequest::getVar("format") == "dynobox") {
				return $rows;
			}
			
			if(!empty($rows)) {
				
				//Order by extrafield
				$order = JRequest::getVar("orderby");
				if($order == '') {
					$order = $moduleParams->ordering_default; //it is not the same with default parameter in JRequest, because empty parameter not handled
				}				
				$order_method = JRequest::getVar("orderto");
				if($order_method == '') {
					$order_method = $moduleParams->ordering_default_method;
				}
				$order_method = preg_replace("/[^A-Za-z0-9]/", "", $order_method);				
				
				if($order == 'k2store') {
					foreach($rows as $key=>$item) {
						
						$params = json_decode($item->plugins);
						$price = $params->k2storeitem_price;
									
						$extrasort[$key] = Array();
						$extrasort[$key][0] = $item;
						$extrasort[$key][1] = $price;
					}
					
					if($order_method == "asc") {
						usort($extrasort, array('K2ModelItemlistfilter','compareasc'));
					}
					else {
						usort($extrasort, array('K2ModelItemlistfilter','comparedesc'));
					}
																
					$rows = Array();
					$total = $limit + $limitstart;

					for($i=$limitstart; $i<$total; $i++) {
						if(!empty($extrasort[$i][0])) {
							$rows[] = $extrasort[$i][0];
						}
					}
				}
				
				// J2 Store price
				if($order == 'j2store') {
					foreach($rows as $key=>$item) {
						
						$params = json_decode($item->plugins);
						$price = $params->j2storej2data->price;
									
						$extrasort[$key] = Array();
						$extrasort[$key][0] = $item;
						$extrasort[$key][1] = $price;
					}
					
					if($order_method == "asc") {
						usort($extrasort, array('K2ModelItemlistfilter','compareasc'));
					}
					else {
						usort($extrasort, array('K2ModelItemlistfilter','comparedesc'));
					}
																
					$rows = Array();
					$total = $limit + $limitstart;

					for($i=$limitstart; $i<$total; $i++) {
						if(!empty($extrasort[$i][0])) {
							$rows[] = $extrasort[$i][0];
						}
					}
				}

				$order = (int)$order;
				
				if($order != 0) {
					foreach($rows as $key=>$item) {						
						$extras = json_decode($item->extra_fields);
						if($extras) {
							require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
							foreach($extras as $extra) {
								if($extra->id == $order) {
									$extra->type = modK2FilterHelper::pull($extra->id, 'type');
									if($extra->type == "text" || $extra->type == "textfield" || $extra->type == "date") {
										preg_match('!\d+!', $extra->value, $matches);
										if(count($matches)) {
											$extraval = trim(str_replace(",", ".", $extra->value));
											$extraval = (float)$extraval;
										}
										else {
											$extraval = $extra->value;
										}
									}
									else {
										$extravalues = json_decode(modK2FilterHelper::pull($extra->id, 'value'));
										foreach($extravalues as $val) {
											if($val->value == $extra->value) {
												$extraval = $val->name;
											}
										}
									}
								}
							}
						}
						
						$extrasort[$key] = Array();
						$extrasort[$key]['item'] = $item;
						$extrasort[$key]['itemTitle'] = mb_strtolower($item->title);
						$extrasort[$key]['extraVal'] = mb_strtolower($extraval);
						$extraval = null;
					}

					if($order_method == "asc") {
						$extrasort = self::array_orderby($extrasort, 'extraVal', SORT_ASC, 'itemTitle', SORT_ASC);
					}
					else {
						$extrasort = self::array_orderby($extrasort, 'extraVal', SORT_DESC, 'itemTitle', SORT_ASC);
					}
																
					$rows = Array();
					$total = $limit + $limitstart;

					for($i=$limitstart; $i<$total; $i++) {
						if(!empty($extrasort[$i]['item'])) {
							$rows[] = $extrasort[$i]['item'];
						}
					}
				}

			}
			
			//added for item navigation
			if(JRequest::getInt("getSiblingsForNavigation")) {
				$siblings = new stdClass;
				foreach($rows as $k=>$item) {
					if($item->id == JRequest::getInt("articleId")) {
						$siblings->prevItem = $rows[$k-1];
						$siblings->nextItem = $rows[$k+1];
					}
				}
				return $siblings;
			}
			//added for item navigation

			return $rows;
				
			// <<- ADDED k2FSM till here
		}

		function array_orderby() {
			$args = func_get_args();
			$data = array_shift($args);
			foreach ($args as $n => $field) {
				if (is_string($field)) {
					$tmp = array();
					foreach ($data as $key => $row)
						$tmp[$key] = $row[$field];
					$args[$n] = $tmp;
				}
			}
			$args[] = &$data;
			call_user_func_array('array_multisort', $args);
			return array_pop($args);
		}
		
		function getTotal($sub_cat = 0) {

				$user = &JFactory::getUser();
				$aid = $user->get('aid');
				$db = &JFactory::getDBO();
				$params = &K2HelperUtilities::getParams('com_k2');
				$task = JRequest::getCmd('task');

				if($task=='search' && $params->get('googleSearch'))
					return 0;

				$jnow = &JFactory::getDate();
				$now = $jnow->toSQL();
				$nullDate = $db->getNullDate();

				
				//added for additional categories plugin
				if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')
					|| JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')
					|| JPluginHelper::isEnabled('k2', 'k2multiplecategories')
				) {
					$query = "SELECT COUNT(DISTINCT i.id)";
				}
				else {
					$query = "SELECT COUNT(*)";
				}
				
				//$query .= ", (r.rating_sum/r.rating_count) AS rating";
				
				$query .= " FROM #__k2_items as i";

				$query .= " LEFT JOIN #__k2_categories c ON c.id = i.catid";
				
				//added for additional categories plugin
				if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
					$query .= " LEFT JOIN #__k2_additional_categories AS ca ON ca.itemID = i.id";
				}
				if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
					$query .= " LEFT JOIN #__k2_multiple_categories AS cac ON cac.itemID = i.id";
				}
				if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
					$query .= " LEFT JOIN #__k2_multiple_categories AS cac ON cac.item_id = i.id";
				}	
				
				$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";
				
				if ($task == 'tag')
						$query .= " LEFT JOIN #__k2_tags_xref tags_xref ON tags_xref.itemID = i.id LEFT JOIN #__k2_tags tags ON tags.id = tags_xref.tagID";

				if($task=='user' && !$user->guest && $user->id==JRequest::getInt('id')){
					$query .= " WHERE ";
				}
				else {
					 $query .= " WHERE i.published = 1 AND ";
				}
				
				$query .= "i.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
				." AND i.trash = 0"
				." AND c.published = 1"
				." AND c.access IN(".implode(',', $user->getAuthorisedViewLevels()).")"
				." AND c.trash = 0";
					
				$mainframe = &JFactory::getApplication();
				$languageFilter = $mainframe->getLanguageFilter();
				if($languageFilter) {
					$languageTag = JFactory::getLanguage()->getTag();
					//added
					$query .= " AND i.language IN (".$db->quote($languageTag).",".$db->quote('*').")" ;
				}
				
				// ADDED K2FSM from here ->>
				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
				$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
				
				if ($moduleParams->restrict == 1) {
					if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
						$restcat = $moduleParams->restcat;
						$restcat = str_replace(" ", "", $restcat);
						$restcat = explode(",", $restcat);
						
						$restsub = $moduleParams->restsub;
						
						if($restsub == 1) {
							$query .= " AND ( ";
							foreach($restcat as $kr => $restcatid) {
								$restsubs = self::getCategoryTree($restcatid);
								foreach($restsubs as $k => $rests) {
									$query .= "i.catid = " . $rests;
									//added for additional categories plugin
									if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
										$query .= " OR ca.catid = " . $rests;
									}
									if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
										$query .= " OR cac.catID = " . $rests;
									}
									if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
										$query .= " OR cac.cat_id = " . $rests;
									}
									if($k+1 < sizeof($restsubs))
										$query .= " OR ";
								}
								if($kr+1 < sizeof($restcat))
									$query .= " OR ";			
							}
							$query .= " )";
						}
						
						else {
							$query .= " AND ( ";
							foreach($restcat as $kr => $restcatid) {
								$query .= "i.catid = " . $restcatid;
								//added for additional categories plugin
								if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
									$query .= " OR ca.itemID = " . $restcatid;
								}
								if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
									$query .= " OR cac.catID = " . $restcatid;
								}
								if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
									$query .= " OR cac.cat_id = " . $restcatid;
								}
								if($kr+1 < sizeof($restcat))
									$query .= " OR ";			
							}
							$query .= " )";
						}
					}	
					
					else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
						$restcata = JRequest::getVar('restcata');
						$restsub = $moduleParams->restsub;
						
						$catids = explode(",", $restcata);
						if($restsub == 1) {
							foreach($restcata as $catid) {
								$restsubs = self::getCategoryTree($catid);
							}
						}
						
						$query .= " AND (i.catid IN(".implode(",", $catids).")";
							//added for additional categories plugin
							if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {							
								$query .= " OR ca.catid IN(".implode(",", $catids).")";
							}
							if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
								$query .= " OR cac.catID IN(".implode(",", $catids).")";
							}
							if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
								$query .= " OR cac.cat_id IN(".implode(",", $catids).")";
							}
						$query .= ")";
					}
				}
				
				$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
				$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";
				
				$filters_match = (JRequest::getVar("filter_match") == "any") ? "OR" : "AND";
				if($filters_match == 'OR') {
					$query .= " AND (1";
				}
				
				if (JRequest::getVar('category')) {
					$catid = JRequest::getVar('category');
					if(!is_array($catid)) {
						$catid = Array($catid);
					}
					
					$catids = Array();
					foreach($catid as $k=>$cid) {						
						array_push($catids, (int)$cid);
						if($moduleParams->restsub) {
							$restsubs = self::getCategoryTree($catid);
							if($restsubs) {
								$catids = array_merge($catids, $restsubs);
							}
						}
					}
					$catids = array_unique($catids);
					$query .= " {$filters_match} (i.catid IN (".implode(",", $catids).")";
					//added for additional categories plugin
					if (JPluginHelper::isEnabled('k2', 'k2additonalcategories')) {
						$query .= " OR ca.catid IN (".implode(",", $catids).")";
					}
					if (JPluginHelper::isEnabled('k2', 'incptvk2multiplecategories')) {
						$query .= " OR cac.catID IN (".implode(",", $catids).")";
					}
					if (JPluginHelper::isEnabled('k2', 'k2multiplecategories')) {
						$query .= " OR cac.cat_id IN (".implode(",", $catids).")";
					}
					$query .= ")";
				}

				//Build query depending on task
				switch ($task) {
								
						// ADDED k2FSM from here ->>
						case 'filter':
                        
                        $slider_count = 0;

						foreach($_REQUEST as $param=>$value) {
							preg_match('/^searchword([0-9]+)$/', $param, $matches);
							$i = $matches[1];
						
								$badchars = array('#', '>', '<', '\\');
										
										
								$search = JRequest::getVar('searchword'.$i, '');
								if ($search != '') {
									if(!is_array($search)) {
										$search = Array($search);
									}

									require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
									$myfield = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
									
									foreach($search as $k=>$word) {
										if(JRequest::getVar("condition_search".$i) == "AND") {
											$query .= " AND (";
										}
										else {
											if($k == 0) {
												$query .= " {$filters_match} (";
											}
											else {
												$query .= " OR ";
											}
										}
										
										$query .= self::prepareFilterArray($word, $badchars, 0, 0, 0, $myfield, $i, 0);
										
										if(($k+1) == count($search)
											|| JRequest::getVar("condition_search".$i) == "AND"
										) {
											$query .= ")";
										}
									}
								}
								
								//text extrafield from
								preg_match('/^searchword([0-9]+)-from$/', $param, $matches);
								$i = $matches[1];
								$search_from = JRequest::getVar('searchword'.$i.'-from', null);
								
								if(!empty($search_from)) {
								
									$mydb = JFactory::getDBO();
									$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
									
									if ($moduleParams->restrict == 1) {
										if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
											$restcat = $moduleParams->restcat;
											$restcat = str_replace(" ", "", $restcat);
											$restcat = explode(",", $restcat);
											
											$restsub = $moduleParams->restsub;
											
											if($restsub == 1) {
												$myquery .= " AND ( ";
												foreach($restcat as $kr => $restcatid) {
													$restsubs = self::getCategoryTree($restcatid);
													foreach($restsubs as $k => $rests) {
														$myquery .= "catid = " . $rests;
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
													$myquery .= "catid = " . $restcatid;
													if($kr+1 < sizeof($restcat))
														$myquery .= " OR ";			
												}
												$myquery .= " )";
											}
										}	
										
										else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
											$restcata = JRequest::getVar('restcata');
											$restsub = $moduleParams->restsub;
											
											$catids = explode(",", $restcata);
											if($restsub == 1) {
												foreach($restcata as $catid) {
													$restsubs = self::getCategoryTree($catid);
												}
											}
											
											$myquery .= " AND (catid IN(".implode(",", $catids).")";
											$myquery .= ")";
										}
									}
									
									$mydb->setQuery($myquery);
									$items = $mydb->LoadObjectList();
									
									foreach($items as $item) {
										$extra_fields = json_decode($item->extra_fields);
										$value = '';
										foreach($extra_fields as $extra_field) {
											if($extra_field->id == $i) {
												$value = $extra_field->value;
											}
										}
										
										$value = preg_replace('~[^0-9,.-]~','',$value);
										$value = str_replace(",", ".", $value);

										if($value >= $search_from) {
											$myids[] = $item->id;
										}											
									}
									
									if($myids) {
										$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
									}
									else {
										$query .= " {$filters_match} i.id = 0";
									}
									
									unset($myids);
									
								}
								
								//text extrafield to
								preg_match('/^searchword([0-9]+)-to$/', $param, $matches);
								$i = $matches[1];	
								$search_to = JRequest::getVar('searchword'.$i.'-to', null);
								
								if(!empty($search_to)) {
								
									$mydb = JFactory::getDBO();
									$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
									
									if ($moduleParams->restrict == 1) {
										if ($moduleParams->restmode == 0 && $moduleParams->restcat != '') {
											$restcat = $moduleParams->restcat;
											$restcat = str_replace(" ", "", $restcat);
											$restcat = explode(",", $restcat);
											
											$restsub = $moduleParams->restsub;
											
											if($restsub == 1) {
												$myquery .= " AND ( ";
												foreach($restcat as $kr => $restcatid) {
													$restsubs = self::getCategoryTree($restcatid);
													foreach($restsubs as $k => $rests) {
														$myquery .= "catid = " . $rests;
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
													$myquery .= "catid = " . $restcatid;
													if($kr+1 < sizeof($restcat))
														$myquery .= " OR ";			
												}
												$myquery .= " )";
											}
										}	
										
										else if ($moduleParams->restmode == 1 && JRequest::getVar("restcata") != "") {
											$restcata = JRequest::getVar('restcata');
											$restsub = $moduleParams->restsub;
											
											$catids = explode(",", $restcata);
											if($restsub == 1) {
												foreach($restcata as $catid) {
													$restsubs = self::getCategoryTree($catid);
												}
											}
											
											$myquery .= " AND (catid IN(".implode(",", $catids).")";
											$myquery .= ")";
										}
									}
									
									$mydb->setQuery($myquery);
									$items = $mydb->LoadObjectList();
									
									foreach($items as $item) {
										$extra_fields = json_decode($item->extra_fields);
										$value = '';
										foreach($extra_fields as $extra_field) {
											if($extra_field->id == $i) {
												$value = $extra_field->value;
											}
										}
										
										$value = preg_replace('~[^0-9,.-]~','',$value);
										$value = str_replace(",", ".", $value);

										if($value <= $search_to && $value != '') {
											$myids[] = $item->id;
										}											
									}
									
									if($myids) {
										$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
									}
									else {
										$query .= " {$filters_match} i.id = 0";
									}
									
									unset($myids);
									
								}
                                
								
							   preg_match('/^array([0-9]+)$/', $param, $matches);
							   $i = $matches[1];
                               
							   $search = JRequest::getVar('array'.$i, null);
                                if (!empty($search)) {
                                    $count = sizeof($search);
                                    require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
                                    $myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));                                       
									$query .= self::prepareFilterArray($search, $badchars, 0, 1, $count, $myfields, $i, 0);
                                }
								
                                preg_match('/^slider([0-9]+)$/', $param, $matches);
    						    $i = $matches[1];
                                
								$slider_search = JRequest::getVar('slider'.$i, null);
                                if (!empty($slider_search)) {
                                        
                                        $mydb = &JFactory::getDBO();
                                        $myquery = "SELECT * FROM #__k2_extra_fields WHERE id = $i";
                                        $mydb->setQuery($myquery);
                                        $myresults = $mydb->LoadObjectList();
                                        
										foreach($myresults as $myresult) {
                                            require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
                                            $myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
                                        }
										
										$range = 0;                                            
                                        $sql = self::prepareFilterArray($slider_search, $badchars, 1, 0, 0, $myfields, $i, $range);
                                        if (! empty($sql)) {
                                                $query .= $sql;
                                        } else {
                                                $rows = array();
                                                return $rows;
                                        }
                                }
								
								preg_match('/^slider_range([0-9]+)$/', $param, $matches);
    						    $i = $matches[1];
								
								$slider_range = JRequest::getVar('slider_range'.$i, null);
                                if (!empty($slider_range)) {
                                        
                                        $mydb = JFactory::getDBO();
                                        $myquery = "SELECT * FROM #__k2_extra_fields WHERE id = {$i}";
                                        $mydb->setQuery($myquery);
                                        $myresults = $mydb->LoadObjectList();
                                        
										foreach($myresults as $myresult) {
                                            require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
                                            $myfields = (modK2FilterHelper::extractExtraFields(modK2FilterHelper::pull($i,'')));
                                        }
                                        
										$range = 1;
                                        $sql = self::prepareFilterArray($slider_range, $badchars, 1, 0, 0, $myfields, $i, $range);
                                        if (!empty($sql)) {
                                                $query .= $sql;
                                        }
                                }
								
								preg_match('/^link([0-9]+)$/', $param, $matches);
    						    $i = $matches[1];
								
								$link = JRequest::getVar('link'.$i, null);
                                if (!empty($link)) {
                                    $sql = " {$filters_match} ";
                                    $sql .= " (i.extra_fields ";
									$sql .= "REGEXP '^.*\"{$i}\",\"value(.[^}]*\"{$link}";
                                    $sql .= ")\".*$')";
									
									$query .= $sql;
								}
								
								//text extrafield a-z
								preg_match('/^search_az([0-9]+)$/', $param, $matches);
    						    $i = $matches[1];
								
								$search_az = JRequest::getVar('search_az'.$i, null);
								
                                if (!empty($search_az)) {
									$search_az = str_split($search_az);
									$sql  = " {$filters_match} (i.extra_fields REGEXP '^.*\"{$i}\",\"value\":\"(".implode("|", $search_az).").*\".*$')";
									$query .= $sql;
								}
                                
                          } //foreach
						  
						  
								///searchable labels
								$flabel = JRequest::getVar('flabel');
								if($flabel) {
									$flabel = implode(" ", $flabel);
									$query .= self::prepareSearch($flabel);
								}
							
								///tag filter
								$tag = JRequest::getString('ftag');
								
								if(!empty($tag)) {
									jimport('joomla.filesystem.file');
									
									if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php')) {
										$registry = JFactory::getConfig();
										$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

										$sql = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
										$sql .= " WHERE jfc.value = ".$db->Quote($tag);
										$sql .= " AND jfc.reference_table = 'k2_tags'";
										$sql .= " AND jfc.reference_field = 'name' AND jfc.published=1";

										$db->setQuery($sql, 0, 1);
										$result = $db->loadResult();
									}
									
									if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php')) {
										$registry = JFactory::getConfig();
										$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

										$sql = " SELECT reference_id FROM #__falang_content as fc LEFT JOIN #__languages as fl ON fc.language_id = fl.lang_id";
										$sql .= " WHERE fc.value = ".$db->Quote($tag);
										$sql .= " AND fc.reference_table = 'k2_tags'";
										$sql .= " AND fc.reference_field = 'name' AND fc.published=1";

										$db->setQuery($sql, 0, 1);
										$result = $db->loadResult();
									}
									
									if (!isset($result) || $result < 1) {
											$sql = "SELECT id FROM #__k2_tags WHERE name=".$db->Quote($tag);
											$db->setQuery($sql, 0, 1);
											$result = $db->loadResult();
									}
									
									$query.=" {$filters_match} i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE tagID=".(int)$result.")";
								
								}
								
								///multi tag filter
								$taga = JRequest::getVar('taga');
								if(!empty($taga)) {							
									if(JRequest::getVar("condition_taga") == "AND") {
										foreach($taga as $tag_id) {
											$query .= " AND {$tag_id} IN (SELECT tagID FROM #__k2_tags_xref WHERE itemID = i.id)";
										}
									}
									else {
										$query .= " {$filters_match} i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE (";
											$query .= "tagID IN (".implode(",", $taga).")";
										$query .= "))";
									}								
								}
								
								///title filter
								$title = addslashes(JRequest::getString('ftitle'));
								if(!empty($title)) {
									$title = $this->prepareTextInput($title);
									$titles = explode(" ", $title);
									foreach($titles as $k => $title) {
										$query .= " {$filters_match} UPPER(i.title) REGEXP UPPER('^.*{$title}.*$')";
									}
								}
								
								///item text
								$ftext = addslashes(JRequest::getString('ftext'));
								if(!empty($ftext)) {
									$ftext = $this->prepareTextInput($ftext);
									$query .= " {$filters_match} (UPPER(i.introtext) REGEXP UPPER('^.*{$ftext}.*$') OR UPPER(i.fulltext) REGEXP UPPER('^.*{$ftext}.*$'))";
								}	
								
								///title A-Z filter
								$title_az = JRequest::getString('ftitle_az');
								if(!empty($title_az)) {
									if($title_az == "num") {
										$query .= " {$filters_match} i.title REGEXP '^[0-9]+.*$'";
									}
									else {
										$query .= " {$filters_match} i.title REGEXP '^{$title_az}.*$'";
									}
								}
								
								///item all in one
								$phrase = addslashes(JRequest::getString('fitem_all'));
								if(!empty($phrase)) {
									$phrase = $this->prepareTextInput($phrase);
									$query .= " {$filters_match} ("; // AND || OR
										$query .= $this->getKeywordQuery($phrase);
									$query .= ")";
								
								}
								
								///item id
								$fitem_id = JRequest::getInt('fitem_id');
								
								if(!empty($fitem_id)) {
								
									$query .= " {$filters_match} i.id = {$fitem_id}";
								
								}
								
								/// author
								$author = JRequest::getVar("fauthor");
								if($author) {
									if(!is_array($author)) {
										$author = Array($author);
									}
									foreach($author as $k=>$aid) {
										if($k == 0) {											
											$query .= " {$filters_match} (i.created_by = {$aid}";
										}
										else {
											$query .= " OR i.created_by = {$aid}";
										}
										if(($k+1) == count($author)) {
											$query .= ")";
										}
									}
								}
								
								/// creation date
								$created = JRequest::getString('created');
								
								if(!empty($created)) {
									$query .= " {$filters_match} i.created REGEXP '^{$created}.*$'";
								}								

								$thour = date("H:i:s");
								
								/// creation date range
								$created_from = JRequest::getString('created-from');
								$created_to = JRequest::getString('created-to');
								
								if(!empty($created_from) || !empty($created_to)) {
									if(!empty($created_from) && !empty($created_to)) {
										$query .= " {$filters_match} (i.created >= '{$created_from} 00:00:00' AND i.created <= '{$created_to} 23:59:59')";
									}
									if(!empty($created_from) && empty($created_to)) {
										$query .= " {$filters_match} i.created >= '{$created_from} 00:00:00'";
									}
									if(empty($created_from) && !empty($created_to)) {
										$query .= " {$filters_match} i.created <= '{$created_to} 23:59:59'";
									}
								}									
								
								/// publish up date
								$publish_up = JRequest::getString('publish_up');
								
								if(!empty($publish_up)) {
									$query .= " {$filters_match} i.publish_up REGEXP '^{$publish_up}.*$'";
								}
								
								/// publish up date range
								$publish_up_from = JRequest::getString('publish-up-from');
								$publish_up_to = JRequest::getString('publish-up-to');
								
								if(!empty($publish_up_from) && !empty($publish_up_to)) {
									$query .= " {$filters_match} (i.publish_up >= '{$publish_up_from}' AND i.publish_up <= '{$publish_up_to} {$thour}')";
								}
								
								/// publish down date
								$publish_down = JRequest::getString('publish_down');
								
								if(!empty($publish_down)) {
									$query .= " {$filters_match} i.publish_down REGEXP '^{$publish_down}.*$'";
								}
								
								/// publish down date range
								$publish_down_from = JRequest::getString('publish-down-from');
								$publish_down_to = JRequest::getString('publish-down-to');
								
								if(!empty($publish_down_from) && !empty($publish_down_to)) {
									$query .= " {$filters_match} (i.publish_down >= '{$publish_down_from}' AND i.publish_down <= '{$publish_down_to} {$thour}')";
								}
								
								/// K2Store price range
								$price_from = JRequest::getVar('price-from');
								$price_to = JRequest::getVar('price-to');
								
								if(!empty($price_from) || !empty($price_to)) {
								
									$mydb = &JFactory::getDBO();
									$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
									$mydb->setQuery($myquery);
									$items = $mydb->LoadObjectList();
									
									foreach($items as $item) {
										
										$params = json_decode($item->plugins);
										$price = $params->k2storeitem_price;

										if(!empty($price_from) && !empty($price_to)) {
											if($price >= $price_from && $price <= $price_to) {
												$myids[] = $item->id;
											}
										}
										else if (empty($price_from) && !empty($price_to)) {
											if($price <= $price_to) {
												$myids[] = $item->id;
											}
										}
										else if (!empty($price_from) && empty($price_to)) {
											if($price >= $price_from) {
												$myids[] = $item->id;
											}											
										}
									}
									
									if($myids) {
										$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
									}
									else {
										$query .= " {$filters_match} i.id = 0";
									}
									
								}

								/// J2Store price range
								$price_from = JRequest::getVar('price-fromj2');
								$price_to = JRequest::getVar('price-toj2');
								
								if(!empty($price_from) || !empty($price_to)) {
								
									$mydb = &JFactory::getDBO();
									$myquery = "SELECT * FROM #__k2_items WHERE published = 1 AND trash = 0";
									$mydb->setQuery($myquery);
									$items = $mydb->LoadObjectList();
									
									foreach($items as $item) {
										
										$params = json_decode($item->plugins);
										$price = $params->j2storej2data->price;
										if(!empty($price_from) && !empty($price_to)) {
											if($price >= $price_from && $price <= $price_to) {
												$myids[] = $item->id;
											}
										}
										else if (empty($price_from) && !empty($price_to)) {
											if($price <= $price_to) {
												$myids[] = $item->id;
											}
										}
										else if (!empty($price_from) && empty($price_to)) {
											if($price >= $price_from) {
												$myids[] = $item->id;
											}											
										}
									}
									
									if($myids) {
										$query .= " {$filters_match} i.id IN(".implode(',', $myids).")";	
									}
									else {
										$query .= " {$filters_match} i.id = 0";
									}
									
								}
								
								//item rating
								$rating_from = JRequest::getInt('rating-from');
								if($rating_from) {
									//added for k2multirate plugin
									if (JPluginHelper::isEnabled('system', 'k2multirate')) {
										$query .= " {$filters_match} (SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 /(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 >= {$rating_from}
												  ";
									}
									else {
										$query .= " {$filters_match} (r.rating_sum/r.rating_count) >= {$rating_from}";	
									}
								}
								
								$rating_to = JRequest::getInt('rating-to');
								if($rating_to) {
									//added for k2multirate plugin
									if (JPluginHelper::isEnabled('system', 'k2multirate')) {
										$query .= " {$filters_match} ((SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 /(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 <= {$rating_to} 
													 OR (SELECT SUM(rating_sum) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 /(SELECT COUNT(ratename) FROM #__k2_multirating WHERE itemid = i.id AND ratename != 'updown')
													 is null)
												  ";
									}
									else {
										$query .= " {$filters_match} ((r.rating_sum/r.rating_count) <= {$rating_to} OR r.rating_count is null)";	
									}
								}

								if($filters_match == 'OR') {
									$query = str_replace("AND (1 OR ", "AND (", $query);
									$query .= ")";
								}
								
							break;
						// <<- ADDED K2FSM till here

						default:
								$searchIDs = $params->get('categories');

								if (is_array($searchIDs) && count($searchIDs)) {

										if ($params->get('catCatalogMode')) {
												$sql = @implode(',', $searchIDs);
												$query .= " AND i.catid IN ({$sql})";
										} else {
 											$result = self::getCategoryTree($searchIDs);
												if (count($result)) {
														$sql = @implode(',', $result);
														$query .= " AND i.catid IN ({$sql})";
												}
										}
								}

								break;
				}

				//Featured flag
				if (JRequest::getVar('featured', 1) == '0') {
						$query .= " AND i.featured != 1";
				} else if (JRequest::getVar('featured') == '2') {
						$query .= " AND i.featured = 1";
				}
				
				$db->setQuery($query);
				$result = $db->loadResult();
				return $result;
		}
		
		function prepareSearch($search) {
			jimport('joomla.filesystem.file');
			$db = JFactory::getDBO();
			$language = JFactory::getLanguage();
			$defaultLang = $language->getDefault();
			$currentLang = $language->getTag();
			$length = JString::strlen($search);
			$sql = '';
			
			$filters_match = (JRequest::getVar("filter_match") == "any") ? "OR" : "AND";
			
			if(JRequest::getVar('categories')){
				$categories = @explode(',', JRequest::getVar('categories'));
				JArrayHelper::toInteger($categories);
				$sql.= " {$filters_match} i.catid IN (".@implode(',', $categories).") ";
			}
			
			if(empty($search)) {
				return $sql;
			}

			$type='any';

			if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $currentLang != $defaultLang) {
					$search = explode(' ', JString::strtolower($search));
					foreach ($search as $searchword) {
						if (JString::strlen($searchword) > 3 && !in_array($searchword, $search_ignore)) {
							$word = $db->Quote('%'.$db->getEscaped($searchword, true).'%', false);

							$jfQuery = " SELECT reference_id FROM #__jf_content as jfc LEFT JOIN #__languages as jfl ON jfc.language_id = jfl.".K2_JF_ID;
							$jfQuery .= " WHERE jfc.reference_table = 'k2_items'";
							$jfQuery .= " AND jfl.code=".$db->Quote($currentLang);
							$jfQuery .= " AND jfc.published=1";
							$jfQuery .= " AND jfc.value LIKE ".$word;
							$jfQuery .= " AND (jfc.reference_field = 'title'
									OR jfc.reference_field = 'introtext'
									OR jfc.reference_field = 'fulltext'
									OR jfc.reference_field = 'image_caption'
									OR jfc.reference_field = 'image_credits'
									OR jfc.reference_field = 'video_caption'
									OR jfc.reference_field = 'video_credits'
									OR jfc.reference_field = 'extra_fields_search'
									OR jfc.reference_field = 'metadesc'
									OR jfc.reference_field = 'metakey'
						)";
							$db->setQuery($jfQuery);
							$result = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
							$result = @array_unique($result);
							foreach ($result as $id) {
								$allIDs[] = $id;
							}

							if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomfish'.DS.'joomfish.php') && $currentLang != $defaultLang) {

								if (isset($allIDs) && count($allIDs)) {
									JArrayHelper::toInteger($allIDs);
									$conditions[] = "i.id IN(".implode(',', $allIDs).")";
								}

							}


						}

					}
				if (count($conditions)) {
					$sql .= " {$filters_match} (".implode(" OR ", $conditions).")";
				}
			}
			else {
				$search = explode(' ', JString::strtolower($search));
				$sql .= " {$filters_match} (";
				foreach ($search as $k=>$searchword) {
					$escaped = $db->escape($searchword, true);
					$searchword = $db->Quote('%'.$escaped.'%', false);
					
					$sql .= "(LOWER(i.title) LIKE ".$searchword." OR LOWER(i.introtext) LIKE ".$searchword." OR LOWER(i.`fulltext`) LIKE ".$searchword." OR LOWER(i.extra_fields_search) LIKE ".$searchword." OR LOWER(i.image_caption) LIKE ".$searchword." OR LOWER(i.image_credits) LIKE ".$searchword." OR LOWER(i.video_caption) LIKE ".$searchword." OR LOWER(i.video_credits) LIKE ".$searchword." OR LOWER(i.metadesc) LIKE ".$searchword." OR LOWER(i.metakey) LIKE ".$searchword.")";
					
					if(($k + 1) != count($search)) {
						$sql .= " OR ";
					}
				}
				$sql .= ")";
			}			
			return $sql;
		}
		
		
	// ADDED K2FSM from here ->>
	// modified function prepareSearch($search) to match multiple queries
        
	function prepareFilterArray($searches2, $badchars, $slider, $array, $count, $slarray, $i_slider, $range) {
		jimport('joomla.filesystem.file');
		$db = &JFactory::getDBO();
		$language = JFactory::getLanguage();
		
		//$defaultLang = $language->getDefault();
		$langParams = &JComponentHelper::getParams('com_languages');
		$defaultLang = $langParams->get('site', 'en-GB');
		
		$currentLang = $language->getTag();
		
		$search = $searches2 ;
		
		if(!is_array($search)) {
			$length = JString::strlen($search);

			if (JString::substr($search, 0, 1) == '"' && JString::substr($search, $length - 1, 1) == '"') {
				$type = 'exact';
			}
			else {
				$type = 'any';
			}
		}
		else $type = 'any';

		$filters_match = (JRequest::getVar("filter_match") == "any") ? "OR" : "AND";
		
                                if($slider == 0 and $array == 0) {

                                                $text = $search;
												$n = 0;
												if(count((array)$slarray)) {
													foreach($slarray as $k=>$value) {
														if (trim(mb_strtolower($value)) == trim(mb_strtolower($text))) {
															$sql .= "(i.extra_fields REGEXP '^.*\"$i_slider\",\"value.[^}]*\"{$k}\".*$')";
															$n = 1;
														}
													}
												}
                                                if ($n != 1) {
													$sql = '';
													
													//convert to ascii codes if not in english
													$search_upper = json_encode(mb_strtoupper($search, "utf-8"));
													$search_upper = str_replace("\"", "", $search_upper);
													$search_upper = str_replace("\\", "\\\\", $search_upper);
													$search_upper = str_replace("+", "\\\\+", $search_upper);
													$search_upper = addslashes($search_upper);
													$search_upper = str_replace("(", "\\\\(", $search_upper);
													$search_upper = str_replace(")", "\\\\)", $search_upper);
													
													$search_lower = json_encode(mb_strtolower($search, "utf-8"));
													$search_lower = str_replace("\"", "", $search_lower);
													$search_lower = str_replace("\\", "\\\\", $search_lower);
													$search_lower = str_replace("+", "\\\\+", $search_lower);
													$search_lower = addslashes($search_lower);
													$search_lower = str_replace("(", "\\\\(", $search_lower);
													$search_lower = str_replace(")", "\\\\)", $search_lower);
													
													$search_upper_first = json_encode(mb_strtoupper(mb_substr($search, 0, 1, "utf-8")) . mb_strtolower(mb_substr($search, 1, mb_strlen($search), "utf-8")));
													$search_upper_first = str_replace("\"", "", $search_upper_first);
													$search_upper_first = str_replace("\\", "\\\\", $search_upper_first);
													$search_upper_first = str_replace("+", "\\\\+", $search_upper_first);
													$search_upper_first = addslashes($search_upper_first);
													$search_upper_first = str_replace("(", "\\\\(", $search_upper_first);
													$search_upper_first = str_replace(")", "\\\\)", $search_upper_first);
													
													//search upper first, but without another text in lower
													$search_upper_first_orig = json_encode(mb_strtoupper(mb_substr($search, 0, 1, "utf-8")) . mb_substr($search, 1, mb_strlen($search)));
													$search_upper_first_orig = str_replace("\"", "", $search_upper_first_orig);
													$search_upper_first_orig = str_replace("\\", "\\\\", $search_upper_first_orig);
													$search_upper_first_orig = str_replace("+", "\\\\+", $search_upper_first_orig);
													$search_upper_first_orig = addslashes($search_upper_first_orig);
													$search_upper_first_orig = str_replace("(", "\\\\(", $search_upper_first_orig);
													$search_upper_first_orig = str_replace(")", "\\\\)", $search_upper_first_orig);
													
													//search upper first + upper last character
													$search_upper_first_last = json_encode(mb_strtoupper(mb_substr($search, 0, 1, "utf-8")) . mb_strtolower(mb_substr($search, 1, (mb_strlen($search)-2), "utf-8")) . mb_strtoupper(mb_substr($search, (mb_strlen($search)-1), 1, "utf-8")));
													$search_upper_first_last = str_replace("\"", "", $search_upper_first_last);
													$search_upper_first_last = str_replace("\\", "\\\\", $search_upper_first_last);
													$search_upper_first_last = str_replace("+", "\\\\+", $search_upper_first_last);
													$search_upper_first_last = addslashes($search_upper_first_last);
													$search_upper_first_last = str_replace("(", "\\\\(", $search_upper_first_last);
													$search_upper_first_last = str_replace(")", "\\\\)", $search_upper_first_last);
													
													$search = json_encode($search);
													$search = str_replace("\"", "", $search);
													$search = str_replace("\\", "\\\\", $search);
													$search = str_replace("+", "\\\\+", $search);
													$search = addslashes($search);
													$search = str_replace("(", "\\\\(", $search);
													$search = str_replace(")", "\\\\)", $search);
													$search = str_replace("*", "\\\\*", $search);
													
													$text = addslashes($text);
													$text = str_replace("/", "\\\\\\\/", $text);
													$text = str_replace("(", "\\\\(", $text);
													$text = str_replace(")", "\\\\)", $text);
													$text = str_replace("*", "\\\\*", $text);
													
													$sql  = "(i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$text}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search_upper}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search_lower}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search_upper_first}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search_upper_first_orig}.*\".*$')";
													$sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value\":\"[^}]*{$search_upper_first_last}.*\".*$')";
												}
                                }
                                
								else if ($slider == 0 and $array == 1) {
								  $sql = "";
								  foreach($search as $j=>$word) {
									foreach($slarray as $k=>$value) {
									  if($word == $value) {
										if(JRequest::getVar("condition_array".$i_slider) == "AND") {
										  $sql .= " AND i.extra_fields REGEXP '^.*\"$i_slider\",\"value(.[^}]*\"{$k})\".*$'";
										}
										else {
										  if($j == 0) {
										  $sql .= " {$filters_match} (i.extra_fields REGEXP '^.*\"$i_slider\",\"value(.[^}]*\"{$k}";					
										  }
										  else {
											$sql .= "|.[^}]*\"{$k}";
										  }
										  if(($j+1) == count($search)) {
											$sql .= ")\".*$')";
										  }
										}
									  }		
									}
								  }
								}
                                
                                else if ($slider == 1 && $array == 0 && $range == 0) {
                                        
										$slindex = Array();
										foreach($slarray as $k=>$val) {
											if($val <= $search) {
												$slindex[] = $k;
											}
										}		
										
										if($slindex) {
											foreach($slindex as $k=>$index) {
												if($k == 0) { 
													$sql = " {$filters_match} ((i.extra_fields REGEXP '^.*\"$i_slider\",\"value.[^}]*\"$index\".*$')";
												}
												else $sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value.[^}]*\"$index\".*$')";
											}
										
											$sql .= ")";
										}

                                }
								
                                else if ($slider == 1 && $array == 0 && $range == 1) {

										$texts = explode(" - ", $search);

										$slindex = Array();
										foreach($slarray as $k=>$val) {
											if($val >= $texts[0] && $val <= $texts[1]) {
												$slindex[] = $k;
											}
										}		
										
										if($slindex) {
											foreach($slindex as $k=>$index) {
												if($k == 0) { 
													$sql = " {$filters_match} ((i.extra_fields REGEXP '^.*\"$i_slider\",\"value.[^}]*\"$index\".*$')";
												}
												else $sql .= " OR (i.extra_fields REGEXP '^.*\"$i_slider\",\"value.[^}]*\"$index\".*$')";
											}
										
											$sql .= ")";
										}

                                }
                        

			return $sql;
		}

		// Deprecated function, left for compatibility reasons
		function getCategoryChildren($catid, $clear = false) {

			static $array = array();
			if ($clear)
			$array = array();
			$user = &JFactory::getUser();
			$aid = (int) $user->get('aid');
			$catid = (int) $catid;
			$db = &JFactory::getDBO();
			$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0 AND access<={$aid} ORDER BY ordering ";
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			foreach ($rows as $row) {
				array_push($array, $row->id);
				if (K2ModelItemlist::hasChildren($row->id)) {
					K2ModelItemlist::getCategoryChildren($row->id);
				}
			}
			return $array;
		}

		// Deprecated function, left for compatibility reasons
		function hasChildren($id) {

			$user = &JFactory::getUser();
			$aid = (int) $user->get('aid');
			$id = (int) $id;
			$db = &JFactory::getDBO();
			$query = "SELECT * FROM #__k2_categories WHERE parent={$id} AND published=1 AND trash=0 AND access<={$aid} ";
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			if (count($rows)) {
				return true;
			} else {
				return false;
			}
		}
		
		function countCategoryItems($id) {

				$mainframe = &JFactory::getApplication();
				$user = &JFactory::getUser();
				$aid = (int) $user->get('aid');
				$id = (int) $id;
				$db = &JFactory::getDBO();

				$jnow = &JFactory::getDate();
				$now = $jnow->toSQL();
				$nullDate = $db->getNullDate();

				$categories = K2ModelItemlist::getCategoryTree($id);
				$query = "SELECT COUNT(*) FROM #__k2_items WHERE catid IN (".implode(',', $categories).") AND published=1 AND trash=0";

				$query .= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
				if($mainframe->getLanguageFilter()) {
					$query.= " AND language IN(".$db->Quote(JFactory::getLanguage()->getTag()).", ".$db->Quote('*').")";
				}
				
				$query .= " AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= ".$db->Quote($now)." )";
				$query .= " AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= ".$db->Quote($now)." )";
				$db->setQuery($query);
				$total = $db->loadResult();
				return $total;
		}
		
		function getCategoryFirstChildren($catid, $ordering = NULL) {

				$mainframe = &JFactory::getApplication();
				$user = &JFactory::getUser();
				$aid = $user->get('aid');
				$db = &JFactory::getDBO();
				$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0";
				
				$query.= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") ";
				if($mainframe->getLanguageFilter()) {
					$query.= " AND language IN(".$db->Quote(JFactory::getLanguage()->getTag()).", ".$db->Quote('*').")";
				}

				switch ($ordering) {

						case 'order':
								$order = " ordering ASC";
								break;

						case 'alpha':
								$order = " name ASC";
								break;

						case 'ralpha':
								$order = " name DESC";
								break;

						case 'reversedefault':
								$order = " id DESC";
								break;

						default:
								$order = " id ASC";
								break;

				}

				$query .= " ORDER BY {$order}";

				$db->setQuery($query);
				$rows = $db->loadObjectList();
				if ($db->getErrorNum()) {
						echo $db->stderr();
						return false;
				}

				return $rows;
		}
				
		public static function getCategoryTree($categories){
			$mainframe = &JFactory::getApplication();
			$db = &JFactory::getDBO();
			$user = &JFactory::getUser();
			$aid = (int) $user->get('aid');
			if(!is_array($categories)){
				$categories = (array)$categories;
			}
			JArrayHelper::toInteger($categories);
			$categories = array_unique($categories);
			sort($categories);
			$key = implode('|', $categories);
			$clientID = $mainframe->getClientId();
			static $K2CategoryTreeInstances = array();
			if(isset($K2CategoryTreeInstances[$clientID]) && array_key_exists($key, $K2CategoryTreeInstances[$clientID])){
				return $K2CategoryTreeInstances[$clientID][$key];
			}
			$array = $categories;
			while(count($array)){
				$query = "SELECT id
						FROM #__k2_categories 
						WHERE parent IN (".implode(',', $array).") 
						AND id NOT IN (".implode(',', $array).") ";
				if($mainframe->isSite()){
					$query.="
								AND published=1 
								AND trash=0";

					$query.= " AND access IN(".implode(',', $user->getAuthorisedViewLevels()).")";
					if($mainframe->getLanguageFilter()) {
						$query.= " AND language IN(".$db->Quote(JFactory::getLanguage()->getTag()).", ".$db->Quote('*').")";
					}
				}
				$db->setQuery($query);
				$array = K2_JVERSION == '30' ? $db->loadColumn() : $db->loadResultArray();
				$categories = array_merge($categories, $array);
			}
			JArrayHelper::toInteger($categories);
			$categories = array_unique($categories);
			$K2CategoryTreeInstances[$clientID][$key] = $categories;
			return $categories;
		}		
		
		function utfCharToNumber($char) {
			$i = 0;
			$number = '';
			while (isset($char[$i])) {
				$number.= ord($char[$i]);
				++$i;
				}
			return $number;
		}
		
		function getExtra($exclude = '', $catid = 0) {
			$db = JFactory::getDBO();
			
			$extra_group = '';
			if($catid != 0) {
				JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
				$category = JTable::getInstance('K2Category', 'Table');
				$category->load($catid);
				
				if($category->extraFieldsGroup != 0) {
					$group = $category->extraFieldsGroup;
					$extra_group = " AND `group` IN ({$group})";
				}
			}
			
			if($exclude == '') {
				$exclude = 0;
			}
			
			$query = "SELECT * FROM #__k2_extra_fields WHERE id NOT IN ({$exclude}){$extra_group} ORDER BY ordering";
			$db->setQuery($query);
			$extras = $db->loadObjectList();
			return $extras;
		}

		function compareasc($v1, $v2) {
		   if ($v1[1] == $v2[1]) return 0;
		   return ($v1[1] < $v2[1])?-1:1;
		}		
		
		function comparedesc($v1, $v2) {
		   if ($v1[1] == $v2[1]) return 0;
		   return ($v1[1] > $v2[1])?-1:1;
		}	

		function searchstat() {
			foreach($_GET as $name=>$value) {
				if(!is_array($value)) {
					$value = Array($value);
				}
				foreach($value as $val) {
					$val = preg_replace("/[^\w_ ]+/u", "", $val);
					switch($name) {
						case "category" :
							$search_term = "Category: ".$this->getCategoryName($val);
							$this->storestat($search_term);
						break;
						
						case "ftag" :
						case "taga" :
							if((int)$val != 0) {
								$val = $this->getTagName($val);
							}
							$search_term = "Tag: " . $val;
							$this->storestat($search_term);						
						break;
						
						case "fitem_all" :
							$search_term = "Keyword: ".$val;
							$this->storestat($search_term);						
						break;
						
						case (preg_match('/searchword.*/', $name) ? true : false) :
							preg_match_all('/(\d+)/s', $name, $id);
							$search_term = "Extrafield: ".$this->getExtrafieldName($id[0][0]). " -> " .$val;
							$this->storestat($search_term);	
						break;
					}
				}
			}
		}
		
		function storestat($search_term) {
			$db = JFactory::getDBO();

			$params = JComponentHelper::getParams('com_search');
			$enable_log_searches = $params->get('enabled');

			$search_term = $db->escape(trim($search_term));

			if (@$enable_log_searches)
			{
				$db = JFactory::getDbo();
				$query = 'SELECT hits'
				. ' FROM #__core_log_searches'
				. ' WHERE LOWER(search_term) = "'.$search_term.'"'
				;
				$db->setQuery($query);
				$hits = intval($db->loadResult());
				if ($hits) {
					$query = 'UPDATE #__core_log_searches'
					. ' SET hits = (hits + 1)'
					. ' WHERE LOWER(search_term) = "'.$search_term.'"'
					;
					$db->setQuery($query);
					$db->query();
				} else {
					$query = 'INSERT INTO #__core_log_searches VALUES ("'.$search_term.'", 1)';
					$db->setQuery($query);
					$db->query();
				}
			}
		}
		
		function getCategoryName($id) {
			if(is_array($id)) return;
		
			$db = JFactory::getDBO();
			
			$query = "SELECT name from #__k2_categories WHERE id = {$id}";
			$db->setQuery($query);
			
			return $db->loadResult();
		}
		
		function getTagName($id) {
			$db = JFactory::getDBO();
			$query = "SELECT name from #__k2_tags WHERE id = {$id}";
			$db->setQuery($query);			
			return $db->loadResult();
		}		
		
		function getExtrafieldName($id) {
			$db = JFactory::getDBO();
			
			$query = "SELECT name from #__k2_extra_fields WHERE id = {$id}";
			$db->setQuery($query);
			
			return $db->loadResult();
		}
		
	function getAuthors($params) {
		$mainframe = &JFactory::getApplication();
		$componentParams = &JComponentHelper::getParams('com_k2');
		$where = '';
		
		if($params->restrict) {	
			if($params->restmode == 0 && trim($params->restcat) != "") {
				$catids = $params->restcat;
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
						if($params->restsub){
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
			else {
				$catid = JRequest::getVar("restcata", 1);			
				$where = " catid = {$catid} AND ";			
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
		$query = "SELECT DISTINCT created_by FROM #__k2_items
					WHERE {$where} published=1 
						AND ( publish_up = ".$db->Quote($nullDate)." OR publish_up <= ".$db->Quote($now)." ) 
						AND ( publish_down = ".$db->Quote($nullDate)." OR publish_down >= ".$db->Quote($now)." ) 
						AND trash=0 
						AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") 
						AND created_by_alias='' 
						{$languageCheck}
						AND EXISTS (SELECT * FROM #__k2_categories WHERE id= #__k2_items.catid AND published=1 AND trash=0 AND access IN(".implode(',', $user->getAuthorisedViewLevels()).") {$languageCheck})";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$authors = array();
		if (count($rows)) {
			foreach ($rows as $row) {
				$author = JFactory::getUser($row->created_by);
				if($author->block == 1) continue;			
				$author->link = JRoute::_(K2HelperRoute::getUserRoute($author->id));
				$query = "SELECT id, gender, description, image, url, `group`, plugins FROM #__k2_users WHERE userID=".(int)$author->id;
				$db->setQuery($query);
				$author->profile = $db->loadObject();
				$authors[] = $author;
			}
		}		
		return $authors;
	}
	
	function prepareTextInput($text) {
		$text = str_replace("(", "\\\\(", $text);
		$text = str_replace(")", "\\\\)", $text);
		$text = str_replace("*", "\\\\*", $text);
		$text = str_replace("+", "\\\\+", $text);
		return $text;
	}
	
	function getKeywordQuery($phrase) {
		$terms = array();
		foreach(preg_split("/\s+OR\s+/smix", $phrase) as $num=>$term) { // prepare OR
			if($phrase == $term) break; // no any operator exists
			$terms[] = ($num == 0 && $sql == '') ? $term : array("OR" => $term);
		}
		foreach(preg_split("/\s+&\s+|\s+AND\s+/smix", $phrase) as $num=>$term) { // prepare AND
			if($phrase == $term) break; // no any operator exists
			$terms[] = ($num == 0 && $sql == '') ? $term : array("AND" => $term);
		}
		
		if(count($terms)) {
			foreach($terms as $term) {
				if(is_array($term)) {
					foreach($term as $operator=>$word) {
						$sql .=  ' ' . $operator . ' (' . $this->getTermQuery($word) . ')';
					}
				}
				else {
					$sql .= ' (' . $this->getTermQuery($term) . ')';
				}
			}
		}
		else {
			$sql .= ' (' . $this->getTermQuery($phrase) . ')';
		}
		return $sql;
	}
	
	function getTermQuery($term) {
		$db = JFactory::getDBO();
		require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
		$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
		$authors_list = self::getAuthors($moduleParams);
		$query = '';
		
		//tags
		$sql = "SELECT id FROM #__k2_tags WHERE name=".$db->Quote($term);
		$db->setQuery($sql, 0, 1);
		$result = $db->loadResult();
		$query .= "(i.id IN (SELECT itemID FROM #__k2_tags_xref WHERE tagID=".(int)$result."))";
		
		//title
		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php')) {
			$registry = JFactory::getConfig();
			$lang = K2_JVERSION == '30' ? $registry->get('jflang') : $registry->getValue('config.jflang');

			$sql = " SELECT reference_id FROM #__falang_content as fc LEFT JOIN #__languages as fl ON fc.language_id = fl.lang_id";
			$sql .= " WHERE fc.value REGEXP '^.*{$term}.*$'";
			$sql .= " AND fc.reference_table = 'k2_items'";
			$sql .= " AND fc.reference_field = 'title' AND fc.published=1";

			$db->setQuery($sql);
			$result = $db->loadColumn();
			if(count($result)) {
				$query .= " OR (i.id IN (".implode(",", $result)."))";
			}
		}
		else {
			$query .= " OR (i.title REGEXP '^.*{$term}.*$')";
		}
		
		//alias
		$query .= " OR (i.alias REGEXP '^.*{$term}.*$')";
		
		//text
		$query .= " OR (i.introtext REGEXP '^.*{$term}.*$')";
		$query .= " OR (i.fulltext REGEXP '^.*{$term}.*$')";
		
		//extrafields
		$query .= " OR (i.extra_fields_search REGEXP '^.*({$term}).*$')";
		
		//author
		foreach($authors_list as $author) {
			if ((strpos(mb_strtolower($author->name), mb_strtolower($term)) !== false) || (strpos(mb_strtolower($author->username), mb_strtolower($term)) !== false)) {
					$query .= " OR (i.created_by = {$author->id})";
			}
		}
		return $query;
	}

// <<- ADDED K2FSM till here

}
