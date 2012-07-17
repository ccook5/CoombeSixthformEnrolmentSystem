<?php 

require_once '../config.inc.php';
require_once '../header.inc.php';
require_once '../footer.inc.php';

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

function print_students_for_course($course_id)
{
	global $config, $link;
	$arr = array();

	$sql = "SELECT * FROM students INNER JOIN BLOCKS_CourseEnrolment ON students.id=BLOCKS_CourseEnrolment.StudentID WHERE BLOCKS_CourseEnrolment.CourseID='".$course_id."'";

//  print $sql;
	$result = mysql_query($sql, $link);

	if (!$result)
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
	while ($row = mysql_fetch_array($result)) {
		$arr[] = $row['FirstName'];
		print($row['FirstName']." ".$row['LastName']);
	}
}

$sql = "SELECT * FROM BLOCKS_Blocks WHERE Year='".$config['current_year']."' ORDER BY id";
$result = mysql_query($sql, $link);

while ($row = mysql_fetch_array($result)) {
	print("  <h1>".$row['Name'].".</h1>");
	$sql2 = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id WHERE BlockID='".$row['id']."' AND EnrolmentYear='".$config['current_year']."'";
	//print($sql2);
	$result2 = mysql_query($sql2, $link);
	
	while ($row2 = mysql_fetch_array($result2)) {
		print("  <h1>".$row2['SubjectName']."</h1>");
		print_students_for_course($row2['id']);
	}
}

print_footer();
?>
