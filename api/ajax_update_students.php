<?php
require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

$ajax_filename = 'ajax_update_students.php';
	
$sql = '';

if (! isset($_POST['action']))
{
    print_header($title = 'Coombe Sixth form enrolment form. - students_details tester', $hide_title_bar = true, $script = "", $exclude_datatables_js = false);

	echo("  <h4>".$ajax_filename." tester</h4>\n");
    echo("  <form action=\"".$ajax_filename."\" method=\"post\">\n");
?>
  <table style='width: 300px; border: 1px solid black;' >
    <tr>
     <td>Action</td>
     <td><select name='action'><option>delete</option><option>new</option><option>update</option></select></td>
    </tr>
    <tr>
     <td>Enrolment Year</td>
     <td>
      <select name='EnrolmentYear'> 
       <option>2010</option><option>2011</option>
       <option selected="selected">2012</option><option>2013</option>
      </select>
     </td>
    </tr>
    <tr><td>ID</td>        <td><input type='text' name='StudentID' /></td></tr>
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
    </table>
   </form>
<?php
	print_footer();
}
else
{
	$action    = get_post_val('action');
	$StudentID = get_post_val('StudentID');
	
	echo $StudentID;
	print_r($_POST);

	$FirstName           = get_post_val('FirstName');
	$LastName            = get_post_val('LastName');
	$StudentType         = get_post_val('StudentType');
	$PreviousInstitution = get_post_val('PreviousInstitution');
	$EnrolmentYear       = get_post_val('EnrolmentYear');

	if ($action == "delete") {
		delete_all_results_for_student($StudentID);

		delete_all_courseenrolments_for_student($StudentID);

		$sql = "DELETE FROM students WHERE id='".$StudentID."' AND EnrolmentYear='".$config['current_year']."'";
	}
	else if ($action == "update" or $action == "new")
	{
		echo 1;
		if (! is_numeric($StudentType) ) {
			echo $StudentType;
			$StudentType = get_studenttype_as_id($StudentType);
		}
		echo 2;
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
	} else {
	    print("<div class='error'>Error: Incorrect Action</div>");
	}

	echo "$SQL: '".$sql."'";
	if (! mysql_query($sql, $link))
	{
		print("sql: ".$sql);
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	} else {
		echo "success";
	}
}
?>
