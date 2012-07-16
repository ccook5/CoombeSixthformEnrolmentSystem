<?php
require_once('config.inc.php');
require_once('footer.inc.php');

if (! isset($_POST['action']))
{
print_header($title = 'Coombe Sixth form enrolment form. - students_details tester', $hide_title_bar = true, $script = "", $exclude_datatables_js = false);

?>
  <table style='width: 300px; border: 1px solid black;' >
   <form action="ajax_update_students.php" method="post"> 
    <tr>
     <td>Action</td>
     <td><select name='action'><option>delete</option><option>new</option><option>update</option></select></td>
    </tr>
    <tr>
     <td>Enrolment Year</td>
     <td>
      <select name='EnrolmentYear'> 
       <option>2010</option><option>2011</option>
       <option>2012</option><option>2013</option>
      </select>
     </td>
    </tr>
    <tr><td>ID</td>        <td><input type='text' name='student_id' /></td></tr>
    <tr><td>First Name</td><td><input type='text' name='FirstName'  /></td></tr>
    <tr><td>Last Name</td> <td><input type='text' name='LastName'   /></td></tr>
    <tr><td>Student Type</td>
     <td>
      <select name='StudentType' /> 
<?php
		$sql_student_types    = "SELECT * FROM StudentTypes";
		$result_student_types = mysql_query($sql_student_types, $link);

		if (!$result_student_types) {
			die('Invalid query: ' . mysql_error());
		} else {
			while($row_student_type = mysql_fetch_assoc($result_student_types)) {
				print("       <option>".$row_student_type['CourseType']."</option>\n");
			}
		}
?>
       </select>
      </td>
     </tr>
     <tr><td>Previous Institution:</td><td><input type="text" name="PreviousInstitution" /></td></tr>
     <tr><td><input type="submit" /></td></tr>
   </form>
  </table>
<?php
	print_footer();
} else {
	$action    = mysql_real_escape_string($_POST['action']);
	$StudentID = mysql_real_escape_string($_POST['StudentID']);

	if ($action == "delete") { //We have all the info we need
	}
	else if ($action == "update" or $action == "new") {
		$FirstName           = mysql_real_escape_string($_POST['FirstName']);
		$LastName            = mysql_real_escape_string($_POST['LastName']);
		$StudentType         = mysql_real_escape_string($_POST['StudentType']);
		$PreviousInstitution = mysql_real_escape_string($_POST['PreviousInstitution']);
		$EnrolmentYear       = mysql_real_escape_string($_POST['EnrolmentYear']);
	} else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}

	if ($action == "delete") {
		$sql           = "DELETE FROM GCSE_Results WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$config['current_year']."'";
		$result        = mysql_query($sql, $link);

		if (!$result)
		{
			die('Invalid query: ' . mysql_error()." On line ".__line__);
		}
		
		$sql           = "DELETE FROM BLOCKS_courseenrolment WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$config['current_year']."'";
		$result        = mysql_query($sql, $link);

		if (!$result)
		{
			die('Invalid query: ' . mysql_error()." On line ".__line__);
		}
		$sql = "DELETE FROM students WHERE id='".$StudentID."' AND EnrolmentYear='".$config['current_year']."'";
	}
	else if ($action == "update" or $action == "new")
	{
	//Convert Student type to an integer/key value from the student type table
		$sql_student_types    = "SELECT * FROM StudentTypes WHERE CourseType='".$StudentType."' LIMIT 1";
		$result_student_types = mysql_query($sql_student_types, $link);

		if (!$result_student_types) {
			print("sql: ".$sql_student_types );
			die('Invalid query: ' . mysql_error()." On line ".__line__);
		} else {
			if (mysql_num_rows($result_student_types) < 1) {
				die('Error: Not enough Rows');
			}
			$row_student_type = mysql_fetch_assoc($result_student_types);
			
			if ($row_student_type['CourseType'] == $StudentType) {
				$StudentType = $row_student_type['id'];
			}
		}
		$sql = '';
	// if $action = new then make the sql query INSERT
		if ($action == "new")
		{
			$sql = "INSERT INTO students (id, EnrolmentYear, FirstName, LastName, StudentType, PreviousInstitution) 
			VALUES ('".$StudentID."', '".$config['current_year']."', '".$FirstName."', '".$LastName."', '".$StudentType."', '".$PreviousInstitution."')";
		}
	// else make the query UPDATE
		else if ($action == "update")
		{
			$sql  = "UPDATE students SET ";
			$sql .= "EnrolmentYear='".$config['current_year']."', ";
			$sql .= "FirstName='".$FirstName."', ";
			$sql .= "LastName='".$LastName."', ";
			$sql .= "StudentType='".$StudentType."', ";
			$sql .= "PreviousInstitution='".$PreviousInstitution."' ";
			$sql .= "WHERE id='".$StudentID."'";
		}
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print("sql: ".$sql);
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}
?>
