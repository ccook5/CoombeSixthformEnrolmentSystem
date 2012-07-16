<?php

require_once 'config.inc.php';
require_once 'header.inc.php';

$StudentID    = mysql_real_escape_string($_POST['StudentID']);
$CourseTypeID = mysql_real_escape_String($_POST['CourseTypeID']);

echo $StudentID;

print_header($title = 'Coombe Sixth Form Enrolment - Blocks', 
				$hide_title_bar        = true, 
				$script                = "", 
				$exclude_datatables_js = true, 
				$meta                  ="      <meta http-equiv='refresh' content='1;url=/students_blocks.php?student_id=".$StudentID."'/>");

// delete all the current enrolments
if (isset($_POST['StudentID']))
{
	$StudentID     = mysql_real_escape_string($_POST['StudentID']);
	$EnrolmentYear = $config['current_year'];
	$sql           = "DELETE FROM BLOCKS_courseenrolment WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$EnrolmentYear."'";
	$result        = mysql_query($sql, $link);

	print("sql: ".$sql);
	if (!$result)
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}

echo "New enrolments:\n";
print_r($_POST);

foreach ($_POST['block'] as $value)
{
	$sql_insert  = "INSERT INTO BLOCKS_CourseEnrolment (CourseID, StudentID, EnrolmentYear) VALUES ('";
	$sql_insert .= $value."', '".$StudentID."', '".$config['current_year']."')";
	print($sql_insert."\n");
	
	$result_insert = mysql_query($sql_insert, $link);

	if ($result_insert)
	{
		print("done\n");
	} else {  die('Invalid query: ' . mysql_error());  }
}

?>

