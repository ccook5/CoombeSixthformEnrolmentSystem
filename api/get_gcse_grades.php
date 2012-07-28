<?php

require_once('../config.inc.php');

function get_Qualification($QualificationID)
{
	global $link;
	
	$sql_qual     = "SELECT * FROM GCSE_Qualification WHERE id='".$QualificationID."'";
	$result_qual  = mysql_query($sql_qual, $link);
	$row_qual     = mysql_fetch_array($result_qual);

	return $row_qual['Type'].' - '.$row_qual['Length'];
}
// Query -- id, Grade, Points, QualificationID
$sql    = "SELECT * from gcse_grade";
$result = mysql_query($sql, $link);

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}
else
{
	echo("{\n");
	echo("  \"sEcho\": 1, ");
	echo("  \"iTotalRecords\": \"".mysql_num_rows($result)."\",");
	echo("  \"iTotalDisplayRecords\": \"".mysql_num_rows($result)."\",");
	echo("  \"aaData\": [\n");

	$first_loop = True;
	
  while($row = mysql_fetch_array($result))
  {
    if (! $first_loop) 
	{
		echo(",\n");
	}
	$first_loop = False;
	echo("    [\n");
	echo('    "'.$row['id']."\",\n");
	echo('    "'.$row['Grade']."\",\n");
	echo('    "'.$row['Points']."\",\n");
	echo('    "'.get_qualification($row['QualificationID'])."\",\n");
	echo("    \"\",\n");
	echo("    \"\"\n");
	echo("    ]");
  }
echo("  ]\n");
echo("}\n");
}

?>
