<?php
require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

$ajax_filename = 'ajax_update_gcse_qualifications.php';

if (! isset($_POST['action']) )
{
	print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "", $exclude_datatables_js = true);

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
     <td>Type</td>
     <td><input type='text' name='Type' /></td>
    </tr>
    <tr>
     <td>Length</td>
     <td><input type='text' name='Length' /></td>
    </tr>
    <tr>
     <td>EquivalentGCSE</td>
     <td><input type='text' name='EquivalentGCSE' /></td>
    </tr>
    <tr>
     <td>Notes</td>
     <td><input type='text' name='Notes' /></td>
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
	
	$id             = get_post_val('id');
	$Type           = get_post_val('Type');
	$Length         = get_post_val('Length');
	$EquivalentGCSE = get_post_val('EquivalentGCSE');
	$Notes          = get_post_val('Notes');

	print("action = ".$action);
	
	print("<p>&dollar;id             = ".$id."</p>");
	print("<p>&dollar;Type           = ".$Type."</p>");
	print("<p>&dollar;Length         = ".$Length."</p>");
	print("<p>&dollar;EquivalentGCSE = ".$EquivalentGCSE."</p>");
	print("<p>&dollar;Notes          = ".$Notes."</p>");

	if ($action == "delete")
	{
		$sql = "DELETE FROM gcse_qualification WHERE id='".$id."'";
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO gcse_qualification (Type, Length, EquivalentGCSE, Notes) VALUES ('".$Type."', '".$Length."', '".$EquivalentGCSE."', '".$Notes."' )";
	}
	else if ($action == "update")
	{
		$sql = "UPDATE gcse_qualification SET Type='".$Type."', Length='".$Length."', EquivalentGCSE='".$EquivalentGCSE."', Notes='".$Notes."' WHERE id='".$id."';";
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
