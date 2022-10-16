<?php
/**
 * @version		$Id: view.feed.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
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

		$mainframe = &JFactory::getApplication();
		$params = &K2HelperUtilities::getParams('com_k2');
		$document = &JFactory::getDocument();
		$model = &$this->getModel('itemlist');
		$limitstart = JRequest::getInt('limitstart');
		
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
		
		$model = &$controller->getModel('ItemListFilter');
		/// Added K2FSM

		$moduleID = JRequest::getInt('moduleID');
		if ($moduleID) {

			$result = $model->getModuleItems($moduleID);
			$items = $result->items;
			$title = $result->title;

		}
		else {

			//Get data depending on task
			$task = JRequest::getCmd('task');
			switch ($task) {
					
				// ADDED K2FSM from here ->>
				case 'filter':
					require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
					$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
				
					$resultf = JText::_($moduleParams->resultf);
					
					if($model->getTotal() == 0) {
						$resultf = JText::_($moduleParams->noresult); 
						if($resultf == "")
							$resultf = "No search results";
					}
					
					//Set title
					$title = $resultf;
					
					$this->assignRef('resultf', $resultf);
					
					break;
				// <<- ADDED K2FSM till here

				default:

					//Set featured flag
					JRequest::setVar('featured', $params->get('catFeaturedItems'));

					//Set title
					$title = $params->get('page_title');
					
					// Set ordering
					$ordering = $params->get('catOrdering');

					break;

			}

			//Get items
			if(!isset($ordering)) {
				$items = $model->getData();
			}
			else {
				$items = $model->getData($ordering);
			}


		}

		//Prepare feed items
		$model = &$this->getModel('item');
		foreach ($items as $item) {

			$item = $model->prepareFeedItem($item);
			$item->title = $this->escape($item->title);
			$item->title = html_entity_decode($item->title);
			$feedItem = new JFeedItem();
			$feedItem->title = $item->title;
			$feedItem->link = $item->link;
			$feedItem->description = $item->description;
			$feedItem->date = $item->created;
			$feedItem->category = $item->category->name;
			$feedItem->author = $item->author->name;
			if($params->get('feedBogusEmail')) {
				$feedItem->authorEmail = $params->get('feedBogusEmail');
			}
			else {
				if($mainframe->getCfg('feed_email') == 'author') {
					$feedItem->authorEmail = $item->author->email;
				}
				else {
					$feedItem->authorEmail = $mainframe->getCfg('mailfrom');
				}
			}

			//Add item
			$document->addItem($feedItem);
		}

		//Set title
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
				$title = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $params->get('page_title'));
				$params->set('page_title', $title);
			}
			elseif ($mainframe->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $params->get('page_title'), $mainframe->getCfg('sitename'));
				$params->set('page_title', $title);
			}
		}
		$document->setTitle($params->get('page_title'));

	}

}
