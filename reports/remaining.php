<?php

require_once('config.inc.php');
require_once('header.inc.php');
require_once('footer.inc.php');

print_header($title = 'Coombe Sixth Form Enrolment', $hide_title_bar = false, $script = "");

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

/** Print the html table for the students enrolments.
  *
  */
function print_blocks_table($StudentType)
{
	global $config, $link;

	echo("      <table class='with-borders-horizontal'>\n");
	echo("       <tr>\n");

	$blocks             = Array();
	$i                  = 0;
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
					$html = $row['SubjectName']."\n";

					$html .= "<span style='float: right; vertical-align: top; font-size: 0.65em' >";
					$html .= "(".get_places_left($row[0]);
					$html .= "/".$row['MaxPupils'].")";
					$html .= "</span>\n";

					$blocks[$i][ $row_blocks['Name'] ] = $html;

					echo("<tr><td style='height: 2.1em'>\n");
					echo($blocks[$i][ $row_blocks['Name'] ]);
					echo("</td></tr>\n");

					$i += 1;
				}
				echo("</table>");

			} else {  die('Invalid query: ' . mysql_error());  }
		}
	} else {  die('Invalid query: ' . mysql_error());  }

	echo("       </tr>\n");
	echo("      </table>");
}

// sql query - get a list of student types
$sql = "SELECT * FROM StudentTypes";
$result = mysql_query($sql, $link);

if (!$result) {  die('Invalid query: ' . mysql_error());  }

while ($row = mysql_fetch_array($result)) {
	print("  <h1>".$row['CourseType']."</h1>");
?>
   <div class='block' >
     <div id="dynamic">
      <table class='with-borders-horizontal'>
       <tr>
        <td colspan="6" style="align: center">
<?php print_blocks_table($row['CourseType']); ?>
        </td>
       </tr>
      </table>
     </div><!-- id=dynamic //-->
   </div><!-- class=block //-->
<?php
}

print_footer();
?>  
