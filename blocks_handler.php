<?php

require_once 'config.inc.php';
require_once 'header.inc.php';

$StudentID    = mysql_real_escape_string($_POST['StudentID']);
$CourseTypeID = mysql_real_escape_String($_POST['CourseTypeID']);

/** Get all the current courses for the current block.
  *
  * We build a list for each block, then print by row in a loop 
  * later on in the code, so this isn't inefficient.
  */
function get_courses($CourseTypeID, $CourseID)
{
	global $link, $config;
	$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id";
	$sql   .= " INNER JOIN BLOCKS_CourseTypes ON Type=BLOCKS_CourseTypes.id";
	$sql   .= " WHERE EnrolmentYear=".$config['current_year']." AND =".$row_blocks['id'];
	$result = mysql_query($sql, $link);

	if ($result) {
		$i = 0;
		while($row = mysql_fetch_array($result))
		{
		}
	} else {  die('Invalid query: ' . mysql_error());  }
}

print_header($title = 'Coombe Sixth Form Enrolment - Blocks', $hide_title_bar = true, $script = "");

echo "&dollar;_POST: ";
print_r($_POST);

echo  "CourseTypeID: ".$CourseTypeID."\n--\n";

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
	
		print("row:");
		print_r($row);

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
		
//  list all the new enrolments

 		echo "Current Enrolments:\n";
		print_r($current_enrolments);

		echo "New enrolments:\n";
		if (isset($_POST['block'][ $row['id'] ])) {
			print_r($_POST['block'][ $row['id'] ]);
//  if new == none, delete all current enrolments
//  if more than one current, error!
			if (count($current_enrolments) > 1)
			{
				print("<div class='error'>Error: more than one entry for this block.");
			} else if (count($current_enrolments) < 1) {
//  if new == current, then do nothing
				if ($_POST['block'][ $row['id'] ] == $current_enrolments[0]['CourseID'])
				{
					print("CourseID Match. Not doing anything.\n");
				}
//  if new != current, then update
				else if ($_POST['block'][ $row['id'] ] == $current_enrolments[0]['CourseID'])
				{
					print(" Update: ".$_POST['block'][ $row['id'] ]."=".$current_enrolments[0]['CourseID']."\n");
				}
			}
//  else insert.
			else
			{
				print("Insert: ".$_POST['block'][ $row['id'] ]."=".$current_enrolments[0]['CourseID']."\n");
			}
		} else {
			print("No new enrolments for this block\n");
		}

// we should make null an option? then if block type requires an option, it can be an error if we try to enrol as none.
		print("\n------------------------------------------------\n\n");
	}
} else { print 2;  die('Invalid query: ' . mysql_error());  }

//select * from blocks_courseenrolment Join blocks_course where BlockId = key and enrolyear= this year
//if enrolment != new one, then delete it
//if there is more than 1 delete them all.
//insert into blocks_courseenrolemnt CourseID (value), StudentID, EnrolmentYear
?>

