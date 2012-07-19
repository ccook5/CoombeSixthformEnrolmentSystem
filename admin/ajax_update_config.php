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
  <h4>ajax_update_config.php tester</h4>
  <form action="ajax_update_config.php" method="post"> 
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
     <td>Setting</td>
     <td><input type='text' name='setting' /></td>
    </tr>
    <tr>
     <td>value</td>
     <td><input type='text' name='value' /></td>
    </tr>
    <tr>
     <td>About</td>
     <td><textarea name="about" rows="2" cols="20"></textarea></td>
    </tr>
    <tr><td><input type="submit" /></td></tr>
   </table>
  </form>
<?php
	print_footer();
} else {
	$action = mysql_real_escape_string($_POST['action']);
	$setting = mysql_real_escape_string($_POST['setting']);

	print("action = ".$action);

	if ($action == "delete") { //We have all the info we need
	}
	else if ($action == "update" or $action == "new")
	{
		$value         = mysql_real_escape_string($_POST['value']);
		$about         = mysql_real_escape_string($_POST['about']);

		print("<p>&dollar;setting = ".$setting."</p>");
		print("<p>&dollar;value   = ".$value."</p>");
		print("<p>&dollar;about   = ".$about."</p>");
	}
	else {
		print("<div class='error'>Error: Incorrect Action</div>");
	}
	
	if ($action == "delete") {
		$sql = "DELETE FROM configuration WHERE setting='".$setting."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO configuration (setting, value, about) VALUES ('".$setting."', '".$value."', '".$about."')";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE configuration SET setting='".$setting."', value='".$value."', about='".$about."' WHERE setting='".$setting."'";
	}

	$result = mysql_query($sql, $link);

	if (!$result)
	{
		print('sql:'.$sql);
		die('Invalid query: ' . mysql_error());
	}
}
?>
