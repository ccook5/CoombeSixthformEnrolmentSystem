<?php

require_once('config.inc.php');

include      'header.inc.php';

if (! isset($_GET['student_id'])) {
	echo "<div class='error'>No student Id found</div>";
	die;
} else {
	$StudentID = mysql_real_escape_string($_GET['student_id']);
}

$hide_columns = "";

if ($config["debug"] != true) {
	$hide_columns = "				{ 'bVisible': false, 'aTargets': [ 0, 1 ] },\n";
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "
var StudentID = '".$StudentID."';

$(document).ready( function() {
	
	var ResultsTable = $('#results').dataTable( {
		'bProcessing': true,
		'sAjaxSource': 'get_results.php?StudentID=".$StudentID."',
		'sScrollY'   : '200px',
		'bFilter'    : false,
		'bPaginate'  : false,
		'aoColumnDefs': [".$hide_columns."
// Center the first 2 (id + student id), grade(third last) and last two columns (edit/delete buttons)
			{ 'sClass'  : 'center', 'aTargets': [ 0, 1, -1, -2, -3 ] },
			
// Minimise the width of the first two and last two columns (edit/delete buttons)
			{ 'sWidth'  : '5%', 'aTargets': [ 0, 1, -1, -2] }
		]
	} );

	students_results(ResultsTable);
	
	/* Add a click handler to the rows - this could be used as a callback */
	$('#students tbody').click( function( event ) {
		$('#students_results').attr('src','/students_results.php');
		
		$(studentTable.fnSettings().aoData).each(function (){
			$(this.nTr).removeClass('row_selected');
		});
		$(event.target.parentNode).addClass('row_selected');
		
		$('#students_results').attr('src','/students_results.php?student_id='+$(event.target.parentNode).find('td:first').html());
		$('#average_results').attr('src','/enrolment.students.average_results.php?StudentID='+$(event.target.parentNode).find('td:first').html());
	} );
} );
	");
//
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
			echo "         <label><input type=\"radio\" name=\"CourseTypeID\" id=\"CourseTypeID\"";
			if ($StudentType == $row['id']) {
				echo " checked ";
			}
			echo " value=\"".$row['id']."\"/>".$row['CourseType']."</label>&nbsp;&nbsp;&nbsp;\n";
		}
	}
	return 0;
}
	
function get_places_left($courseID)
{
	echo $courseID;
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
	$sql_enrolments .= " INNER JOIN BLOCKS_CourseDef ON BLOCKS_Course.CourseDefID=BLOCKS_CourseDef.id WHERE BLOCKS_CourseEnrolment.StudentID='".$StudentID."'";

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
function print_blocks_table($StudentID)
{
	global $config, $link;
	
	echo("      <table class='with-borders-horizontal'>\n");
	echo("       <tr>\n");

	$enrolments    = get_students_current_enrolments($StudentID);
	$blocks        = Array();
	$count_records = 0;
	$i             = 0;
	
	// Get a list of the current blocks from the database. This should be a list like 'a','b','c',etc.
	$sql_blocks    = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType=0 ORDER BY id";
	$result_blocks = mysql_query($sql_blocks, $link);

	if ($result_blocks)
	{
		while($row_blocks = mysql_fetch_array($result_blocks))
		{
			echo ("      <th>".$row_blocks['Name']."</th>");
			
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

					$blocks[$i][ $row_blocks['Name'] ]  = "<input type=\"radio\" name=\"block[".$row_blocks['id']."]\" id=\"block[".$row_blocks['id']."]\" ";
					$blocks[$i][ $row_blocks['Name'] ] .= "value=\"".$row[0]."\"";
					
					// if student is allready enrolled on this course, then mark it as selected.
					if (isset($enrolments[ $row['BlockID'] ]) && $enrolments[ $row['BlockID'] ] == $row[ 0 ]) {
						$blocks[$i][ $row_blocks['Name'] ] .= " checked />";
					} else {
						$blocks[$i][ $row_blocks['Name'] ] .= " />";
					}
					$blocks[$i][ $row_blocks['Name'] ] .= "<label for='block[".$row_blocks['id']."]'>";
					$blocks[$i][ $row_blocks['Name'] ] .= $row['SubjectName'];
				  /*$blocks[$i][ $row_blocks['Name'] ] .= " (".get_places_left($row['id'])."&nbsp;of&nbsp;".$row['id'].$row['MaxPupils'].")";*/
					$blocks[$i][ $row_blocks['Name'] ] .= "</label>";
					
					$i += 1;
				}
			} else {  die('Invalid query: ' . mysql_error());  }
		}
	} else {  die('Invalid query: ' . mysql_error());  }
	
	echo("       </tr>\n");
	echo("       <tr>\n");

	foreach($blocks as $r)
	{	
		echo ("      <tr>\n");
		foreach($r as $b)
		{
			echo("        <td>".$b."</td>\n");
			$i += 1;
		}
		echo("      </tr>\n");
	}
	echo("       </tr>\n");
	echo("      </table>");
}
?>
   <div class='block' >
    <form action="blocks_handler.php" method="post">
     <input type="hidden" name="StudentID" value="<?php echo $StudentID; ?>" />
     <div id="dynamic">
      <table class='with-borders-horizontal'>
       <tr>
<?php
	$sql = "SELECT MobileNumber, SequenceNumber, StudentType FROM students WHERE id=\"".$StudentID."\"";
	$result = mysql_query($sql, $link);

	$row = 0;
	if (!$result) {  die('Invalid query: ' . mysql_error());  }
	else
	{
		$i = 0;
		$row = mysql_fetch_array($result);
	}
?>
        <td>Mobile Number: <input type='text' value='<?php echo $row['MobileNumber']; ?>'/></td>
        <td>Sequence Number: <input type='text' value='<?php echo $row['SequenceNumber']; ?>'/></td>

        <td>
<?php print_coursetype_selects($row['StudentType']); ?>
        </td>
       </tr>
       <tr>
        <td colspan="6" style="align: center">
<?php
print_blocks_table($StudentID);
?>
        </td>
       </tr>
       <tr>
        <td colspan="3" style="align: center">
         <input type="submit" /> - <input type="reset" />
        </td>
       </tr>
      </table>
     </div><!-- id=dynamic //-->
    </form>
   </div><!-- class=block //-->
  
 </body>
</html>