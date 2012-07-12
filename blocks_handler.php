<?php

require_once 'config.inc.php';
require_once 'header.inc.php';

$StudentID    = mysql_real_escape_string($_POST['StudentID']);
$CourseTypeID = mysql_real_escape_String($_POST['CourseTypeID']);

echo $StudentID;

/** Get all the current courses for the current block.
  *
  * We build a list for each block, then print by row in a loop 
  * later on in the code, so this isn't inefficient.
  *//*
function get_courses($CourseTypeID, $CourseID)
{
	global $link, $config;
	$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id";
	$sql   .= " INNER JOIN BLOCKS_CourseTypes ON Type=BLOCKS_CourseTypes.id";
	$sql   .= " WHERE EnrolmentYear='".$config['current_year']."'";
	$result = mysql_query($sql, $link);

	if ($result) {
		$i = 0;
		while($row = mysql_fetch_array($result))
		{
		}
	} else {  die('Invalid query: ' . mysql_error());  }
}
*/
print_header($title = 'Coombe Sixth Form Enrolment - Blocks', 
				$hide_title_bar        = true, 
				$script                = "", 
				$exclude_datatables_js = false, 
				$meta                  ="      <meta http-equiv='refresh' content='10;url=/students_blocks.php?student_id=".$StudentID."'/>");

echo "&dollar;_POST: ";
print("<div class='debug'><pre>");
//print_r($_POST);
print("</pre></div><!-- end debug -->");

//echo  "CourseTypeID: ".$CourseTypeID."<br />\n--<br />\n";

// Select everything from blocks where coursetype matches. 
$sql    = "SELECT * FROM BLOCKS_Blocks WHERE CourseType='".$CourseTypeID."' AND Year='".$config['current_year']."' ORDER BY id";
$result = mysql_query($sql, $link);

if ($result)
{
	$i = 0;
	
	// for each block:
	while($row = mysql_fetch_array($result))
	{
		$current_enrolments = Array();
		$new_enrolments     = Array();

// List all the current enrolments for this block
		$sql_enrolments  = "SELECT * FROM BLOCKS_CourseEnrolment";
		$sql_enrolments .= " INNER JOIN BLOCKS_Course ON CourseID=BLOCKS_Course.id";
		$sql_enrolments .= " WHERE BlockID='".$row['id']."'";
//		$sql_enrolments .= " AND BLOCKS_CourseEnrolment.EnrolmentYear='".$config['current_year']."'";
		$result_enrolments = mysql_query($sql_enrolments, $link);

		if ($result_enrolments)
		{
			$i = 0;

			// for each enrolment in current block:
			while($row_enrolments = mysql_fetch_array($result_enrolments))
			{
				$current_enrolments[] = $row_enrolments;
			}
		} else {  die('Invalid query: ' . mysql_error());  }

 		echo "Current Enrolments:\n";
		print_r($current_enrolments);

		echo "New enrolments:\n";
		print_r($_POST);

		if (isset($_POST['block'][ $row['id'] ]))
		{
//			print_r($_POST['block'][ $row['id'] ]);
			
//  if new == none, delete all current enrolments
//  if more than one current, error!
			if (count($current_enrolments) > 1)
			{
				print("<div class='error'>Error: more than one entry for this block.");
			}
			else if (count($current_enrolments) == 1)
			{
//  if new == current, then do nothing
				if ($_POST['block'][ $row['id'] ] == $current_enrolments[0]['CourseID'])
				{
					print("CourseID Match. Not doing anything.<br />\n");
				}
//  if new != current, then update
				else if ($_POST['block'][ $row['id'] ] != $current_enrolments[0]['CourseID'])
				{
					$sql_update  = " UPDATE BLOCKS_CourseEnrolment SET ";
					$sql_update .= "CourseID='".$_POST['block'][ $row['id'] ]."', ";
					$sql_update .= "StudentID='".$StudentID."', ";
					$sql_update .= "EnrolmentYear='".$config['current_year']."' ";
					// second index 0 in $current_enrolments below is the row id from the database. it doesn't have a string index.
					$sql_update .= "WHERE id='".$current_enrolments[0][0]."'\n";
//					print($sql_update."<br />\n");
				
					$result_update = mysql_query($sql_update, $link);

					if ($result_update)
					{
						print("done\n");
					} else {  die('Invalid query: ' . mysql_error());  }
					}
			}
//  else insert.
			else
			{
				$sql_insert = "INSERT INTO BLOCKS_CourseEnrolment (CourseID, StudentID, EnrolmentYear) VALUES ('".$_POST['block'][ $row['id'] ]."', '".$StudentID."', '".$config['current_year']."')";
//				print($sql_insert."\n");
				
				$result_insert = mysql_query($sql_insert, $link);

				if ($result_insert)
				{
					print("done\n");
				} else {  die('Invalid query: ' . mysql_error());  }

			}
		} else {
//			print("No new enrolments for this block, We should delete any current ones...\n");
			
			if (count($current_enrolments) >= 1)
			{
//				print_r($current_enrolments);
				$sql_delete = "DELETE FROM BLOCKS_CourseEnrolment WHERE id='".$current_enrolments[0][0]."' AND StudentID='".$StudentID."'";
//				print($sql_delete);
				$result_delete = mysql_query($sql_delete, $link);

				if ($result_delete)
				{
					print("done\n");
				} else {  die('Invalid query: ' . mysql_error());  }
			}
		}

// we should make null an option? then if block type requires an option, it can be an error if we try to enrol as none.
//		print("<br />\n------------------------------------------------<br /><br />\n\n");
	}
} else { print 2;  die('Invalid query: ' . mysql_error());  }

?>

