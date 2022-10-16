<?php
/*
// K2 Multiple Extra fields Filter and Search module by Andrey M
// molotow11@gmail.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class pkg_k2filterInstallerScript {
	
	public function postflight($route, $_this) {
		  $db = JFactory::getDbo();
		  try
		  {
			 $q = $db->getQuery(true);
			 $q->update('#__extensions');
			 $q->set(array('enabled = 1'));
			 $q->where("element = 'k2filter'");
			 $q->where("type = 'plugin'", 'AND');
			 $db->setQuery($q);
			 method_exists($db, 'execute') ? $db->execute() : $db->query();
		  }
		  catch (Exception $e)
		  {
			 throw $e;
		  }
    }
	
}
 
?>