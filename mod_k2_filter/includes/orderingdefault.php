<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldOrderingDefault extends JFormField {
	var $_name = 'orderingdefault';

	var	$type = 'orderingdefault';

	function getInput(){
		return JFormFieldOrderingDefault::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
	
	function fetchElement($name, $value, &$node, $control_name)
	{
	
			$mitems[] = JHTML::_('select.option',  'date', JText::_('MOD_K2_FILTER_ORDERING_DATE') );
			$mitems[] = JHTML::_('select.option',  'alpha', JText::_('MOD_K2_FILTER_ORDERING_TITLE') );
			$mitems[] = JHTML::_('select.option',  'order', JText::_('MOD_K2_FILTER_ORDERING_ORDER') );
			$mitems[] = JHTML::_('select.option',  'featured', JText::_('MOD_K2_FILTER_ORDERING_FEATURED') );
			$mitems[] = JHTML::_('select.option',  'hits', JText::_('MOD_K2_FILTER_ORDERING_HITS') );
			$mitems[] = JHTML::_('select.option',  'rand', JText::_('MOD_K2_FILTER_ORDERING_RANDOM') );
			$mitems[] = JHTML::_('select.option',  'best', JText::_('MOD_K2_FILTER_ORDERING_RATING') );
			$mitems[] = JHTML::_('select.option',  'id', JText::_('MOD_K2_FILTER_ORDERING_ID') );
	
			$db = JFactory::getDBO();
	
			$query = "SELECT t.*, g.name AS group_name ";
			$query .= "FROM #__k2_extra_fields AS t ";
			$query .= "LEFT JOIN #__k2_extra_fields_groups AS g ON g.id = t.group ";
			$query .= "WHERE t.published = 1 ";
			$query .= "ORDER BY group_name, t.ordering ";
			
			$db->setQuery( $query );
			$list = $db->loadObjectList();
			
			if(count($list)) {
				$group = $list[0]->group_name;
				array_splice( $list, 0, 0, $group );
				
				for($i = 1; $i < count($list); $i++) {
					$new_group = $list[$i]->group_name;
					if($new_group != $group) {
						array_splice( $list, $i, 0, $new_group );
						$group = $new_group;
					}
				}

				foreach ($list as $item) {
					if(is_object($item)) {
						$mitems[] = JHTML::_('select.option',  $item->id, '   '.$item->name." [".$item->id."]" );
					}
					else {
						$mitems[] = JHTML::_('select.option',  '', '   --------- '.$item.' ---------' );
					}
				}
			}
			
			$mitems[] = JHTML::_('select.option',  'k2store', JText::_('MOD_K2_FILTER_ORDERING_K2STORE') );
			$mitems[] = JHTML::_('select.option',  'j2store', JText::_('MOD_K2_FILTER_FILTER_TYPE_PRICE_RANGE_J2STORE') );
			
			$fieldName = $name;

			$output = JHTML::_('select.genericlist',  $mitems, $fieldName, null, 'value', 'text', $value );
			return $output;
	}
}

?>