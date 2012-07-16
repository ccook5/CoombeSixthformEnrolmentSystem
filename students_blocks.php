<?php

require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');

if (! isset($_GET['student_id'])) {
	die( "<div class='error'>No student Id found</div>" );
} else if ($_GET['student_id'] == "null") {
	die;
} else {
	$StudentID = mysql_real_escape_string($_GET['student_id']);
}

$hide_columns = "";

if ($config["debug"] != true) {
	$hide_columns = "				{ 'bVisible': false, 'aTargets': [ 0, 1 ] },\n";
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "

function clear_column(ColumnID)
{
	$('input[name=\'block['+ColumnID+']\' ]').attr(\"checked\", false);
}
	");

function print_coursetype_selects($StudentType)
{
	global $config, $link;
	$sql = "SELECT * FROM StudentTypes";
	$result = mysql_query($sql, $link);

	if (!$result) {  die('Invalid query: ' . mysql_error());  }
	else
	{
		while($row = mysql_fetch_array($result))
		{
			if ($StudentType == $row['id']) {
				echo "         Student Type: ".$row['CourseType']."\n";
				echo "         <input type=\"hidden\" name=\"CourseTypeID\" value=\"".$StudentType."\" />";
			}
		}
	}
	return 0;
}
	
function get_places_left($courseID)
{
	global $config, $link;
	$sql_places = "SELECT * FROM BLOCKS_CourseEnrolment WHERE EnrolmentYear=".$config['current_year']." AND CourseID='".$courseID."' ORDER BY id";
	$result_places = mysql_query($sql_places, $link);

	if (!$result_places) {  die('Invalid query: ' . mysql_error());  }
	else
	{
		return mysql_num_rows($result_places);
	}
	return 0;
}

/** Get a list of all the students current enrolments.
  *
  * There may be none or more.
  *
  * There should only be one per block, but we don't check this currently.
  */
function get_students_current_enrolments($StudentID)
{
	global $config, $link;
	
	$sql_enrolments  = "SELECT * FROM BLOCKS_CourseEnrolment INNER JOIN BLOCKS_Course ON CourseID=BLOCKS_Course.id";
	$sql_enrolments .= " INNER JOIN BLOCKS_CourseDef ON BLOCKS_Course.CourseDefID=BLOCKS_CourseDef.id";
	$sql_enrolments .= " WHERE BLOCKS_CourseEnrolment.StudentID='".$StudentID."'";

	$result_enrolments = mysql_query($sql_enrolments, $link);

	$enrolments = Array();

	if (!$result_enrolments) {  die('Invalid query: ' . mysql_error());  }
	else
	{
		while($row_enrolments = mysql_fetch_array($result_enrolments))
		{
			$enrolments[ $row_enrolments['BlockID'] ] = $row_enrolments['CourseID'];
		}
	}
	return $enrolments;
}

/** Print the html table for the students enrolments.
  *
  * We use get_students_current_enrolments() to get a list of what they have allready.
  * This table contains form elements that should let us submit changes to the enrolments.
  * This form should post the data to blocks_handler.php
  */
function print_blocks_table($StudentID, $StudentType)
{
	global $config, $link;
	
	echo("      <table class='with-borders-horizontal'>\n");
	echo("       <tr>\n");

	$enrolments         = get_students_current_enrolments($StudentID);
	$blocks             = Array();
	$count_records      = 0;
	$i                  = 0;
	$max_rows_in_blocks = 0;
	$table_footer       = Array();
	
	// Get a list of the current blocks from the database. This should be a list like 'a','b','c',etc.
	$sql_blocks    = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType='".$StudentType."' ORDER BY id";
	$result_blocks = mysql_query($sql_blocks, $link);

	if ($result_blocks)
	{
		while($row_blocks = mysql_fetch_array($result_blocks))
		{
		    echo ("<td style='padding: 0px; margin: 0px; padding-bottom: 30px; position: relative;'><table>");
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
					$count_records += 1;

					$blocks[$i][ $row_blocks['Name'] ]  = "<input type=\"radio\" name=\"block[".$row_blocks['id']."]\" id=\"block[".$row_blocks['id']."][".$i."]\" ";
					$blocks[$i][ $row_blocks['Name'] ] .= "value=\"".$row[0]."\"";
					
					// if student is allready enrolled on this course, then mark it as selected.
					if (isset($enrolments[ $row['BlockID'] ]) && $enrolments[ $row['BlockID'] ] == $row[ 0 ]) {
						$blocks[$i][ $row_blocks['Name'] ] .= " checked />\n";
					} else {
						$blocks[$i][ $row_blocks['Name'] ] .= " />\n"; 
					}
					$blocks[$i][ $row_blocks['Name'] ] .= "<label for='block[".$row_blocks['id']."][".$i."]'>\n";
					$blocks[$i][ $row_blocks['Name'] ] .= $row['SubjectName']."\n";

					$blocks[$i][ $row_blocks['Name'] ] .= "<span style='float: right; vertical-align: top; font-size: 0.65em' >";
				    $blocks[$i][ $row_blocks['Name'] ] .= "(".get_places_left($row[0]);
					$blocks[$i][ $row_blocks['Name'] ] .= "/".$row['MaxPupils'].")";
					$blocks[$i][ $row_blocks['Name'] ] .= "</span>\n";
					$blocks[$i][ $row_blocks['Name'] ] .= "</label>\n";
					
					echo("<tr><td style='height: 2.1em'>\n");
					echo($blocks[$i][ $row_blocks['Name'] ]);
					echo("</td></tr>\n");
					
					$i += 1;
					if ($i > $max_rows_in_blocks)
					{
						$max_rows_in_blocks = $i;
					}
				}
				echo("</table>");
				
				echo "<input type='button' onClick=\"clear_column(".$row_blocks['id'].")\" value='Clear Block' style='position: absolute; bottom: 2px; left: 2px;' />";
			} else {  die('Invalid query: ' . mysql_error());  }
		}
	} else {  die('Invalid query: ' . mysql_error());  }
		
	echo("       </tr>\n");
	echo("      </table>");
}

$sql = "SELECT MobileNumber, SequenceNumber, StudentType FROM students WHERE id=\"".$StudentID."\"";
$result = mysql_query($sql, $link);

$row = 0;
if (!$result) {  die('Invalid query: ' . mysql_error());  }
else if (mysql_num_rows($result) == 1)
{
	$i = 0;
	$row = mysql_fetch_array($result);
?>
   <div class='block' >
    <form action="blocks_handler.php" method="post">
     <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>" />
     <div id="dynamic">
      <table class='with-borders-horizontal'>
       <tr>
 <!--       <td>Mobile Number:   <input type='text' value='<?php echo $row['MobileNumber'];   ?>' /></td> -->
        <td>Sequence Number: <input type='text' value='<?php echo $row['SequenceNumber']; ?>' /></td>
        <td>
<?php print_coursetype_selects($row['StudentType']); ?>
        </td>
       </tr>
       <tr>
        <td colspan="6" style="align: center">
<?php print_blocks_table($StudentID, $row['StudentType']); ?>
        </td>
       </tr>
       <tr>
        <td colspan="3" style="align: center">
         <input type="submit" value="Save"/> - <input type="reset" />
        </td>
       </tr>
      </table>
     </div><!-- id=dynamic //-->
    </form>
   </div><!-- class=block //-->
<?php
}

print_footer();
?>  
