<?php
/**
 * @version     $Id: header.php 1647 2012-09-26 16:30:16Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ;

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

if(!file_exists(JPATH_ADMINISTRATOR.'/components/com_k2/elements/base.php')) {
	echo "You need to install K2 first.<br />";
	echo "<a href='https://getk2.org'>https://getk2.org</a>";
	die;
}

require_once (JPATH_ADMINISTRATOR.'/components/com_k2/elements/base.php');

class K2ElementHeader extends K2Element
{
    public function fetchElement($name, $value, &$node, $control_name)
    {
		if(!JFile::exists(JPATH_SITE . '/components/com_k2/k2.php')) {
			echo "
				<h2>K2 Component not installed</h2>
				<p>K2 it is content construction
				kit for Joomla, <br /> it is an alternative for standard Joomla article
				manager.</p>

				<p>
				You can learn more about K2 at this pages: <br />
				<a href='http://getk2.org/about' target='_blank'>http://getk2.org/about</a> <br />
				<a href='http://getk2.org/documentation' target='_blank'>http://getk2.org/documentation</a> <br />
			";
			die;
		}
		
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root(true).'/media/k2/assets/css/k2.modules.css?v=2.6.0');
        return '<div class="paramHeaderContainer jwHeaderContainer"><div class="paramHeaderContent jwHeaderContent">'.JText::_($value).'</div><div class="k2clr jwHeaderClr"></div></div>';
    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name)
    {
        return NULL;
    }

}

class JFormFieldHeader extends K2ElementHeader
{
    var $type = 'header';
}

class JElementHeader extends K2ElementHeader
{
    var $_name = 'header';
}
