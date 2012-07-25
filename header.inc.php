<?php
function print_header($title = "Coombe Sixth Form Registration", $hide_title_bar = 'false', $script = '', $exclude_datatables_js = false, $meta = "", $extra_script = "")
{
?>
<!DOCTYPE html>
<html>
 <head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title ?></title>
  
  <script type="text/javascript" language="javascript" src="/js/jquery-1.7.2.min.js"></script>
  <!-- this includes jquery widget as well -->
  <script type="text/javascript" language="javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
<?php
	echo $meta;

	if ($exclude_datatables_js == false) {
?>
  <script type="text/javascript" language="javascript" src="/js/jquery.dataTables.js"></script>
  <script type="text/javascript" language="javascript" src="/js/jquery.jeditable.js"></script>
<?php
	}
?>
  <link   type="text/css" rel="stylesheet" href="/css/style.css" />
  <link   type="text/css" rel="stylesheet" href="/css/coombe-metro/jquery-ui-1.8.21.custom.css" />
  <link   type="text/css" rel="stylesheet" href="/css/jquery.ui.all.css" />
  <link   type="text/css" rel="stylesheet" href="/css/enrolment.gcse_results.css" />
  

  <script type="text/javascript" language="javascript" src="/enrolment.gcse_results.js.php" charset="UTF-8" ></script>
<?php 
	if (! empty($extra_script))
	{
		echo("  <script type='text/javascript' language='javascript' src='".$extra_script."' charset='UTF-8' ></script>");
	}
?>
  <script type="text/javascript" charset="utf-8"><?php echo $script; ?></script>

 </head>
 
 <body>
<?php
    if ($hide_title_bar == false) {
      echo "  <div class='header'><h1>".$title."</h1></div>\n";
?>
   <div class="main-menu">
    <a href="/results.php">GCSE Results</a> |
    <a href="/blocks.php">Blocks</a> |
    <a href="/admin/index.php">Admin</a> |
    <a href="/report.php">Reports</a>&nbsp;&nbsp;
   </div>
<?php
    }
}
?>