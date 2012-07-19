<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

if (! isset($_POST['action']) )
{
	print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "

		$(document).ready(function()
		{
			update_grade_selectbox();
		});
		
		$('select.gcse_type').live('change', update_grade_selectbox );", $exclude_datatables_js = false);
?>
  <h4>ajax_update_studenttype.php tester</h4>
  <form action="ajax_update_studenttype.php" method="post"> 
   <table style='width: 300px; border: 1px solid black;' >
    <tr>
     <td>Action</td>
     <td>
      <select name='action' > 
       <option>delete</option><option>new</option><option>update</option>
      </select>
     </td>
    </tr>
    <tr>
     <td>id</td>
     <td><input type='text' name='id' /></td>
    </tr>
    <tr>
     <td>type</td>
     <td><input type='text' name='type' /></td>
    </tr>
    <tr><td><input type="submit" /></td></tr>
   </table>
  </form>
<?php
	print_footer();
} else {
	$action = mysql_real_escape_string($_POST['action']);
	$id = mysql_real_escape_string($_POST['id']);

	print("action = ".$action);

	if ($action == "delete") { //We have all the info we need
	}
	else if ($action == "update" or $action == "new")
	{
		$type         = mysql_real_escape_string($_POST['type']);

		print("<p>&dollar;id = ".$id."</p>");
		print("<p>&dollar;type   = ".$type."</p>");
	}
	else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}
	
	if ($action == "delete") {
		$sql = "DELETE FROM studenttypes WHERE id='".$id."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO studenttypes (CourseType) VALUES ('".$type."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE studenttypes SET id='".$id."', CourseType='".$type."' WHERE id='".$id."'";
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print('sql:'.$sql);
		die('Invalid query: ' . mysql_error());
	}
}
?>
