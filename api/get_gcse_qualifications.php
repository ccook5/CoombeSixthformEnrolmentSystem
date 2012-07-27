<?php

require_once('../config.inc.php');

// Query
$sql    = "SELECT * from gcse_qualification";
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
	echo('    "'.$row['Type']."\",\n");
	echo('    "'.$row['Length']."\",\n");
	echo('    "'.$row['EquivalentGCSE']."\",\n");
	echo('    "'.$row['Notes']."\",\n");
	echo("    \"\",\n");
	echo("    \"\"\n");
	echo("    ]");
  }
echo("  ]\n");
echo("}\n");
}

?>
