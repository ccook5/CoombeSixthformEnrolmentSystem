<?php
function print_header($title = "Coombe Sixth form registration form.", $hide_title_bar = 'false', $script = '', $exclude_datatables_js = false)
{
?>
<html>
 <head>
  <title><?php echo $title ?></title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script type="text/javascript" language="javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.js"></script>
<?php
	if ($exclude_datatables_js == false) {
?>
  <script type="text/javascript" language="javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/jquery.dataTables.js"></script>
  <script type="text/javascript" language="javascript" src="jquery.jeditable.js"></script>
<?php
	}
?>
  <script type="text/javascript" charset="UTF-8" language="javascript" src="enrolment.gcse_results.js.php"></script>
  <script type="text/javascript" charset="utf-8"><?php echo $script; ?></script>
 </head>
 
 <body>
<?php
    if ($hide_title_bar == false) {
      echo "  <div class='header'><h1>".$title."</h1></div>";
    }
}
?>