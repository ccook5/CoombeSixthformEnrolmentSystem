<?php 
require_once 'config.inc.php';

/** Builds a javascript list from an sql query. This gets used within the javascript 
  *
  * @param $sql           An sql query to get the data from the database.
  * @param $list_name     The name of the javascript variable that contains the array.
  * @param $key_column    The name of the column in the sql database 
  *                       table to use for the key. Note: the speling needs 
  *                       to match the table column name perfectly or it will bug out.
  * @param $value_column  The name of the column in the database table to use 
  *                       for the value. Like $key_column, this needs to be spelled
  *                       exactly the same as the column name in the database.
  * @param $value_column2 This is optional, and can put some extra data into the javascript
  *                       array.
  *
  * @return               Javascript code containing an array populated from the database.
  */
function build_list($sql, $list_name, $key_column, $value_column, $value_column2 = "")
{
	global $link;
	$list = "\n";
	$result = mysql_query($sql, $link);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		while($row = mysql_fetch_array($result))  {
			if ($value_column2 == "") {
				$list .= "			".$list_name."[\"".$row[$key_column]."\"] = '".$row[$value_column]."';\n";
			} else {
				$list .= "			".$list_name."[\"".$row[$key_column]."\"] = '".$row[$value_column]." - ".$row[$value_column2]."';\n";
			}
		}
	}
	return $list;
}

/** Builds a javascript list from an sql query. 
  *
  * TODO: Is this just a duplicate of above?
  *
  * In some cases, we want to have a list of foreign keys matched against primary keys in the javascript.
  */
function build_f_key_list($sql, $list_name, $key_column, $fk_column)
{
	global $link;
	$list = "\n";
	$result = mysql_query($sql, $link);
	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
		while($row = mysql_fetch_array($result))  {
			$list .= "			".$list_name."[\"".$row[$key_column]."\"] = '".$row[$fk_column]."';\n";
		}
	}
	return $list;
}

/** Creates a javascript function that creates a html select element.
  *
  * <spans> are around the options because you can't hide an option within 
  * a select on all browsers. JS designers are idiots. 
  *
  * See later because once you do this, selectedIndex refers only to the 
  * elements that arn't wraped in span tags. However, when you look up the 
  * option via dom using the new selectedIndex, you get the wrong option...
  *
  * This is now fixed with a jquery selector.
  *
  * TODO: try changing build_f_key_list() to plain build_list(). 
  * I can't see why it wouldn't work, but don't have time to test it now.
  */
function create_select_builder($func_name, 
                               $sql, 
							   $class_name_test, 
							   $key_column, 
							   $value_column, 
							   $value_column2 = "", 
							   $f_key_column  = "", 
							   $id_column     = "")
{
	$s  = "\n";
	$s .= "		function ". $func_name ."( d ) {\n";
	$s .= "			var arr = Array();\n";
	$s .= "         ".build_list($sql, "arr", $key_column, $value_column, $value_column2)."\n";
	if ($f_key_column != "") {
		$s .= "			var f_keys = Array();\n";
		$s .= "         ".build_f_key_list($sql, "f_keys", $key_column, $f_key_column)."\n";
	}
	$s .= "			var s = '<select class=\"".$class_name_test."\">';\n";
	$s .= "			for (var i in arr)\n";
	$s .= "			{\n";
	$s .= "				s += '<span>';\n";
	$s .= "				s += ' <option';\n";
	$s .= "				s += '  class=\"".$class_name_test."\"';\n";
	$s .= "				s += '  id='+i+''\n";
	if ($f_key_column != "") {
		$s .= "				s += ' value=' + f_keys[i];\n";
	}
	$s .= "				if (d == arr[i]) {\n";
	$s .= "					s += ' selected=\"selected\"';\n";
	$s .= "				}\n";
	$s .= "				s += '>' + arr[i] + '</option>';\n";
	$s .= "				s += ' </span>';\n";
	$s .= "			}\n";
	$s .= "			s += '</select>';\n";
	$s .= "			return s;\n";
	$s .= "		}\n";

	return $s;
}
?>