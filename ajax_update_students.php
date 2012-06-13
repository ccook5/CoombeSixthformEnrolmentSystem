<?php

require_once('config.inc.php');

if (! isset($_POST['action'])) {
?>
<html>
 <body>
  <h4>ajax_students_results.php tester</h4>
  <table style='width: 300px; border: 1px solid black;' >
   <form action="ajax_update_students.php" method="post"> 
    <tr>
     <td>Action</td>
     <td>
      <select name='action' /><option>delete</option><option>new</option><option>update</option></select>
     </td>
    </tr>
    <tr>
     <td>Action</td>
     <td>
      <select name='EnrolmentYear' /> 
       <option>2010</option><option>2011</option>
       <option>2012</option><option>2013</option>
      </select>
     </td>
    </tr>
    <tr><td>ID</td><td><input type='text' name='student_id' /></td></tr>
    <tr><td>First Name</td><td><input type='text' name='FirstName'/></td></tr>
    <tr><td>Last Name</td><td><input type='text' name='LastName' /></td></tr>
    <tr><td>Student Type</td>
     <td>
      <select name='StudentType' /> 
<?php
	//Convery Student type to an integer/key value from the student type table
		$sql_student_types    = "SELECT * FROM StudentTypes";
		$result_student_types = mysql_query($sql_student_types, $link);

		if (!$result_student_types) {
			die('Invalid query: ' . mysql_error());
		} else {
			$matched = false;
			while($row_student_type = mysql_fetch_assoc($result_student_types)) {
				print("       <option>".$row_student_type['CourseType']."</option>\n");
			}
		}
?>
       </select>
      </td>
     </tr>
     <tr>
      <td>Previous Institution:</td>
      <td><input type="text" name="PreviousInstitution" /> </td>
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
	} else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}

	if ($action == "delete") {
	//TODO: Delete the row
	}
	else if ($action == "update" or $action == "new")
	{
	//Convery Student type to an integer/key value from the student type table
		$sql_student_types    = "SELECT * FROM StudentTypes";
		$result_student_types = mysql_query($sql_student_types, $link);

		if (!$result_student_types) {
			die('Invalid query: ' . mysql_error());
		} else {
			print($StudentType);
			$matched = false;
			while($row_student_type = mysql_fetch_assoc($result_student_types)) {
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
			$sql = "INSERT INTO students (id, EnrolmentYear, FirstName, LastName, StudentType, PreviousInstitution) 
			VALUES ('".$student_id."', '".$config['current_year']."', '".$FirstName."', '".$LastName."', '".$StudentType."', '".$PreviousInstitution."')";
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
