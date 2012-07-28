<?php

/** Gets a value from http POST data
  *
  * Return 0 if not found.
  */
function get_post_val($key)
{
	if (isset($_POST[$key]))
	{
		
		print("<p>&dollar;".$key."          = ".$_POST[$key]."</p>");
		return mysql_real_escape_string($_POST[$key]);
	}
	return 0;
}


function print_edit_table_row($ColumnName)
{
?>
    <tr>
     <td><?php echo $ColumnName; ?></td>
     <td><input type='text' name='echo $ColumnName; ?>' /></td>
    </tr>
<?php
}

?>

