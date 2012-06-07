<?php

require_once('config.inc.php');

// Query
$sql_student_types = "SELECT * from StudentTypes";

$result_student_types = mysql_query($sql_student_types, $link);

if (!$result_student_types)
{
  die('Invalid query: ' . mysql_error());
}
else
{
  while($row_student_types = mysql_fetch_array($result_student_types))
  {
    $student_types[$row_student_types['id'] ] = $row_student_types['CourseType'];
  }
}

// Query
$sql = "SELECT * from students WHERE EnrolmentYear=".$config['current_year'];

$result = mysql_query($sql, $link);

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}
else
{
echo("{\n");
echo("  \"sEcho\": 1, ");
echo("  \"iTotalRecords\": \"57\",");
echo("  \"iTotalDisplayRecords\": \"57\",");
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
	echo('    "'.$row['FirstName']."\",\n");
	echo('    "'.$row['LastName']."\",\n");
	echo('    "'.$row['PreviousInstitution']."\",\n");
	echo('    "'.$row['EnrolmentYear']."\",\n");
	if ($student_types[ $row['StudentType'] ] == "")
	{
		echo('    "'.$row['StudentType']."\"\n");
	}
	else
	{
		echo('    "'.$student_types[ $row['StudentType'] ]."\",\n");
	}
	echo("    \"\",\n");
	echo("    \"\"\n");
	echo("    ]");
    //$config[$row['setting']] = $row['value'];
  }
echo("  ]\n");
echo("}\n");
}

?>
