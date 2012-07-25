<?php

/** Gets a value from http POST data
  *
  * Return 0 if not found.
  */
function get_post_val($key)
{
	if (isset($_POST[$key]))
	{
		return mysql_real_escape_string($_POST[$key]);
	}
	return 0;
}

?>

