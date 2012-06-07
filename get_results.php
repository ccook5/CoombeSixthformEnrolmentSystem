<?php
require_once('config.inc.php');

if (! isset($_GET['student_id'])) {
  echo "<div class='error'>No student Id found</div>";
  die;
} else {
  $student_id = mysql_real_escape_string($_GET['student_id']);
}

$sql    = "SELECT * from GCSE_Results JOIN GCSE_Grade ON GCSE_Results.GradeID=GCSE_Grade.id JOIN GCSE_Qualification ON GCSE_Grade.QualificationID=GCSE_Qualification.id WHERE StudentID='".$student_id."'";
$result = mysql_query($sql, $link);

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
	echo('    "'.$row['Type'].' - '.$row['Length']        ."\",\n");
	$sql_subject    = "SELECT * from GCSE_Subjects WHERE id='".$row['SubjectID']." LIMIT 1'";
	$result_subject = mysql_query($sql_subject, $link);
	$row_subject    = mysql_fetch_array($result_subject); 
	echo('    "'.$row_subject['Name']."\",\n");
	echo('    "'.$row['Grade'].' {'.$row['Points']       ." points}\",\n");
	
	echo("    \"\",\n");
	echo("    \"\"\n");
	echo("    ]");
  }
  echo("  ]\n");
  echo("}\n");
}

?>
