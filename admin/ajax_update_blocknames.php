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
  <h4>ajax_update_blocknames.php tester</h4>
  <form action="ajax_update_blocknames.php" method="post"> 
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
     <td>name</td>
     <td><input type='text' name='name' /></td>
    </tr>
    <tr>
     <td>coursetype</td>
     <td><input type='text' name='coursetype' /></td>
    </tr>
    <tr>
     <td>year</td>
     <td><input type='text' name='year' value="2012" /></td>
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
		print_r($_POST);
		$name         = mysql_real_escape_string($_POST['name']);
		$coursetype   = mysql_real_escape_string($_POST['coursetype']);
		$year         = mysql_real_escape_string($_POST['year']);

		print("<p>&dollar;id         = ".$id."</p>");
		print("<p>&dollar;Name       = ".$name."</p>");
		print("<p>&dollar;CourseType = ".$coursetype."</p>");
	}
	else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}
	
	if ($action == "delete") {
		$sql = "DELETE FROM BLOCKS_Blocks WHERE id='".$id."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO BLOCKS_Blocks (Name, CourseType, Year) VALUES ('".$name."', '".$coursetype."', '".$year."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE BLOCKS_Blocks SET id='".$id."', Name='".$name."', CourseType='".$coursetype."' WHERE id='".$id."'";
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print('sql:'.$sql);
		die('Invalid query: ' . mysql_error());
	}
}
?>
