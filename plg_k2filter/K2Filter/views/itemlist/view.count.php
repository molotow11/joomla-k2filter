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

		$mainframe = &JFactory::getApplication();
		$params = &K2HelperUtilities::getParams('com_k2');
		$model = &$this->getModel('itemlist');
		$limitstart = JRequest::getInt('limitstart');
		$view = JRequest::getWord('view');
		$task = JRequest::getWord('task');
		$db = &JFactory::getDBO();
		
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
		
		$model = &$controller->getModel('ItemListFilter');
		/// Added K2FSM

		echo $model->getTotal();

	}
}
