<?php
function print_header($title = "Coombe Sixth form registration form.", $hide_title_bar = 'false', $script = '', $exclude_datatables_js = false)
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

  	<style TYPE="text/css">
       <!--
#accordion {
    list-style: none;
    padding: 0 0 0 0;
    width: 170px;
}
#accordion div {
    display: block;
    background-color: #dddddd;
    font-weight: bold;
    margin: 1px;
    cursor: pointer;
    padding: 5 5 5 7px;
    list-style: circle;
	border: 1px solid #999;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
}
#accordion ul {
    list-style: none;
    padding: 0 0 0 0;
}
#accordion ul{
    display: none;
}
#accordion ul li {
    font-weight: normal;
    cursor: auto;
    background-color: #fff;
    padding: 0 0 0 7px;
}
#accordion a {
    text-decoration: none;
}
#accordion a:hover {
    text-decoration: underline;
}
     -->
      </style>
 </head>
 
 <body>
<?php
    if ($hide_title_bar == false) {
      echo "  <div class='header'><h1>".$title."</h1></div>";
    }
}
?>