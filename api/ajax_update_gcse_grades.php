<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

$ajax_filename = 'ajax_update_gcse_grades.php';

$Table_Name = 'GCSE_Grade';
	
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
<?php
	print_edit_table_row('Grade');
	print_edit_table_row('Points');
	print_edit_table_row('QualificationID');	
?>
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

// Query -- id, Grade, Points, QualificationID
	$Grade           = get_post_val('Grade');
	$Points          = get_post_val('Points');
	$QualificationID = get_post_val('QualificationID');
	
	if ($action == "delete")
	{
		$sql = "DELETE FROM ".$Table_Name." WHERE id='".$id."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO ".$Table_Name." (Grade, Points, QualificationID) VALUES ('".$Grade."', '".$Points."', '".$QualificationID."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE ".$Table_Name." SET Grade='".$Grade."', Points='".$Points."', QualificationID='".$QualificationID."' WHERE id='".$id."'";
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
