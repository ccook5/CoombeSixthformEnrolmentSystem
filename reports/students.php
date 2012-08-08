<?php 

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

function get_students_courses($StudentID)
{
	global $link, $config;
	
	$sql3  = "SELECT * FROM BLOCKS_CourseEnrolment";
	$sql3 .= " INNER JOIN BLOCKS_Course ON BLOCKS_Course.id=BLOCKS_CourseEnrolment.CourseID";
	$sql3 .= " INNER JOIN BLOCKS_CourseDef ON BLOCKS_Course.CourseDefID=BLOCKS_CourseDEF.id";
	$sql3 .= " WHERE StudentID='".$StudentID."';";
	$res_gsc = mysql_query($sql3, $link) or die("Error: Problem with Query : ".$sql3);

	$courses = array();
	while ($row_gsc = mysql_fetch_array($res_gsc)) {
		$courses[ $row_gsc['BlockID'] ] = $row_gsc['SubjectName'];
	}
	
	return $courses;
}

// sql query - get a list of student types
$sql = "SELECT StudentTypes.id, CourseType FROM StudentTypes INNER JOIN students ON StudentTypes.id=students.StudentType WHERE EnrolmentYear=2012 GROUP BY CourseType;";
$result = mysql_query($sql, $link);

while ($row = mysql_fetch_array($result)) {
	print("  <h1>".$row['CourseType']."</h1>");

	print("  <table class='with-borders'>");
	print("   <thead>");
	print("    <td>First Name</td><td>Last Name</td><td>Notes</td>");

	$sql2 = "SELECT * FROM BLOCKS_Blocks WHERE CourseType='".$row['id']."' AND YEAR='".$config['current_year']."' ORDER BY id";
	$res = mysql_query($sql2, $link) or die("error1");
	
	$block_list = array();
	while ($row2 = mysql_fetch_array($res)) {	
		print("    <td>".$row2["Name"]."</td>");	
		$block_list[$row2["id"]] = $row2["Name"];
	}
	print("   </thead>");

//print table body
	$sql3 = "SELECT * FROM students WHERE StudentType='".$row['id']."' AND EnrolmentYear='".$config['current_year']."'";
	$res3 = mysql_query($sql3, $link) or die("error2");
	while ($row3 = mysql_fetch_array($res3))
	{
		print("   <tr>");	
		print("    <td>".$row3["FirstName"]."</td>");	
		print("    <td>".$row3["LastName"]."</td>");	
		print("    <td></td>");
		
		$courses = get_students_courses($row3['id']);
		
		foreach ($block_list as $block_id => $block) {
			print("<td>");
			if (isset($courses[$block_id])) {
				print($courses[$block_id]);
			}
			print("</td>");	
		}
	    print("</tr>");
	}
	print("</table>");
}

print_footer();
?>
