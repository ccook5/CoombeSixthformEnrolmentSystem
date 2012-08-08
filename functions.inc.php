<?php

require_once("config.inc.php");

/** Gets a value from http POST data
  *
  * Return 0 if not found.
  */
function get_post_val($key)
{
	if (isset($_POST[$key]))
	{
		return mysql_real_escape_string($_POST[$key]);
	}
	return 0;
}


function print_edit_table_row($ColumnName)
{
?>
    <tr>
     <td><?php echo $ColumnName; ?></td>
     <td><input type='text' name='echo $ColumnName; ?>' /></td>
    </tr>
<?php
}

/** converts a text student type (from a form) to an id for the database.
  */
function get_studenttype_as_id($StudentType)
{
	global $link, $config;
	$sql    = "SELECT * FROM StudentTypes WHERE CourseType='".$StudentType."' LIMIT 1";
	$result_student_types = mysql_query($sql, $link);

	if (!$result_student_types) {
		print("sql: ".$sql );
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	} else {
		if (mysql_num_rows($result_student_types) < 1) {
			die('Error: Not enough Rows');
		}
		$row_student_type = mysql_fetch_assoc($result_student_types);
		
		if ($row_student_type['CourseType'] == $StudentType) {
			return $row_student_type['id'];
		}
	}
	die("Student ID Not Found");
	return -1;
}

/** deletes all results for a student */
function delete_all_results_for_student($StudentID)
{
	global $config, $link;
	$sql           = "DELETE FROM GCSE_Results WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$config['current_year']."'";
	$result        = mysql_query($sql, $link);

	if (!$result)
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}

/** deleetes course enrolments for a student. */
function delete_all_courseenrolments_for_student($StudentID)
{
	global $config, $link;
	$EnrolmentYear = $config['current_year'];
	$sql           = "DELETE FROM BLOCKS_courseenrolment WHERE StudentID='".$StudentID."' AND EnrolmentYear='".$EnrolmentYear."'";
	$result        = mysql_query($sql, $link);

	if (!$result)
	{
		die('Invalid query: ' . mysql_error()." On line ".__line__);
	}
}
?>

