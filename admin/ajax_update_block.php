<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

//TODO : this file should be called something different, like ajax_update_block_course.

if (! isset($_POST['action']) )
{
	print_header($title = 'Coombe Sixth form enrolment form.', $hide_title_bar = true, $script = "

		$(document).ready(function()
		{
			update_grade_selectbox();
		});
		
		$('select.gcse_type').live('change', update_grade_selectbox );", $exclude_datatables_js = false);
?>
  <h4>ajax_update_block.php tester</h4>
  <form action="ajax_update_block.php" method="post"> 
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
     <td>BlockID</td>
     <td><input type='text' name='BlockID' /></td>
    </tr>
    <tr>
     <td>EnrolmentYear</td>
     <td><input type='text' name='EnrolmentYear' value='<?php echo $config['current_year']; ?>' /></td>
    </tr>
    <tr>
     <td>MaxPupils</td>
     <td><input type='text' name='MaxPupils' /></td>
    </tr>
    <tr>
     <td>CourseDefID</td>
     <td><input type='text' name='CourseDefID' /></td>
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

	$BlockID         = get_post_val('BlockID');
	$MaxPupils       = get_post_val('MaxPupils');
	$CourseDefID     = get_post_val('CourseDefID');

	print("action = ".$action);

	print("<p>&dollar;BlockID     = ".$BlockID."</p>");
	print("<p>&dollar;MaxPupils   = ".$MaxPupils."</p>");
	print("<p>&dollar;CourseDefID = ".$CourseDefID."</p>");

	if ($action == "delete")
	{
		if (isset($_POST['id']) )
		{
			$sql = "DELETE FROM BLOCKS_Course WHERE id='".$id."'";
		}
		else
		{
			$sql  = "DELETE FROM BLOCKS_Course WHERE BlockID='".$BlockID."' ";
			$sql .= "AND MaxPupils='".$MaxPupils."' AND CourseDefID='".$CourseDefID."' AND EnrolmentYear='".$config['current_year']."'";
		}
	}
	else if ($action == "new")
	{
		$sql = "INSERT INTO BLOCKS_Course (BlockID, MaxPupils, CourseDefID, EnrolmentYear) VALUES ('".$BlockID."', '".$MaxPupils."', '".$CourseDefID."', '".$config['current_year']."');";
	}
	else if ($action == "update")
	{
		$sql  = "UPDATE BLOCKS_Course SET BlockID='".$BlockID."', MaxPupils='".$MaxPupils;
		if (isset($_POST['CourseDefID']))
		{
			$sql .= "', CourseDefID='".$CourseDefID;
		}
		$sql .= "', EnrolmentYear='".$config['current_year']."' WHERE id='".$id."'";
	}
	else {
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
