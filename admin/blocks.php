<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

// get a list of student/course types

$sql          = "SELECT * FROM StudentTypes;";
$result       = mysql_query($sql, $link);
$course_types = Array();
$first_course = "";

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}
else
{
  while($row = mysql_fetch_array($result))
  {
	if ($first_course == "") {
		$first_course = $row['id'];
	}
	$course_types[$row['id']] = $row['CourseType'];
  }
}

if (! isset($_GET['block_id']))
{
    print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "", $exclude_datatables_js = false, $meta="<meta http-equiv='refresh' content='0;url=blocks.php?block_id=".$first_course."'>");
	die();
}
else if ($_GET['block_id'] == "null")
{
	die;
}
else
{
	$block_id = mysql_real_escape_string($_GET['block_id']);
}

print_header($title = 'Coombe Sixth Form Enrolment', 
	$hide_title_bar = false, 
	$script = "", 
	$exclude_datatables_js = true, 
	$meta = "",
	$extra_script="blocks.js.php");


foreach($course_types as $ct_key => $ct_value)
{
	echo('     <a href="blocks.php?block_id='.($ct_key)."\">\n");
	echo('      '.$ct_value."</a> |\n");
}

/** Print the html table for the students enrolments.
  *
  * We use get_students_current_enrolments() to get a list of what they have allready.
  * This table contains form elements that should let us submit changes to the enrolments.
  */

echo("      <div class='block'><table class='admin-blocks'>\n");
echo("       <tr>\n");

$blocks             = Array();
$i                  = 0;
$cols = 0;

// Get a list of the current blocks from the database. This should be a list like 'a','b','c',etc.
$sql_blocks    = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType='".$block_id."' ORDER BY id";
$result_blocks = mysql_query($sql_blocks, $link);

if ($result_blocks)
{
	$cols = mysql_num_rows($result_blocks);
	while($row_blocks = mysql_fetch_array($result_blocks))
	{
		echo ("<td><table>");
		echo ("      <thead><th width='10%'>".$row_blocks['Name']."</th></thead>");
		
		/** Get all the current courses for the current block.
		  *
		  * We build a list for each block, then print by row in a loop 
		  * later on in the code, so this isn't inefficient. 
		  */
		$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id";
		$sql   .= " INNER JOIN StudentTypes ON Type=StudentTypes.id";
		$sql   .= " WHERE EnrolmentYear=".$config['current_year']." AND BlockID=".$row_blocks['id'];
		$result = mysql_query($sql, $link);

		if ($result) {
			$i = 0;
			while($row = mysql_fetch_array($result))
			{
?>
      <tr>
	   <td id='course_<?php echo($row[0]); ?>'>
        <?php echo($row['SubjectName']." <br /><input type='text' id='ip_".$row[0]."' value='".$row["MaxPupils"]."' disabled='disabled' size='2' />"); ?>
		<div style='float: right;'>
		<span
		 class='ui-icon ui-icon-trash'
		 onClick='remove_course(event, <?php echo($row_blocks['id'].", \"".$row["SubjectName"]."\", ".$row[0]); ?>)'> </span>
		<span
		 class='ui-icon ui-icon-pencil'
		 onClick='edit_course(event, <?php echo($row_blocks['id'].", \"".$row["SubjectName"]."\", ".$row[0]); ?>)'> </span>
		</div>
	   </td>
	  </tr>
<?php	
				$i += 1;
			}
			echo("</table>");
			
			echo "<input type='button' onClick=\"add_to_block(event, ".$row_blocks['id'].")\" value='Add' style='position: absolute; bottom: 2px; left: 2px;' />";
		} else {  die('Invalid query: ' . mysql_error());  }
	}
} else {  die('Invalid query: ' . mysql_error());  }
	
echo("       </tr>\n");
echo("       <tr><td colspan='".$cols."'>\n");

// Get a list of the current course definitions
$sql    = "SELECT * FROM BLOCKS_CourseDef WHERE Type='".$block_id."' ORDER BY SubjectName";
$result = mysql_query($sql, $link);

if ($result)
{
	print('<select id="course">');
	while($row = mysql_fetch_array($result))
	{
		print('<option value="'.$row['id'].'">'.$row['SubjectName'].'</option>');
	}
	print('</select>');
}
?><br />
		 <label for="MaxPupils">Maximum Pupils for course</label>
         <input type="textbox" value="30" name="MaxPupils" id="MaxPupils" onkeyup="update_slider(event);" />
         <div id="slider"></div>
        </td>
	   </tr>
      </table></div>
