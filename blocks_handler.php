<?php

require_once 'config.inc.php';
include 'header.inc.php';

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

echo  "CourseTypeID: ".$CourseTypeID."\n";

// Select everything from blocks where coursetype matches. 
$sql    = "SELECT * FROM BLOCKS_Blocks WHERE CourseType='".$CourseTypeID."' AND Year='".$config['current_year']."'";
$result = mysql_query($sql, $link);

if ($result) {

	$i = 0;

	// for each block:
	while($row = mysql_fetch_array($result))
	{
		print("Blocks: ");
		print_r($row);
		
		$current_enrolments = Array();
		$new_enrolments     = Array();
		print("row:");
		print_r($row);
//	list all the current enrolments
		$sql_enrolments  = "SELECT * FROM BLOCKS_CourseEnrolment";
		$sql_enrolments .= " INNER JOIN BLOCKS_Course ON CourseID=BLOCKS_Course.id";
		$sql_enrolments .= " WHERE BlockID='".$row['id']."'";
//		$sql_enrolments .= " AND BLOCKS_CourseEnrolment.EnrolmentYear='".$config['current_year']."'";
		$result_enrolments = mysql_query($sql_enrolments, $link);

		if ($result_enrolments) {
	print 1;
			$i = 0;

			// for each block:
			while($row_enrolments = mysql_fetch_array($result_enrolments))
			{
	print 3;
				$current_enrolments[] = $row_enrolments;
			}
		} else {  die('Invalid query: ' . mysql_error());  }
		
//  list all the new enrolments

		echo "Current Enrolments:";
		print_r($current_enrolments);

		echo "New enrolments:";
		print_r($_POST['block']);

//		foreach () {
//		}
		
//  if new == none, delete all current enrolments
//  if more than one current, error!
//  if new != current, then update
//  else insert.

// we should make null an option? then if block type requires an option, it can be an error if we try to enrol as none.
	}
} else { print 2;  die('Invalid query: ' . mysql_error());  }


//select * from blocks_courseenrolment Join blocks_course where BlockId = key and enrolyear= this year

//if enrolment != new one, then delete it

//if there is more than 1 delete them all.

//insert into blocks_courseenrolemnt CourseID (value), StudentID, EnrolmentYear
?>

