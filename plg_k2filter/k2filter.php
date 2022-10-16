<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');
jimport('joomla.version');

class plgSystemK2Filter extends JPlugin {
	
	function onAfterRoute() {				
		if(JRequest::getVar("option") == "com_k2" && JRequest::getVar("view") == "itemlist") {		
			$mainframe = JFactory::getApplication();
			
			$component = JRequest::getVar("option");
			$view = JRequest::getVar("view");
			$task = JRequest::getVar("task");
			$format = JRequest::getVar("format");
			
			if($task == "filter") {				
				ini_set("display_errors", "On");			
				error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

				ini_set("memory_limit", "400M");
				ini_set("max_execution_time", "300"); 
			
				//JSession::checkToken('get') or die('Invalid Token');
			
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menu->setActive(JRequest::getVar("Itemid"));

				if (!defined('JPATH_ROOT')) {
				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));
				}
						
				if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.'/components/com_k2');
				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');
				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');
				
				require_once (JPATH_SITE.DS.'modules'.DS.'mod_k2_filter'.DS.'helper.php');
				$moduleParams = modK2FilterHelper::getModuleParams(JRequest::getInt("moduleId"));
				
				require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'itemlist.php');
				$controller = new K2ControllerItemlist;				

				$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter';
				
				$config['name'] =  "itemlist";
				$config['default_task'] =  "display";
				$config['base_path'] =  $pluginPath;
				$config['model_path'] =  $pluginPath.DS."models";
				$config['view_path'] =  $pluginPath.DS."views";
				
				$controller->__construct($config);
				
				switch($format) {
					case "raw" :
						$view = $controller->getView("itemlist", "raw");
					break;						
					
					case "json" :
						$view = $controller->getView("itemlist", "json");
					break;
					
					case "suggestions" :
						$view = $controller->getView("itemlist", "suggestions");
					break;
					
					case "count" :
						$view = $controller->getView("itemlist", "count");
					break;	
					
					case "dynobox" :
						$view = $controller->getView("itemlist", "dynobox");
					break;						
					
					case "feed" :
						JFactory::$document = JDocument::getInstance('feed');
						$view = $controller->getView("itemlist", "feed");
					break;
					
					case "html" :
						$view = $controller->getView("itemlist", "html");
					break;						
					
					default :
						$view = $controller->getView("itemlist", "html");
					break;
				}
					
				$view->addTemplatePath($pluginPath.DS.'templates');
				$controller->addModelPath($pluginPath.DS."models");
				
				$cache = JFactory::getCache("com_k2");
				$cache->clean();
				//$cache = JFactory::getCache("com_k2_extended");
				//$cache->clean();
				
				//added for item navigation
				if($moduleParams->item_navigation) {
					JURI::current();// It's very strange, but without this line at least Joomla 3 fails to fulfill the task
					$router = JSite::getRouter();// get router
					$query = $router->parse(JURI::getInstance()); // Get the real joomla query as an array - parse current joomla link
					$query_string = JURI::getInstance()->buildQuery($query);
					$session = JFactory::getSession();
					$session->set('lastsearch', $query_string);
				}		
				//added for item navigation
			}
		}
		
		//added for item navigation
		if(JRequest::getInt("fromsearch", 0) == 1) {
			//change k2 item template 
			if (!defined('JPATH_ROOT')) {
				define('JPATH_ROOT', JPath::clean(JPATH_SITE));
			} 
			if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
			if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT', JPATH_BASE.DS.'components'.DS.'com_k2');
			if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_k2');
			if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2');

			require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'controllers'.DS.'item.php');
			$controller = new K2ControllerItem; 
			
			$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'k2filter'.DS.'K2Filter';
			
			$config['name'] = "item";
			$config['default_task'] = "display";
			$config['base_path'] = $pluginPath;
			$config['model_path'] = $pluginPath.DS."models";
			$config['view_path'] = $pluginPath.DS."views";

			$controller->__construct($config); 
			$view = $controller->getView("item", "html");
			$controller->addModelPath($pluginPath.DS.'models'); 
			$view = $controller->getView("item", 'html');
		}
		//added for item navigation
	}	
}

?>