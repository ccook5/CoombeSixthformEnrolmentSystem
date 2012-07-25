<?php

require_once('../config.inc.php');

function is_authenticated()
{
	global $config;
	$admin_users = explode(',', $config['admins']);
	
	if (in_array(get_current_user(), $admin_users))
	{
		return true;
	}
	else
	{
		return false;
	}
}

?>

