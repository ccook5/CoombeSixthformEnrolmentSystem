<?php

require_once('config.inc.php');

function print_html_select($sql, $column_to_show, $select_name, $select_id = "", $select_class = "", $select_style = "")
{
	global $link;
	echo "      <select name='".$select_name."' ";
	if ($select_id    != "") echo "id='"   .$select_id."' ";
	if ($select_class != "") echo "class='".$select_class."' ";
	if ($select_style != "") echo "style='".$select_style."' ";
    echo "	/>\n";

	$result = mysql_query($sql, $link);

	if (!$result) {
		die('Invalid query: ' . mysql_error());
	} else {
			$matched = false;
			while($row = mysql_fetch_assoc($result)) {
				print("       <option>".$row[$column_to_show]."</option>\n");
			}
	}
	echo "     </select>\n";
}

if (! isset($_POST['action'])) {
?>
<html>
 <body>
  <h4>ajax_update_students_results.php tester</h4>
  <table style='width: 300px; border: 1px solid black;' >
   <form action="ajax_update_students.php" method="post"> 
    <tr>
     <td>Action</td>
     <td>
      <select name='action' /> 
       <option>delete</option>
       <option>new</option>
       <option>update</option>
      </select>
     </td>
    </tr>
    <tr>
     <td>ID</td>
     <td><input type='text' name='student_id' /></td>
    </tr>
    <tr>
     <td>Subject</td>
     <td>
	  <?php print_html_select("SELECT * FROM GCSE_Subjects", "Name", "Subject"); ?>
     </td>
    </tr>
    <tr>
     <td>Grade</td>
     <td>
	  <?php print_html_select("SELECT * FROM GCSE_Grade", "Grade", "Grade"); ?>
     </td>
    </tr>
    <tr>
     <td>Student ID</td>
     <td>
     </td>
     </tr>
     <tr><td><input type="submit" /></td></tr>
   </form>
  </table>
 </body>
</html>
<?php
} else {

	//This is unsafe at the moment, we need to filter as its user input, and might contain sql; DROP * FROM sixthformenrolemnt;"
	$action = mysql_real_escape_string($_POST['action']);
	$student_id = mysql_real_escape_string($_POST['student_id']);

	print("action = ".$action);
	
	if ($action == "delete") {
	//We have all the info we need
	}
	else if ($action == "update" or $action == "new") {
		$FirstName           = mysql_real_escape_string($_POST['FirstName']);
		$LastName            = mysql_real_escape_string($_POST['LastName']);
		$StudentType         = mysql_real_escape_string($_POST['StudentType']);
		$PreviousInstitution = mysql_real_escape_string($_POST['PreviousInstitution']);
		$EnrolmentYear       = mysql_real_escape_string($_POST['EnrolmentYear']);
		
	}
	else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}

	if ($action == "delete") {
	//We have all the info we need
	}
	else if ($action == "update" or $action == "new")
	{
	//Convery Student type to an integer/key value from the student type table
		$sql_student_types = "SELECT * FROM StudentTypes";

		$result_student_types = mysql_query($sql_student_types, $link);

		if (!$result_student_types)
		{
			die('Invalid query: ' . mysql_error());
		}
		else
		{
			print($StudentType);
			$matched = false;
			while($row_student_type = mysql_fetch_assoc($result_student_types))
			{
				print($row_student_type['CourseType']);
				if ($row_student_type['CourseType'] == $StudentType) 
				{
					$matched = true;
					$StudentType = $row_student_type['id'];
				}
			}
			if ($matched == false)
			{
	// We might want to add the student type, or we might not...
			}
			
		}

		$sql = '';
	// if $action = new then make the sql query INSERT
		if ($action == "new")
		{
			$sql = "INSERT INTO students (id, EnrolmentYear, FirstName, LastName, StudentType, PreviousInstitution) VALUES ('".$student_id."', '".$config['current_year']."', '".$FirstName."', '".$LastName."', '".$StudentType."', '".$PreviousInstitution."')";
		}
	// else make the query UPDATE
		else if ($action == "update")
		{
			$sql = "UPDATE students SET EnrolmentYear='".$config['current_year']."', FirstName='".$FirstName."', LastName='".$LastName."', StudentType='".$StudentType."', PreviousInstitution='".$PreviousInstitution."' WHERE id='".$student_id."'";
		}
		
		print($sql);
		$result = mysql_query($sql, $link);

		if (!$result)
		{
		  die('Invalid query: ' . mysql_error());
		}
		else
		{
		}
	}
}
?>
