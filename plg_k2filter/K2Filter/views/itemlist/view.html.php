<?php
/**
 * @version		$Id: view.html.php 1511 2012-03-01 21:41:16Z joomlaworks $
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
		$mainframe = JFactory::getApplication();
		$params = K2HelperUtilities::getParams('com_k2');
		$limitstart = JRequest::getInt('limitstart');
		$view = JRequest::getWord('view');
		$task = JRequest::getWord('task');
		$db = JFactory::getDBO();
		
		/// Added K2FSM
		$version = new JVersion;
		$joomlaVersion = $version->RELEASE;
		
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
		/// Added K2FSM

		// Add link
		if (K2HelperPermissions::canAddItem())
			$addLink = JRoute::_('index.php?option=com_k2&view=item&task=add&tmpl=component');
		$this->assignRef('addLink', $addLink);

		// Get data depending on task
		switch ($task) {
				
			// ADDED K2FSM from here ->>
			case 'filter':
				jimport( 'joomla.application.module.helper' );
				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
				$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
				
				// Get category
				if($moduleParams->restmode == 0 && trim($moduleParams->restcat) != "") {
					$category_id = $moduleParams->restcat;
					if($category_id != 0) {
						$category_id = str_replace(" ", "", $category_id);
						$category_id = explode(",", $category_id);
						$category_id = $category_id[0];
					}
				}
				else {
					$category_id = JRequest::getInt('restcata', 0);
					$category_id = JRequest::getInt('category', $category_id);			
				
					if($category_id == 0) {
						$application = JFactory::getApplication();
						$menus = $application->getMenu();
						$menu = $menus->getActive();
						if($menu) {
							if(count((array)$menu->params->get('categories'))) {
								$category_id = $menu->params->get('categories');
								$category_id = $category_id[0];
							}
						}
					}
				}
				
				if(is_array($category_id)) {
					$category_id = $category_id[0];
				}
		
				if(!$category_id) {
					$category_id = 1;
				}
		
				//get extras for ordering
				if($moduleParams->ordering_extra == 1) {
					$exclude = $moduleParams->ordering_extra_exclude;	
					$extras = $model->getExtra($exclude, $category_id);
				}
				
				$total_items = $model->getTotal();
				
				$filter_template = JRequest::getVar("template_id");
				if($filter_template == '') {
					$filter_template = $moduleParams->results_template; //it is not the same with default parameter in JRequest
				}
				switch ($filter_template) {				
					case 0 :
						//Set layout
						$this->setLayout('filter');

						//Set limit
						$limit = $params->get('genericItemCount');

						$resultf = $moduleParams->resultf;

						if($total_items == 0) {
							$resultf = $moduleParams->noresult; 
						}
												
						$this->assignRef('resultf', $resultf);
						$this->assignRef('result_count', $total_items);
						$this->assignRef('extras', $extras);

						$addHeadFeedLink = $params->get('genericFeedLink',1);
						break;
						
					case 1 :			
						//Set layout
						$this->setLayout('filter_table');

						//Set limit
						$limit = $params->get('genericItemCount');

						$resultf = $moduleParams->resultf;
						
						if($total_items == 0) {
							$resultf = $moduleParams->noresult; 
						}
						
						$this->assignRef('resultf', $resultf);
						$this->assignRef('result_count', $total_items);
						$this->assignRef('extras', $extras);

						$addHeadFeedLink = $params->get('genericFeedLink',1);
						
						break;
					
					case 2 :
					
						//Set layout
						$this->setLayout('category');
						
						$resultf = $moduleParams->resultf;
						
						if($total_items == 0) {
							$resultf = $moduleParams->noresult; 
						}
																		
						$this->assignRef('resultf', $resultf);
						$this->assignRef('result_count', $total_items);
						$this->assignRef('extras', $extras);
						
						JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
						$category = JTable::getInstance('K2Category', 'Table');
						$category->load($category_id);

						// Hide the add new item link if user cannot post in the specific category
						if (!K2HelperPermissions::canAddItem($category_id)) {
							unset($this->addLink);
						}

						// Merge params
						$cparams = class_exists('JParameter') ? new JParameter($category->params) : new JRegistry($category->params);
						if ($cparams->get('inheritFrom')) {
								$masterCategory = &JTable::getInstance('K2Category', 'Table');
								$masterCategory->load($cparams->get('inheritFrom'));
								$cparams = class_exists('JParameter') ? new JParameter($masterCategory->params) : new JRegistry($masterCategory->params);
						}
						$params->merge($cparams);

						// Category link
						$category->link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));

						// Category image
						$category->image = K2HelperUtilities::getCategoryImage($category->image, $params);

						// Category plugins
						$dispatcher = JDispatcher::getInstance();
						JPluginHelper::importPlugin('content');
						$category->text = $category->description;
						$dispatcher->trigger('onContentPrepare', array('com_k2.category', &$category, &$params, $limitstart));
						$category->description = $category->text;

						// Category K2 plugins
						if(!$category->event) {
							$category->event = new \stdClass();
						}
						$category->event->K2CategoryDisplay = '';
						JPluginHelper::importPlugin('k2');
						$results = $dispatcher->trigger('onK2CategoryDisplay', array(&$category, &$params, $limitstart));
						$category->event->K2CategoryDisplay = trim(implode("\n", $results));
						$category->text = $category->description;
						$dispatcher->trigger('onK2PrepareContent', array ( & $category, &$params, $limitstart));
						$category->description = $category->text;

						$this->assignRef('category', $category);
						$this->assignRef('user', $user);
						
						// Category children
						$ordering = $params->get('subCatOrdering');
						$children = $model->getCategoryFirstChildren($category_id, $ordering);
						if (count((array)$children)) {
							foreach ($children as $child) {
								if ($params->get('subCatTitleItemCounter')) {
									if($model->getTotal($child->id)) {
										$child->numOfItems = $model->getTotal($child->id);
									}
									else {
										$child->numOfItems = 0;
									}
								}
								$child->image = K2HelperUtilities::getCategoryImage($child->image, $params);
								$child->name = htmlspecialchars($child->name, ENT_QUOTES);
								$child->link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($child->id.':'.urlencode($child->alias))));
								$subCategories[] = $child;
							}
							$this->assignRef('subCategories', $subCategories);
						}

						// Set limit
						$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');

						// Set featured flag
						JRequest::setVar('featured', $params->get('catFeaturedItems'));

						$category->name = htmlspecialchars($category->name, ENT_QUOTES);

						// Set ordering
						if($params->get('singleCatOrdering')) {
							$ordering = $params->get('singleCatOrdering');
						}
						else {
							$ordering = $params->get('catOrdering');
						}

						$addHeadFeedLink = $params->get('catFeedLink');							
							
						break;
				
				}
				
					//Search statistics
					if($moduleParams->searchstat == 1) {
						$model->searchstat();
					}
				
					//Set title
					$title = "";
					if($moduleParams->page_heading != "") {
						$title .= $moduleParams->page_heading;
					}
					if(JRequest::getVar('fitem_all') != "") {
						$title .= " " . JRequest::getVar('fitem_all');
					}
					foreach($_GET as $param=>$value) {
						preg_match('/^searchword([0-9]+)$/', $param, $matches);
						$i = $matches[1];
										
						$searchword = JRequest::getVar('searchword'.$i, '');
						if(is_array($searchword)) continue;
						
						if($searchword != "") {
							$title .= " " . $searchword;
						}
						
						preg_match('/^array([0-9]+)$/', $param, $matches);
						$i = $matches[1];
									
						$searchword = JRequest::getVar('array'.$i, '');
						if(is_array($searchword)) {
							foreach($searchword as $word) {
								if($searchword != "") {
									$title .= " " . $word;
								}
							}
						}
					}
					
					foreach($_GET as $param=>$value) {
						if($param == "restcata") {
							JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
							$category_search = &JTable::getInstance('K2Category', 'Table');
							$category_search->load($value);
							$title .= " " . $category_search->name;					
						}
						
						if($param == "category") {
							if(is_array($value)) {
								$value = $value[0];
							}
							JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
							$category_search = &JTable::getInstance('K2Category', 'Table');
							$category_search->load($value);
							$title .= " " . $category_search->name;
						}
					}
					
					$params->set('page_title', $title);

				break;
			// <<- ADDED K2FSM till here

			default:
				// Set layout
				$this->setLayout('category');
				$user = &JFactory::getUser();
				$this->assignRef('user', $user);

				// Set limit
				$limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');
				// Set featured flag
				JRequest::setVar('featured', $params->get('catFeaturedItems'));

				// Set title
				$title = $params->get('page_title');

				// Set ordering
				$ordering = $params->get('catOrdering');

				$addHeadFeedLink = $params->get('catFeedLink',1);

				break;

		}

		// Set limit for model
		if (!$limit) $limit = 10;
		
		if(JRequest::getInt("flimit") != "") {
			$limit = JRequest::getInt("flimit");
			if ($filter_template == 2) {
				//$params->set('num_primary_items', $limit);
			}
		}
		
		JRequest::setVar('limit', $limit);

		// Get items
		if(!isset($ordering)) {
			$items = $model->getData();
		}
		else {
			$items = $model->getData($ordering);
		}		

		// Pagination
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total_items, $limitstart, $limit);

		//Prepare items
		$user = JFactory::getUser();
		$cache = JFactory::getCache('com_k2_extended');
		
		/// Added K2FSM
		if($joomlaVersion < 1.6) {
			$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'K2Filter';
		}
		else {
			$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'K2Filter'.DS.'K2Filter';
		}
		
		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'item.php');
		$controller = new K2ControllerItem;							
	
		$controller->addModelPath($pluginPath.DS."models");
		
		$model = $controller->getModel('ItemFilter');
		
		/// Added K2FSM

		for ($i = 0; $i < sizeof($items); $i++) {

			//Item group
			//added
			if ($task == "category" || $task == "" || ($task == "filter" && $filter_template == 2)) {
			//added	
				if ($i < ($params->get('num_links') + $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items')))
					$items[$i]->itemGroup = 'links';
				if ($i < ($params->get('num_secondary_items') + $params->get('num_leading_items') + $params->get('num_primary_items')))
					$items[$i]->itemGroup = 'secondary';
				if ($i < ($params->get('num_primary_items') + $params->get('num_leading_items')))
					$items[$i]->itemGroup = 'primary';
				if ($i < $params->get('num_leading_items'))
					$items[$i]->itemGroup = 'leading';
			}

			// Check if the model should use the cache for preparing the item even if the user is logged in
			if ($user->guest || $task == 'tag' || $task == 'search' || $task == 'date' || $task == 'filter')
			{
				$cacheFlag = true;
			}
			else
			{
				$cacheFlag = true;
				if (K2HelperPermissions::canEditItem($items[$i]->created_by, $items[$i]->catid))
				{
					$cacheFlag = false;
				}
			}

			// Prepare item
			$items[$i] = $model->prepareItem($items[$i], $view, $task);

			// Plugins
			$items[$i] = $model->execPlugins($items[$i], $view, $task);

			// Trigger comments counter event
			$dispatcher = &JDispatcher::getInstance();
			JPluginHelper::importPlugin ('k2');
			$results = $dispatcher->trigger('onK2CommentsCounter', array ( & $items[$i], &$params, $limitstart));
			if(!$items[$i]->event) {
				$items[$i]->event = new stdClass;
			}
			$items[$i]->event->K2CommentsCounter = trim(implode("\n", $results));
			
			if($moduleParams->item_navigation) {
				$items[$i]->link .= "?fromsearch=1";
			}
		}
		
		//added
		if($task == "filter" && $filter_template == 1) {
			if($items) {
				foreach($items as $item) {
					if(is_object($item->extra_fields)) {
						foreach ($item->extra_fields as $extraField) {
							$flag = 0;
							foreach($extras as $extra) {
								if($extraField->id == $extra->id) {
									$flag = 1;
								}
							}
							if($flag == 0) {
								unset($extraField->value);
							}
						}
					}
				}
			}
		}
		//added

		// Set title
		$document = JFactory::getDocument();
		$application = JFactory::getApplication();
		$menus = $application->getMenu();
		$menu = $menus->getActive();
		if (is_object($menu)) {
				$menu_params = class_exists('JParameter') ? new JParameter($menu->params) : new JRegistry($menu->params);
				if (!$menu_params->get('page_title'))
						$params->set('page_title', $title);
		} else {
				$params->set('page_title', $title);
		}

		if(K2_JVERSION != '15') {
			if ($mainframe->getCfg('sitename_pagetitles', 0) == 1) {
				$tmpTitle = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $params->get('page_title'));
				$params->set('page_title', $tmpTitle);
			}
			elseif ($mainframe->getCfg('sitename_pagetitles', 0) == 2) {
				$tmpTitle = JText::sprintf('JPAGETITLE', $params->get('page_title'), $mainframe->getCfg('sitename'));
				$params->set('page_title', $tmpTitle);
			}
		}
		$document->setTitle($params->get('page_title'));

		// Set metadata for category
		if($task == 'category') {
			if ($params->get('catMetaDesc')) {
				$document->setDescription($params->get('catMetaDesc'));
			}
			else {
				$metaDescItem = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $this->category->description);
				$metaDescItem = K2HelperUtilities::characterLimit($metaDescItem, $params->get('metaDescLimit', 150));
				$metaDescItem = htmlentities($metaDescItem, ENT_QUOTES, 'utf-8');
				$document->setDescription($metaDescItem);
			}
			if ($params->get('catMetaKey')) {
				$document->setMetadata('keywords', $params->get('catMetaKey'));
			}
			if ($params->get('catMetaRobots')) {
				$document->setMetadata('robots', $params->get('catMetaRobots'));
			}
			if ($params->get('catMetaAuthor')) {
				$document->setMetadata('author', $params->get('catMetaAuthor'));
			}
		}

		if(K2_JVERSION != '15') {

			// Menu metadata options
			if ($params->get('menu-meta_description')) {
				$document->setDescription($params->get('menu-meta_description'));
			}

			if ($params->get('menu-meta_keywords')) {
				$document->setMetadata('keywords', $params->get('menu-meta_keywords'));
			}

			if ($params->get('robots')) {
				$document->setMetadata('robots', $params->get('robots'));
			}

			// Menu page display options
			if($params->get('page_heading')) {
				$params->set('page_title', $params->get('page_heading'));
			}
			$params->set('show_page_title', $params->get('show_page_heading'));

		}

		// Pathway
		$pathway = &$mainframe->getPathWay();
		if (!isset($menu->query['task'])) $menu->query['task']='';
		if ($menu) {
			switch ($task) {
				case 'category':
					if ($menu->query['task']!='category' || $menu->query['id']!= JRequest::getInt('id'))
						$pathway->addItem($title, '');
					break;
				case 'user':
					if ($menu->query['task']!='user' || $menu->query['id']!= JRequest::getInt('id'))
						$pathway->addItem($title, '');
					break;

				case 'tag':
					if ($menu->query['task']!='tag' || $menu->query['tag']!= JRequest::getVar('tag'))
						$pathway->addItem($title, '');
					break;

				case 'search':
				case 'date':
					$pathway->addItem($title, '');
					break;
			}
		}

		// Feed link
		$config = JFactory::getConfig();
		$application = JFactory::getApplication();
		$menu = $application->getMenu();
		$default = $menu->getDefault();
		$active = $menu->getActive();
		if ($task=='tag'){
			$link = K2HelperRoute::getTagRoute(JRequest::getVar('tag'));
		} else {
			$link='';
		}
		$sef = K2_JVERSION == '30' ? $config->get('sef') : $config->getValue('config.sef');
		if (!is_null($active) && $active->id == $default->id && $sef)
		{
			$link .= '&Itemid='.$active->id.'&format=feed&limitstart=';
		}
		else
		{
			$link .= '&format=feed&limitstart=';
		}

		$feed = JRoute::_($link);
		$this->assignRef('feed', $feed);

		// Add head feed link
		if ($addHeadFeedLink){
			$attribs = array('type'=>'application/rss+xml', 'title'=>'RSS 2.0');
			$document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type'=>'application/atom+xml', 'title'=>'Atom 1.0');
			$document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
		
		// Load Facebook meta tag for category image (don't use the placeholder)
		if($task == 'category' && $this->category->image && strpos($this->category->image,'placeholder/category.png')===false)
		{
			//$document->setMetaData('image', JString::str_ireplace(JURI::root(true).'/', JURI::root(false), $this->category->image));
			$document->setMetaData('image',substr(JURI::root(),0,-1).str_replace(JURI::root(true),'',$this->category->image));
		}

		// Assign data
		//added
		if ($task == "category" || $task == "" || ($task == "filter" && $filter_template == 2)) {
		//added		
				$leading = @array_slice($items, 0, $params->get('num_leading_items'));
				$primary = @array_slice($items, $params->get('num_leading_items'), $params->get('num_primary_items'));
				$secondary = @array_slice($items, $params->get('num_leading_items') + $params->get('num_primary_items'), $params->get('num_secondary_items'));
				$links = @array_slice($items, $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items'), $params->get('num_links'));
				$this->assignRef('leading', $leading);
				$this->assignRef('primary', $primary);
				$this->assignRef('secondary', $secondary);
				$this->assignRef('links', $links);
		} else {
				$this->assignRef('items', $items);
		}

		// Set default values to avoid division by zero
		if ($params->get('num_leading_columns')==0)
			$params->set('num_leading_columns',1);
		if ($params->get('num_primary_columns')==0)
			$params->set('num_primary_columns',1);
		if ($params->get('num_secondary_columns')==0)
			$params->set('num_secondary_columns',1);
		if ($params->get('num_links_columns')==0)
			$params->set('num_links_columns',1);

		$this->assignRef('params', $params);
		$this->assignRef('pagination', $pagination);

		// Look for template files in component folders
		$this->_addPath('template', JPATH_COMPONENT.DS.'templates');
		$this->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.'default');

		// Look for overrides in template folder (K2 template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates');
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.'default');

		// Look for overrides in template folder (Joomla! template structure)
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'default');
		$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2');

		// Look for specific K2 theme files
		if ($params->get('theme')) {
				$this->_addPath('template', JPATH_COMPONENT.DS.'templates'.DS.$params->get('theme'));
				$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.'templates'.DS.$params->get('theme'));
				$this->_addPath('template', JPATH_SITE.DS.'templates'.DS.$mainframe->getTemplate().DS.'html'.DS.'com_k2'.DS.$params->get('theme'));
		}

		$nullDate = $db->getNullDate();
		$this->assignRef('nullDate', $nullDate);
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin ('k2');
		$dispatcher->trigger('onK2BeforeViewDisplay');

		parent::display($tpl);

	}
}
