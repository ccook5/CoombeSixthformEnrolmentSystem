<?php 
require_once '../config.inc.php';
require_once '../header.inc.php';
require_once '../select_widget.php';

print_header($title = 'Coombe Sixth Form Enrolment - Admin', 
			$hide_title_bar = false, 
			$script = "
	$(document).ready(function() {
		$( '#tabs' ).tabs({
			ajaxOptions: {
				error: function( xhr, status, index, anchor ) {
					$( anchor.hash ).html(
						\"Couldn\'t load this tab. We'll try to fix this as soon as possible. \" +
						\"If this wouldn\'t be a demo.\" );
				}
			}
		});
	} );
", $exclude_datatables_js = false, $meta = "",
			$extra_script="config.js.php");
?>
   <div id="tabs">
    <ul>
<?php
$sql = "SELECT * FROM StudentTypes;";
$result = mysql_query($sql, $link);

$course_types = Array();

if (!$result)
{
  die('Invalid query: ' . mysql_error());
}
else
{
  while($row = mysql_fetch_array($result))
  {
	echo('     <li><a href="block.php?block_id='.($row['id'])."\">\n");
	echo('      '.$row['CourseType']."</a></li>\n");
  }
}
?>
    </ul>
   </div>
 
   <div id="debug" class="debug"></div>
 </body>
</html>