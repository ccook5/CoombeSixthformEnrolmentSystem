<?php

require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');

if (! isset($_GET['block_id'])) {
	die( "<div class='error'>No block Id found</div>" );
} else if ($_GET['block_id'] == "null") {
	die;
} else {
	$block_id = mysql_real_escape_string($_GET['block_id']);
}

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = true, $script = "
	function add_to_block(event, block_id)
	{
		var block = event.target.parentNode.firstChild;
		var courseID = $( 'select#course' )[0].selectedOptions[0].value;
		var courseName = $( 'select#course' )[0].selectedOptions[0].innerHTML;
		
		alert('add to block,'+block_id+', '+course);
		
		var html = '<tr><td style=\'height: 2.1em\'>';
		html += courseName;
		html += '<input type=\'button\' style=\'float: right;\' onClick=\'remove_course(event, '+courseID+', '+');\' value=\'X\' /></td></tr>';
		var elem = $(html);
		elem.appendTo(block);
	}
	
	function remove_course(event, block_id, course_name, course_id)
	{
		alert('remove'+block_id+', '+course_name+', '+course_id);
		
		var request = $.ajax({
			url: 'ajax_update_block.php',
			type: 'POST',
			data: { 
				action             : 'delete',
				id                 : course_id
			},
			dataType: 'html'
		} );

		request.done(function( msg ) {
			$('#debug').html( msg );
			alert('success:'+msg);
			$('#course_'+course_id).remove();
		} );

		request.fail(function(jqXHR, textStatus) {
			alert( 'Request failed: ' + textStatus );
		} )
	}
	
");

/** Print the html table for the students enrolments.
  *
  * We use get_students_current_enrolments() to get a list of what they have allready.
  * This table contains form elements that should let us submit changes to the enrolments.
  * This form should post the data to blocks_handler.php
  */

echo("      <div class='block'><table class='with-borders-horizontal'>\n");
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
				$blocks[$i][ $row_blocks['Name'] ] = $row['SubjectName']."\n";
				$blocks[$i][ $row_blocks['Name'] ] .= "<input type='button' style='float: right;' onClick='remove_course(event, ".$row_blocks['id'].", \"".$row["SubjectName"]."\", ".$row[0].");' value='X'\n";
				$blocks[$i][ $row_blocks['Name'] ] .= "/>\n";
				
				echo("<tr><td style='height: 2.1em' id='course_".$row[0]."'>\n");
				echo($blocks[$i][ $row_blocks['Name'] ]);
				echo("</td></tr>\n");
				
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
$sql    = "SELECT * FROM BLOCKS_CourseDef WHERE Type='".$block_id."' ORDER BY id";
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
	echo("       </td></tr>\n");
	echo("      </table></div>");


?>