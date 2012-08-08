<?php
require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');
require_once('functions.inc.php');

$StudentID    = get_post_val('StudentID');
$CourseTypeID = get_post_val('CourseTypeID');

print_header($title = 'Coombe Sixth Form Enrolment - Blocks', 
				$hide_title_bar        = true, 
				$script                = "", 
				$exclude_datatables_js = true, 
				$meta                  ="      <meta http-equiv='refresh' content='0;url=/students_blocks.php?student_id=".$StudentID."'/>");

// delete all the current enrolments
if (isset($_POST['StudentID']))
{
	delete_all_courseenrolments_for_student(get_post_val('StudentID'));

	if (isset($_POST['block']) )
	{
		foreach ($_POST['block'] as $value)
		{
			$sql_insert  = "INSERT INTO BLOCKS_CourseEnrolment (CourseID, StudentID, EnrolmentYear) VALUES ('";
			$sql_insert .= $value."', '".$StudentID."', '".$config['current_year']."')";

			$result_insert = mysql_query($sql_insert, $link);

			if (!$result_insert)
			{
				die('Invalid query: ' . mysql_error().'. SQL: '.$sql_insert );
			}
		}
	}
	echo("<h1 class='success'>Done.</h1>\n");
}
else
{
	echo("<h1 class='error'>Error: StudentID not found.</h1>\n");
}

print_footer();
?>

