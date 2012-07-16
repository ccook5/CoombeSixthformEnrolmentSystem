<?php 

require_once 'config.inc.php';
require_once 'header.inc.php';
require_once 'footer.inc.php';

$type = "";
if (isset($_GET["type"])) {
	$type = mysql_real_escape_string($_GET["type"]);
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

function print_students_for_course($course_id)
{
	global $config, $link;
	$arr = array();
	
	$sql = "SELECT * FROM students INNER JOIN BLOCKS_CourseEnrolment ON students.id=BLOCKS_CourseEnrolment.StudentID WHERE BLOCKS_CourseEnrolment.CourseID='".$course_id."'";
	
//	print $sql;
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

switch ($type)
{
	case "courses"  :
		echo ("courses");
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
		break;
	case "students" :
		echo ("students");
		break;
// sql query - get a list of student types
		$sql = "SELECT * FROM StudentTypes";
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
			while ($row3 = mysql_fetch_array($res3)) {
				print("   <tr>");	
				print("    <td>".$row3["FirstName"]."</td>");	
				print("    <td>".$row3["LastName"]."</td>");	
				print("    <td></td>");
				
				$sql3 = "SELECT * FROM BLOCKS_CourseEnrolment INNER JOIN BLOCKS_Course ON BLOCKS_Course.id=BLOCKS_CourseEnrolment.CourseID INNER JOIN BLOCKS_CourseDef ON BLOCKS_Course.CourseDefID=BLOCKS_CourseDEF.id WHERE StudentID='".$row3['id']."' ";
				$res3 = mysql_query($sql3, $link) or die("error3");
				$courses = array();
				while ($row3 = mysql_fetch_array($res3)) {
					$courses[ $row3['BlockID'] ] = $row3['SubjectName'];
				}
				
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
		break;
	case "waiting"  :
		echo ("waiting");
		break;
	case "remaining":
		echo ("remaining");
		break;

	default:
?>
  <ul>
   <li><a href="/reports/courses.php">View all course instances and enrolled students.</a></li>
   <li><a href="/reports/students.php">View all students and their courses</a></li>
   <li><a href="/reports/waiting.php">View subject waiting lists</a></li>
   <li><a href="/reports/remaining.php">View places remaining.</a></li>
  </ul>
<?php
		break;
}

print_footer();
?>
