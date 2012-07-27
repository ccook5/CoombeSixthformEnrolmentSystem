<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

$ajax_filename = 'ajax_update_gcse_subjects.php';

if (! isset($_POST['action']) )
{
	print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "

		$(document).ready(function()
		{
			update_grade_selectbox();
		});
		
		$('select.gcse_type').live('change', update_grade_selectbox );", $exclude_datatables_js = true
	);

	echo("  <h4>".$ajax_filename." tester</h4>\n");
    echo("  <form action=\"".$ajax_filename."\" method=\"post\">\n");
?>
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
     <td>SubjectName</td>
     <td><input type='text' name='SubjectName' /></td>
    </tr>
    <tr><td><input type="submit" /></td></tr>
   </table>
  </form>
<?php
	print_footer();
}
else
{
	$action = mysql_real_escape_string($_POST['action']);
	
	$id = get_post_val('id');

	$SubjectName    = get_post_val('SubjectName');

	print("action = ".$action);
	
	print("<p>&dollar;id          = ".$id."</p>");
	print("<p>&dollar;SubjectName = ".$SubjectName."</p>");

	if ($action == "delete")
	{
		$sql = "DELETE FROM GCSE_Subjects WHERE id='".$id."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO GCSE_Subjects (Name) VALUES ('".$SubjectName."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE GCSE_Subjects SET id='".$id."', Name='".$SubjectName."'";
	}
	else
	{
		print("<div class='error'>Error: Incorrect Action</div>");
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print('sql:'.$sql);
		die('Invalid query: ' . mysql_error());
	}
}
?>
