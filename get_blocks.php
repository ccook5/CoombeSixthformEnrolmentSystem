<?php

require_once('config.inc.php');
$count_records = 0;
$i = 0;
// Query
$sql_blocks = "SELECT * FROM BLOCKS_Blocks WHERE Year=".$config['current_year']." AND CourseType=1 ORDER BY id";
$result_blocks = mysql_query($sql_blocks, $link);

if (!$result_blocks) {  die('Invalid query: ' . mysql_error());  }
else
{
	while($row_blocks = mysql_fetch_array($result_blocks))
	{

		// Query
		$sql    = "SELECT * FROM BLOCKS_Course INNER JOIN BLOCKS_CourseDef ON CourseDefID=BLOCKS_CourseDef.id INNER JOIN BLOCKS_CourseTypes ON Type=BLOCKS_CourseTypes.id WHERE EnrolmentYear=".$config['current_year']." AND BlockID=".$row_blocks['id'];
		$result = mysql_query($sql, $link);

		if (!$result) {  die('Invalid query: ' . mysql_error());  }
		else
		{
			$i = 0;
			while($row = mysql_fetch_array($result))
			{
				$count_records += 1;
				
				$blocks[ $row_blocks['Name'] ][$i] = $row['SubjectName']. $row['MaxPupils']. $row['id'];
				
				$i += 1;
			}
		}
	}
}

echo("{\n");
echo("  \"sEcho\": 1, \n");
echo("  \"iTotalRecords\": \"".$count_records."\",\n");
echo("  \"iTotalDisplayRecords\": \"".$count_records."\",\n");
echo("  \"aaData\":\n  [\n");

$first_loop = True;

foreach($blocks as $r)
{
    if (! $first_loop) 
	{
		echo(",\n");
	}
	$first_loop = False;
	
	echo ("     [\n");
	$first_loop2 = true;
	foreach($r as $b) {
		if (! $first_loop2) 
		{
			echo(",\n");
		}
		$first_loop2 = False;

		echo('        "'.$b."\"");
		$i += 1;
	}
	echo("\n     ]");
}
echo("\n  ]\n");
echo("}\n");

?>
