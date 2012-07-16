<?php
require_once("connect.inc.php");

// Query
$sql_config = "SELECT * from configuration";

$result_config = mysql_query($sql_config, $link);

if (!$result_config)
{
  die('Invalid query: ' . mysql_error());
}
else
{
  while($row_config = mysql_fetch_array($result_config))
  {
    $config[$row_config['setting']] = $row_config['value'];
  }
}

?>