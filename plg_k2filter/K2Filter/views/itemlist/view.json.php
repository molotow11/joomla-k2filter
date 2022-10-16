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

class K2ViewItemlist extends K2View
{

    function display($tpl = null)
    {

        $mainframe = JFactory::getApplication();
        $params = K2HelperUtilities::getParams('com_k2');
        $document = JFactory::getDocument();
        if (K2_JVERSION == '15')
        {
            $document->setMimeEncoding('application/json');
            $document->setType('json');
        }
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

		// Set limit for model
		if (!$limit) $limit = 10;
		
		if(JRequest::getInt("flimit") != "") {
			$limit = JRequest::getInt("flimit");
			if ($filter_template == 2) {
				$params->set('num_primary_items', $limit);
			}
		}
		
		JRequest::setVar('limit', $limit);
		
        if ($limit > 100 || $limit == 0)
        {
            $limit = 100;
            JRequest::setVar('limit', $limit);
        }
        $page = JRequest::getInt('page');
        if ($page <= 0)
        {
            $limitstart = 0;
        }
        else
        {
            $page--;
            $limitstart = $page * $limit;
        }
        JRequest::setVar('limitstart', $limitstart);

        $view = JRequest::getWord('view');
        $task = JRequest::getWord('task');

        $response = new JObject();
        unset($response->_errors);

        // Site
        $response->site = new stdClass();
        $uri = JURI::getInstance();
        $response->site->url = $uri->toString(array('scheme', 'host', 'port'));
        $config = JFactory::getConfig();
        $response->site->name = K2_JVERSION == '30' ? $config->get('sitename') : $config->getValue('config.sitename');

        $moduleID = JRequest::getInt('moduleID');
        if ($moduleID)
        {

            $result = $model->getModuleItems($moduleID);
            $items = $result->items;
            $title = $result->title;
            $prefix = 'cat';

        }
        else
        {

            //Get data depending on task
            switch ($task)
            {

                case 'search' :

                    //Set title
                    $title = JText::_('K2_SEARCH_RESULTS_FOR').' '.JRequest::getVar('searchword');

                    // Set parameters prefix
                    $prefix = 'generic';
                    //$response->search = JRequest::getVar('searchword');
                    break;

                default :
                    $user = JFactory::getUser();

                    //Set limit
                    $limit = $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items') + $params->get('num_links');
                    //Set featured flag
                    JRequest::setVar('featured', $params->get('catFeaturedItems'));

                    //Set title
                    $title = $params->get('page_title');

                    // Set ordering
                    $ordering = $params->get('catOrdering');

                    // Set parameters prefix
                    $prefix = 'cat';

                    break;
            }

            if (!isset($ordering))
            {
                $items = $model->getData();
            }
            else
            {
                $items = $model->getData($ordering);
            }

        }

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
		
		$model = &$controller->getModel('ItemFilter');
		
		/// Added K2FSM
        $rows = array();
        for ($i = 0; $i < sizeof($items); $i++)
        {

            //Item group
            if ($task == "category" || $task == "")
            {
                $items[$i]->itemGroup = 'links';

                if ($i < ($params->get('num_links') + $params->get('num_leading_items') + $params->get('num_primary_items') + $params->get('num_secondary_items')))
                    $items[$i]->itemGroup = 'links';
                if ($i < ($params->get('num_secondary_items') + $params->get('num_leading_items') + $params->get('num_primary_items')))
                    $items[$i]->itemGroup = 'secondary';
                if ($i < ($params->get('num_primary_items') + $params->get('num_leading_items')))
                    $items[$i]->itemGroup = 'primary';
                if ($i < $params->get('num_leading_items'))
                    $items[$i]->itemGroup = 'leading';
            }
            else
            {
                $items[$i]->itemGroup = '';
            }

            $itemParams = class_exists('JParameter') ? new JParameter($items[$i]->params) : new JRegistry($items[$i]->params);
            $itemParams->set($prefix.'ItemIntroText', true);
            $itemParams->set($prefix.'ItemFullText', true);
            $itemParams->set($prefix.'ItemTags', true);
            $itemParams->set($prefix.'ItemExtraFields', true);
            $itemParams->set($prefix.'ItemAttachments', true);
            $itemParams->set($prefix.'ItemRating', true);
            $itemParams->set($prefix.'ItemAuthor', true);
            $itemParams->set($prefix.'ItemImageGallery', true);
            $itemParams->set($prefix.'ItemVideo', true);
            $itemParams->set($prefix.'ItemImage', true);
            $items[$i]->params = $itemParams->toString();

            //Check if model should use cache for preparing item even if user is logged in
            if ($user->guest || $task == 'tag' || $task == 'search' || $task == 'date')
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

            //Prepare item
            if ($cacheFlag)
            {
                $hits = $items[$i]->hits;
                $items[$i]->hits = 0;
                JTable::getInstance('K2Category', 'Table');
                $items[$i] = $cache->call(array('K2ModelItemFilter', 'prepareItem'), $items[$i], $view, $task);
                $items[$i]->hits = $hits;
            }
            else
            {
                $items[$i] = $model->prepareItem($items[$i], $view, $task);
            }

            //Plugins
            $items[$i] = $model->execPlugins($items[$i], $view, $task);

            //Trigger comments counter event
            $dispatcher = JDispatcher::getInstance();
            JPluginHelper::importPlugin('k2');
            $results = $dispatcher->trigger('onK2CommentsCounter', array(&$items[$i], &$params, $limitstart));
            $items[$i]->event->K2CommentsCounter = trim(implode("\n", $results));

            // Set default image
            if ($task == 'user' || $task == 'tag' || $task == 'search' || $task == 'date')
            {
                $items[$i]->image = (isset($items[$i]->imageGeneric)) ? $items[$i]->imageGeneric : '';
            }
            else
            {
                if (!$moduleID)
                {
                    K2HelperUtilities::setDefaultImage($items[$i], $view, $params);

                }
            }

            $rows[] = $model->prepareJSONItem($items[$i]);

        }

        $response->items = $rows;
        
        // Prevent spammers from using the tag view
        if ($task == 'tag' && !count($response->items))
        {
            $tag = JRequest::getString('tag');
            $db = JFactory::getDBO();
            $db->setQuery('SELECT id FROM #__k2_tags WHERE name = '.$db->quote($tag));
            $tagID = $db->loadResult();
            if (!$tagID)
            {
                JError::raiseError(404, JText::_('K2_NOT_FOUND'));
                return false;
            }
        }

        // Output
        $json = json_encode($response);

        echo $json;
		die();
    }

}
