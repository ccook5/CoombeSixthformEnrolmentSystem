<?php 

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

function print_students_for_course($course_id)
{
	global $config, $link;

	print("    <table class='report-courses-students'>\n");

	$sql    = "SELECT * FROM students INNER JOIN BLOCKS_CourseEnrolment ON students.id=BLOCKS_CourseEnrolment.StudentID";
    $sql   .= " WHERE BLOCKS_CourseEnrolment.CourseID='".$course_id."' AND students.EnrolmentYear='".$config['current_year']."'";
	$result = mysql_query($sql, $link);

	if ($result)
	{
		if (mysql_num_rows($result) > 0)
		{
?>
     <thead>
	  <tr>
       <th>First Name</th><th>Last Name</th>
	  </tr>
     </thead>
     <tbody>
<?php
			while ($row3 = mysql_fetch_array($result))
			{
				print("      <tr><td>".$row3['FirstName']."</td><td>".$row3['LastName']."</td></tr>\n");
			}
			print("    </tbody>\n");
		} else {print("-");}
		print("   </table>\n");
	}
	else
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}

$sql = "SELECT * FROM BLOCKS_Blocks WHERE Year='".$config['current_year']."' ORDER BY id";
$result = mysql_query($sql, $link);

while ($row = mysql_fetch_array($result)) {
	print("   <h1> Block: ".$row['Name'].".</h1>\n");
	
	$sql2 = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id WHERE BlockID='".$row['id']."' AND EnrolmentYear='".$config['current_year']."'";

	$result2 = mysql_query($sql2, $link);
	
	while ($row2 = mysql_fetch_array($result2)) {
?>
   <div class='report-courses'>
    <h2>Course: <?php echo $row2['SubjectName']; ?></h2>
<?php print_students_for_course($row2[0]); ?>
   </div>
<?php
	}
	print("   <hr />");
}

print_footer();
?>
