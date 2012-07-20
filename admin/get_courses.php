<?php

require_once('../config.inc.php');

// Query
$sql_student_types    = "SELECT * from StudentTypes";
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
$sql    = "SELECT * from BLOCKS_CourseDef";
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
		echo('    "'.$row['SubjectName']."\",\n");
		if (!isset($student_types[ $row['Type'] ]) || $student_types[ $row['Type'] ] == "")
		{
			echo('    "'.$row['Type']."\",\n");
		}
		else
		{
			echo('    "'.$student_types[ $row['Type'] ]."\",\n");
		}
		echo("    \"\",\n");
		echo("    \"\"\n");
		echo("    ]");
	}
	echo("  ]\n");
	echo("}\n");
}

?>
