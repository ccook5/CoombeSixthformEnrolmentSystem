<?php
function print_header($title = "Coombe Sixth Form Registration", $hide_title_bar = 'false', $script = '', $exclude_datatables_js = false)
{
?>
<html>
 <head>
  <title><?php echo $title ?></title>
  <link rel="stylesheet" type="text/css" href="stylesheets/style.css" />
  <script type="text/javascript" language="javascript" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.js"></script>
<?php
	if ($exclude_datatables_js == false) {
?>
  <script type="text/javascript" language="javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/jquery.dataTables.js"></script>
  <script type="text/javascript" language="javascript" src="javascripts/jquery.jeditable.js"></script>
<?php
	}
?>
  <link   type="text/css"        rel="stylesheet"      href="stylesheets/enrolment.gcse_results.css" />
  
  <script type="text/javascript" language="javascript" src="enrolment.gcse_results.js.php" charset="UTF-8" ></script>
  <script type="text/javascript" charset="utf-8"><?php echo $script; ?></script>

 </head>
 
 <body>
<?php
    if ($hide_title_bar == false) {
      echo "  <div class='header'><h1>".$title."</h1></div>";
?>    <div class="main-menu">
	 <a href="blocks.php">Blocks</a> |
	 <a href="results.php">GCSE Results</a>
	</div>
<?php
    }
}
?>