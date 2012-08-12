<?php
require_once('../config.inc.php');
require_once('../header.inc.php');
require_once('../footer.inc.php');
require_once('../functions.inc.php');

$Action    = get_post_val('Action');

print_header($title = 'Coombe Sixth Form Enrolment - Blocks', 
				$hide_title_bar        = true, 
				$script                = "", 
				$exclude_datatables_js = true, 
				$meta                  ="");

				
$current = $config['next_enrolee'];

echo $current;
echo $Action;
if ($Action === "Up") {
	$current ++;
} else if ($Action === "Down") {
	$current --;
} else {
	die;
}


echo $current;

$sql  = "UPDATE configuration SET value=".$current." WHERE setting='next_enrolee';";

if (! mysql_query($sql, $link))
{
	die('Invalid query: ' . mysql_error().'. SQL: '.$sql_insert );
}

print_footer();
?>

