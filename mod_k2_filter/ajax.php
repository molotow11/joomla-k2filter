<?php 

	header('Access-Control-Allow-Origin: *');
	header('Content-Type: text/html');

	//no  cache headers 
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
    define('_JEXEC', 1);
	define('DS', DIRECTORY_SEPARATOR);

	ini_set("display_errors", "On");
	error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
	
	$my_path = dirname(__FILE__);
	$my_path = explode(DS.'modules',$my_path);	
	$my_path = $my_path[0];			
	
	if (file_exists($my_path . '/defines.php')) {
		include_once $my_path . '/defines.php';
	}

	if (!defined('_JDEFINES')) {
		define('JPATH_BASE', $my_path);
		require_once JPATH_BASE.'/includes/defines.php';
	}

	require_once JPATH_BASE.'/includes/framework.php';
	$app = JFactory::getApplication('site');
	$app->initialise();
	
	///////////////////////////////////////////////////////////////////////////////////////////////

	$name = $_GET['name'];
	$value = trim(mb_strtolower($_GET['value']));
	$next = trim(mb_strtolower($_GET['next']));

	$db = JFactory::getDBO();
	
	jimport('joomla.filesystem.file');
	if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_falang'.DS.'falang.php')) {
		$lang = preg_replace("/[^A-Za-z0-9]/", "", JRequest::getVar("lang"));

		$query  = "SELECT value as name, reference_id as id,";
		$query .= " (SELECT value FROM #__falang_content WHERE reference_table = 'k2_extra_fields' AND reference_id = fc.reference_id AND reference_field = 'value' AND language_id = fc.language_id AND value != '[]' LIMIT 0,1) as value,";
		$query .= " lang.sef as lang";
		$query .= " FROM #__falang_content as fc";
		$query .= " LEFT JOIN #__languages as lang ON lang.lang_id = fc.language_id";
		$query .= " WHERE fc.reference_table = 'k2_extra_fields'";
		$query .= " AND fc.reference_field = 'name' AND fc.published = 1";
		$query .= " AND lang.sef = '{$lang}'";

		$db->setQuery($query);
		$results = $db->loadObjectList();
	}

	if(!count($results)) {
		$query = "SELECT * FROM #__k2_extra_fields WHERE published = 1";
		$db->setQuery($query);
		$results = $db->loadObjectList();
	}
	
	$extra_val = '';
	$extra_id = 0;

	foreach($results as $result) {
		if($value == "getall") {
			if (strpos(trim(mb_strtolower($result->name)), $next) !== false) {
				foreach(json_decode($result->value) as $val) {
					$val->forField = $result->id;
					$extra_val[] = $val;
				}
			}
		}
		else {
			if(
				trim(mb_strtolower($result->name)) == trim($value) . " " . trim($next) 
				|| 
				trim(mb_strtolower($result->name)) == trim($next) . " " . trim($value)
			) {
				$extra_val = json_decode($result->value);
				$extra_id = $result->id;
				break;
			}
		}
	}
	
	//usort($extra_val, 'compareasc');

	if($extra_val != '') {
		foreach($extra_val as $val) {
			if(property_exists($val, 'forField')) {
				echo "<option data-extra-id='{$val->forField}'>" . $val->name . "</option>";
			}
			else {
				echo "<option>" . $val->name . "</option>";
			}
		}
		
		if($value != 'getall') {
			echo "<option>".$extra_id."</option>";
		}
	}
	
	function compareasc($v1, $v2) {
	   if ($v1->name == $v2->name) return 0;
	   return ($v1->name < $v2->name)?-1:1;
	}

?>