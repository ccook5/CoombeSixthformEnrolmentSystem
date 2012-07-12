<?php

require_once('config.inc.php');

if (isset($_POST['StudentID']))
{
	$StudentID     = mysql_real_escape_string($_POST['StudentID']);
	$EnrolmentYear = $config['current_year'];
	$sql           = "DELETE FROM GCSE_Results WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$EnrolmentYear."'";
	$result        = mysql_query($sql, $link);

		print("sql: ".$sql);
	if (!$result)
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}
?>