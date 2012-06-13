<?php
require_once('config.inc.php');

if (! isset($_GET['student_id'])) {
  echo "<div class='error'>No student Id found</div>";
  die;
} else {
  $student_id = mysql_real_escape_string($_GET['student_id']);
}

$sql = "SELECT * FROM GCSE_Results WHERE StudentID='".$student_id."'";
//$sql    = "SELECT * from GCSE_Results 
//			JOIN GCSE_Grade ON GCSE_Results.GradeID=GCSE_Grade.id 
//			JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id 
//			WHERE StudentID='".$student_id."'";

$result = mysql_query($sql, $link);

function get_subject($SubjectID)
{
	global $link;
	$sql    = "SELECT * from GCSE_Subjects WHERE id='".$SubjectID."' LIMIT 1";
	$result = mysql_query($sql, $link);
	$row    = mysql_fetch_array($result); 
	return $row['Name'];
}

function get_grade($GradeID)
{
	global $link;
	$sql    = "SELECT * from GCSE_Grade WHERE id='".$GradeID."' LIMIT 1";
	$result = mysql_query($sql, $link);
	$row    = mysql_fetch_array($result); 
	return $row['Grade']/*.' - '.$row['Points']       ." points."*/;
}

function get_Qualification($GradeID)
{
	global $link;
	$sql_grade    = "SELECT id, QualificationID from GCSE_Grade WHERE id='".$GradeID."' LIMIT 1";
	$result_grade = mysql_query($sql_grade, $link);
	$row_grade    = mysql_fetch_array($result_grade);
	
	$sql_qual     = "SELECT * FROM GCSE_Qualification WHERE id='".$row_grade['QualificationID']."'";
	$result_qual  = mysql_query($sql_qual, $link);
	$row_qual     = mysql_fetch_array($result_qual);

	return $row_qual['Type'].' - '.$row_qual['Length'];
}

if (!$result) {
	die('Invalid query: ' . mysql_error());
}
else {
	echo("{\n");
	echo("  \"sEcho\": 1, ");
	echo("  \"iTotalRecords\": \"".mysql_num_rows($result)."\",");
	echo("  \"iTotalDisplayRecords\": \"".mysql_num_rows($result)."\",");
	echo("  \"aaData\": [\n");

	$first_loop = True;
	while($row = mysql_fetch_array($result)) {
		if (! $first_loop) {
			echo(",\n");
		}
		$first_loop = False;
		echo("    [\n");
		echo('    "'.$row[0]."\",\n");
		echo('    "'.get_qualification($row['GradeID'])."\",\n");
		//subject
		echo('    "'.get_subject($row['SubjectID'])."\",\n");
		echo('    "'.get_grade($row['GradeID'])."\",\n");
		
		echo("    \"\",\n");
		echo("    \"\"\n");
		echo("    ]");
	}
	echo("  ]\n");
	echo("}\n");
}

?>
